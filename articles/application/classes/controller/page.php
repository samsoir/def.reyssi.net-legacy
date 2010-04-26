<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page extends Controller_Template {

	public $template = 'page/base.phtml';

	public function action_index()
	{
		$page = Request::factory('article/'.$this->request->param('page').'.json')
			->execute();

		
		if ($page->status != 200)
			return $this->not_found();

		// Decode the article
		$page = json_decode($page->body);

		// Check that the vitals are right
		if ( ! $this->check_page($page))
			return $this->not_found();

		$this->template->content = $page;
		$this->template->bind_global('title', $page->title);
	}

	/**
	 * Tests that the article vitals match
	 * what was requested
	 *
	 * @param   stdClass article 
	 * @return  boolean
	 */
	protected function check_page($page)
	{
		// First test the published status
		if ($page->published != 1)
			return FALSE;

		// Test the page status
		return (0 == $page->article);
	}
}