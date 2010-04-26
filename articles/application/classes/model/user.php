<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Sprig {

	protected $_kuaas;

	protected function _init()
	{
		$this->_fields = array(
			'id'       => new Sprig_Field_Auto,
			'name'     => new Sprig_Field_Char(array(
				'empty'    => FALSE,
				'rules'    => array(
					'max_length'    => array(50),
				),
			)),
			'password' => new Sprig_Field_KuaasPassword(array(
				'empty'    => FALSE,
				'rules'    => array(
					'min_length'    => array(6),
					'max_length'    => array(50),
				),
			)),
			'password_confirm' => new Sprig_Field_KuaasPassword(array(
				'empty'    => TRUE,
				'in_db'    => FALSE,
				'rules'    => array(
					'matches' => array('password'),
				),
			)),
			'email'    => new Sprig_Field_Email(array(
				'empty'    => FALSE,
				'unique'   => TRUE,
			)),
			'website'  => new Sprig_Field_Char(array(
				'rules'    => array(
					'max_length'    => array(100),
					'url'           => array(),
				),
			)),
			'created'  => new Sprig_Field_Timestamp(array(
				'empty'    => FALSE,
			)),
			'articles' => new Sprig_Field_HasMany(array(
				'Model'    => 'Article',
			)),
			'comments' => new Sprig_Field_HasMany(array(
				'Model'    => 'Comment'
			)),
			'roles'    => new Sprig_Field_ManyToMany(array(
				'Model'    => 'Role',
			)),
		);

		$this->_kuaas = Kuaas::instance();
	}

	/**
	 * Overload the __set() magic method to
	 * pre-hash the password value
	 *
	 * @param string $key 
	 * @param string $value 
	 * @return void
	 * @author Sam de Freyssinet
	 */
	public function __set($key, $value)
	{
		if ( ! empty($value) and in_array($key, array('password', 'password_confirm')) and $this->state() !== 'new')
		{

			if ($key === 'password')
			{
				$value = $this->_kuaas->hash_password($value);
			}
			elseif ($key === 'password_confirm')
			{
				$salt = $this->_kuaas->find_salt($this->password);
				$value = $this->_kuaas->hash_password($value, $salt);
			}
		}
		parent::__set($key, $value);
	}

	/**
	 * Checks whether a user has a role or not
	 *
	 * @param   string   role 
	 * @return  boolean
	 */
	public function has_role($role)
	{
		static $roles;

		empty($roles) and $roles = $this->roles;

		if ($role instanceof Model_Role)
			$role = $role->role;

		// Reset roles iterator
		$roles->rewind();

		foreach ($roles as $_role)
		{
			if ($_role->role === $role)
				return TRUE;
		}

		return FALSE;
	}
}