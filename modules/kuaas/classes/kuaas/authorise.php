<?php defined('SYSPATH') or die('No direct script access.');

interface Kuaas_Authorise {
	/**
	 * Returns the resource name for this object
	 *
	 * @return  string
	 */
	public function _resource_name();
}