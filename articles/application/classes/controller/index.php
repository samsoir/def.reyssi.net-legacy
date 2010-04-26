<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Template {

	public $template = 'homepage/base.phtml';

	protected $_title = 'def.reyssi.net';

	public function before()
	{
		parent::before();
		// Apply title
		$this->template->set_global('title','def.reyssi.net');
	}

	public function action_index()
	{
		// Apply title
		$this->template->set_global('is_home' ,TRUE);

		// Get the article count
		$count = Request::factory('articles/all.json');
		$count->get = array('count' => TRUE);
		$count = $count->execute();

		// Parse the number of total articles
		if ($count->status === 200)
		{
			$count = json_decode($count->body);
			$count = $count->count;
		}
		else
			$count = 0;

		if ( ! $count)
			return;

		// Get articles
		$content = Request::factory('articles/all.json');
		$content->get = array('articles' => 1, 'limit' => 5);
		$content = $content->execute();

		// Parse the articles
		if ($content->status === 200)
		{
			$content = json_decode($content->body);
			$this->template->leader = array_shift($content);
			$this->template->columns = $content;
		}

		$this->template->count = $count;

	}

	public function after()
	{
		parent::after();
//		$this->response->body .= View::factory('profiler/stats');
		$this->response->headers['X-Powered-By'] = 'Kohana PHP 3.0.1/Ledger 0.1';
	}

} // End Controller_Index