<?php defined('SYSPATH') or die('No direct script access.');

class Model_Comment extends Sprig {

	protected function _init()
	{
		$this->_fields = array(
			'id'       => new Sprig_Field_Auto,
			'article'  => new Sprig_Field_BelongsTo(array(
				'Model'    => 'Article',
			)),
			'user'     => new Sprig_Field_BelongsTo(array(
				'Model'    => 'User',
			)),
			'comment'  => new Sprig_Field_Text(array(
				'empty'    => FALSE,
			)),
			'approved' => new Sprig_Field_Boolean(array(
				'empty'    => FALSE,
				'default'  => 0,
			)),
			'created'  => new Sprig_Field_Timestamp,
		);
	}
}