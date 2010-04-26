<?php defined('SYSPATH') or die('No direct script access.');

class Model_Role extends Sprig
{
	protected $_title_key = 'name';

	protected $_sorting = array('name' => 'asc');

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
			'name' => new Sprig_Field_Char(array(
				'max_length' => 50,
				'empty'  => FALSE,
				'rules'  => array(
					'regex' => array('/^[\pL_.-]+$/ui')
				),
			)),
			'description' => new Sprig_Field_Char(array(
				'max_length' => 300,
			)),
			'users'       => new Sprig_Field_ManyToMany(array(
				'model'      => 'User',
			)),
			'permissions' => new Sprig_Field_HasMany(array(
				'model'      => 'Permission',
			)),
		);
	}
}