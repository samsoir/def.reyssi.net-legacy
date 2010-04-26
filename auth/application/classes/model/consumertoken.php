<?php defined('SYSPATH') or die('No direct script access.');

class Model_ConsumerToken extends Sprig
{
	/**
	 * The life of a token
	 *
	 * @param integer
	 */
	public function $token_life = 1800; // about 30 minutes

	/**
	 * Initialise the model
	 *
	 * @return void
	 * @access public
	 */
	protected function _init()
	{
		$this->_fields += array(
			'id' => new Sprig_Field_Auto,
			'user' => new Sprig_Field_BelongsTo(array(
				'model'  => 'User',
			)),
			'token' => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'max_length' => 200,
				'unique'     => TRUE,
			)),
			'expires' => new Sprig_Field_Timestamp,
		);
	}

	/**
	 * Overloads the create method to
	 * add the correct expiry
	 *
	 * @return void
	 * @author Sam de Freyssinet
	 */
	public function create()
	{
		$this->expires = time() + $this->token_life;
		return parent::create();
	}
}