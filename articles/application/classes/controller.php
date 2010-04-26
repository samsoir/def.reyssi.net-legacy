<?php defined('SYSPATH') or die('No direct script access.');

class Controller extends Kohana_Controller {

	protected $_config;
	protected $_user;
	protected $_title = 'def.reyssi.net';

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->_config = Kohana_Config::instance()->load('ledger');
		$this->_user = Kuaas::instance()->get_user();
	}

	/**
	 * Handles 404 not found
	 *
	 * @return void
	 */
	public function not_found()
	{
		$this->response->headers['status'] = 404;
		throw new Kohana_Request_Exception('404 Not Found');
	}
} // End Controller