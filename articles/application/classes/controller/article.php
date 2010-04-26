<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Article extends Controller_Template {

	public $template = 'article/base.phtml';

	public function action_item()
	{
		$article = Request::factory('article/'.$this->request->param('article').'.json')
			->execute();


		if ($article->status != 200)
			return $this->not_found();

		// Decode the article
		$article = json_decode($article->body);

		// Check that the vitals are right
		if ( ! $this->check_article($article))
			return $this->not_found();

		$this->template->content = $article;
		$this->template->bind_global('title', $article->title);
	}

	/**
	 * Tests that the article vitals match
	 * what was requested
	 *
	 * @param   stdClass article 
	 * @return  boolean
	 */
	protected function check_article($article)
	{
		// First test the published status
		if ($article->published != 1)
			return FALSE;

		// Test the page status
		if ($article->article != 1)
			return FALSE;

		// Create date from request. This is required
		// to normalise request format
		$request_date = mktime(1, 1, 1, $this->request->param('month'), $this->request->param('day'), $this->request->param('year'));

		// Test the date
		return (date('Y-m-d', $article->created) == date('Y-m-d', $request_date));
	}
}