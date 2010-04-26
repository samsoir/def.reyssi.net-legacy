<?php defined('SYSPATH') or die('No direct script access.');

class Model_Resource extends Sprig {

	protected function _init()
	{
		$this->_fields = array(
			'id'       => new Sprig_Field_Auto,
			'resource' => new Sprig_Field_Char(array(
				'empty'    => FALSE,
				'unique'   => TRUE,
				'rules'    => array(
					'max_length'     => 100,
				),
			)),
			'description' => new Sprig_Field_Text,
			'rights'   => new Sprig_Field_HasMany(array(
				'Model'    => 'Right',
			)),
		);
	}
}