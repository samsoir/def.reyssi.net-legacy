<?php defined('SYSPATH') or die('No direct script access.');

class Model_Role extends Sprig {

	protected function _init()
	{
		$this->_fields = array(
			'id'        => new Sprig_Field_Auto,
			'role'      => new Sprig_Field_Char(array(
				'empty'     => FALSE,
				'unique'    => TRUE,
				'rules'     => array(
					'max_length'    => 50,
				),
			)),
			'description' => new Sprig_Field_Text,
			'users'     => new Sprig_Field_ManyToMany(array(
				'Model'     => 'User',
			)),
			'rights'    => new Sprig_Field_ManyToMany(array(
				'Model'     => 'Right',
			)),
		);
	}
}