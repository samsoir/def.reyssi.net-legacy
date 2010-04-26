<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Archive extends Controller_Template {

	public $template = 'archive/base.phtml';

	protected $_limit = 10;

	public function action_index()
	{
		$this->template->set_global('title', 'def.reyssi.net/archive');

		$count = Request::factory('articles/all.json');
		$count->get = array('count' => TRUE);
		$count = $count->execute();

		if ($count->status !== 200)
			return;

		$count = json_decode($count->body);

		if ($count->count < 1)
			return;

		$pagination = Pagination::factory(array('total_items' => $count->count, 'items_per_page' => $this->_limit));

		$content = Request::factory('articles/all.json');
		$content->get = array(
			'articles'   => 1,
			'limit'      => $this->_limit,
			'offset'     => (($this->request->param('page')-1) * $this->_limit)
		);

		$content = $content->execute();

		if ($content->status === 200)
		{
			$content = json_decode($content->body);
			$this->template->articles = $content;
			$this->template->pagination = $pagination;
		}
	}
}