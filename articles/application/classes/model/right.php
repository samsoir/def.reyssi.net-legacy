<?php defined('SYSPATH') or die('No direct script access.');

class Model_Right extends Sprig {

	protected function _init()
	{
		$this->_fields = array(
			'id'        => new Sprig_Field_Auto,
			'right'      => new Sprig_Field_Char(array(
				'empty'     => FALSE,
				'unique'    => TRUE,
				'rules'     => array(
					'max_length'    => 50,
				),
			)),
			'description' => new Sprig_Field_Text,
			'create'    => new Sprig_Field_Boolean,
			'read'      => new Sprig_Field_Boolean,
			'update'    => new Sprig_Field_Boolean,
			'delete'    => new Sprig_Field_Boolean,
			'roles'     => new Sprig_Field_ManyToMany(array(
				'Model'     => 'Role',
			)),
			'resource'  => new Sprig_Field_BelongsTo(array(
				'Model'     => 'Resource',
			)),
		);
	}
}