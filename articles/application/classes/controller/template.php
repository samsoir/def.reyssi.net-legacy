<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Template extends Kohana_Controller_Template {

	protected $_title;

	public function before()
	{
		parent::before();
		$this->template->set(array(
			'head'      => new View('components/head.phtml'),
			'footer'    => new View('components/footer.phtml'),
			'pages'     => new View('components/pages.phtml'),
		));

		// Get pages
		$pages = Request::factory('articles/all.json');
		$pages->get = array('articles' => 0);
		$pages = $pages->execute();

		// Parse the articles
		if ($pages->status === 200)
		{
			$pages = json_decode($pages->body);
			$this->template->set_global('pages', $pages);
		}

	}

	public function after()
	{
		$this->template->user = $this->_user;
		parent::after();
	}

}