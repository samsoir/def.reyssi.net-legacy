<?php defined('SYSPATH') or die('No direct script access.');

class Model_Nonce extends Sprig
{
	/**
	 * Initialise the model
	 *
	 * @return void
	 * @access public
	 */
	protected function _init()
	{
		$this->_fields += array(
			'id'                => new Sprig_Field_Auto,
			'nonce'             => new Sprig_Field_Char(array(
				'empty'  => FALSE,
				'max_length' => 250,
				'unique'     => TRUE,
			)),
			'timestamp'         => new Sprig_Field_Timestamp(array(
				'auto_now_create' => TRUE,
			)),
		);
	}
}