<?php defined('SYSPATH') or die('No direct script access.');

abstract class Sprig extends Sprig_Core {

	/**
	 * Overload the create method to auto-insert
	 * the timestamp if one does not exist
	 *
	 * @return void
	 * @author Sam de Freyssinet
	 */
	public function create()
	{
		if ( ! isset($this->_fields['created']))
			return parent::create();

		if ($this->created === NULL)
			$this->created = time();

		return parent::create();
	}

	/**
	 * Overload the update method to auto-insert
	 * the timestamp for modified if not exist
	 *
	 * @return void
	 * @author Sam de Freyssinet
	 */
	public function update()
	{
		if ( ! isset($this->_fields['modified']))
			return parent::update();

		if ($this->modified === NULL)
			$this->modified = time();

		return parent::update();
	}
}