<?php defined('SYSPATH') or die('No direct script access.');

class Model_UserAttribute extends Sprig
{
	protected $_title_key = 'name';

	protected $_sorting = array('name' => 'asc');

	protected $_table = 'user_attributes';

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
				'empty'  => FALSE,
				'rules'  => array(
					'regex' => array('/^[\pL_.-]+$/ui')
				),
			)),
			'value' => new Sprig_Field_Text,
			'user_id' => new Sprig_Field_BelongsTo(array(
				'model'  => 'User',
			)),
		);
	}
}