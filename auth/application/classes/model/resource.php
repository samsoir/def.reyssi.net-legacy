<?php defined('SYSPATH') or die('No direct script access.');

class Model_Resource extends Sprig
{
	protected function _init()
	{
		$this->_fields += array(
			'id'            => new Sprig_Field_Auto,
			'resource_id'   => new Sprig_Field_Integer(array(
				'empty'        => FALSE,
			)),
			'role_id'       => new Sprig_Field_Integer(array(
			)),
			'description'   => new Sprig_Field_Char(array(
				'max_length'   => 300,
			)),
			'permissions'   => new Sprig_Field_HasMany(array(
				'model'        => 'Permission',
			)),
		);
	}
}