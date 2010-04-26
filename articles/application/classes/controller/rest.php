<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_REST extends Kohana_Controller_REST implements Kuaas_Authorise  {

	protected $_supported_formats;

	protected $_format;

	/**
	 * Test the authorised state of the user vs action vs resource
	 *
	 * @return void
	 * @author Sam de Freyssinet
	 */
	protected function authorised()
	{
		return Kuaas::instance()->authorise($this, $this->request->action, $this->_user);
	}

	public function before()
	{
		parent::before();

		// Store the request format
		$this->_format = ltrim($this->request->param('format'), '.');

		if ( ! in_array($this->_format, $this->_supported_formats))
			$this->request->action = 'not_found';
	}

	public function action_not_found()
	{
		$this->response->status = 404;
		$this->response->body = '<h1>File not found</h1>';
	}
}