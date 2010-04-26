<?php defined('SYSPATH') or die('No direct script access.');

class Model_Permission extends Sprig
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
			'id' => new Sprig_Field_Auto,
			'resource' => new Sprig_Field_BelongsTo(array(
				'model'  => 'Resource',
			)),
			'role'     => new Sprig_Field_BelongsTo(array(
				'model'  => 'Role',
			)),
			'create'   => new Sprig_Field_Boolean,
			'read'     => new Sprig_Field_Boolean,
			'update'   => new Sprig_Field_Boolean,
			'delete'   => new Sprig_Field_Boolean,
		);
	}
}