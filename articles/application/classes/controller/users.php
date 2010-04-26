<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Users extends Controller_REST {

	protected $_action_map = array
	(
		'GET'    => 'read',
		'PUT'    => 'create',
		'POST'   => 'update',
		'DELETE' => 'delete',
	);

	protected $_supported_formats = array(
		'json',
	);

	/**
	 * Required for Kuaas_Authorise
	 *
	 * @return string
	 */
	public function _resource_name()
	{
		return 'users';
	}

	public function action_create()
	{
		if ( ! $this->authorised())
			return $this->action_not_found();

		
	}

	public function action_read()
	{
		if ( ! $this->authorised())
			return $this->action_not_found();

	}

	public function action_update()
	{
		if ( ! $this->authorised())
			return $this->action_not_found();

	}

	public function action_delete()
	{
		if ( ! $this->authorised())
			return $this->action_not_found();

	}
}