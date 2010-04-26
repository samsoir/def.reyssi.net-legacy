<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Sprig
{
	protected $_title_key = 'username';

	protected $_sorting = array('username' => 'asc');

	/**
	 * Number of iterations to create hash from user
	 * name information
	 *
	 * @var integer
	 */
	protected $_consumer_secret_gen_length = 15;

	/**
	 * The algorithm to use to create the nonce hash
	 *
	 * @var string
	 */
	protected $_consumer_secret_algo = 'sha256';

	/**
	 * Initialise the model
	 *
	 * @return void
	 * @access public
	 */
	protected function _init()
	{
		$this->_fields += array(
			'id'               => new Sprig_Field_Auto,
			'username'         => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'unique' => TRUE,
				'rules'  => array(
					'regex' => array('/^[\pL_.-]+$/ui')
				),
			)),
			'password'         => new Sprig_Field_Password(array(
				'empty' => FALSE,
			)),
			'password_confirm' => new Sprig_Field_Password(array(
				'empty' => TRUE,
				'in_db' => FALSE,
				'rules' => array(
					'matches' => array('password'),
				),
			)),
			'consumer_secret' => new Sprig_Field_Char(array(
				'max_length' => 250,
				'editable'   => FALSE,
				'unique'     => TRUE,
			)),
			'timestamp'       => new Sprig_Field_Timestamp(array(
				'empty' => TRUE,
				'auto_now_create' => TRUE,
			)),
			'user_attributes' => new Sprig_Field_HasMany(array(
				'model'   => 'UserAttribute',
			)),
			'roles'           => new Sprig_Field_ManyToMany(array(
				'model'   => 'Role'
			)),
		);
	}

	/**
	 * Overload the create method to allow for
	 * nonce generation
	 *
	 * @return self
	 * @access public
	 */
	public function create()
	{
		$this->nonce = $this->create_nonce();
		return parent::create();
	}

	public function authenticate(Validate $array, $field)
	{
		
	}

	public function authorise()
	{
		
	}

	/**
	 * Creates a consumer secret based on a random
	 * hash of the username
	 *
	 * @return string
	 * @access public
	 */
	protected function create_nonce()
	{
		$username = $this->username;
		$length = strlen($username);
		$consumer_secret_parts = array();

		do
		{
			$consumer_secret_parts[] = $username[mt_rand(0, $length-1)];
		}
		while (count($consumer_secret_parts) <= $this->consumer_secret_gen_length);

		$consumer_secret = implode($consumer_secret_parts);
		return hash($this->_consumer_secret_algo, $consumer_secret);
	}
}