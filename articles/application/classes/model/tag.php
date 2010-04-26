<?php defined('SYSPATH') or die('No direct script access.');

class Model_Tag extends Sprig {

	protected function _init()
	{
		$this->_fields = array(
			'id'      => new Sprig_Field_Auto,
			'tag'     => new Sprig_Field_Char(array(
				'unique'    => TRUE,
				'empty'     => FALSE,
				'rules'     => array(
					'max_length'   => 30,
				),
			)),
			'articles' => new Sprig_Field_ManyToMany(array(
				'Model'     => 'Article',
			)),
		);
	}
}