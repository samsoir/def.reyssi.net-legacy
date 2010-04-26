<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Feed extends Controller_REST {

	protected $_supported_formats = array(
		'rss',
	);

	protected $_pub_date;

	protected $_last_build;

	public function _resource_name()
	{
		return 'feed';
	}

	public function action_index()
	{
		// Load items
		$content = Request::factory('articles/all.json');
		$content->get = array('articles' => 1, 'limit' => $this->_config['feed_length']);
		$content = $content->execute();

		if ($content->status !== 200 or $content->headers['Content-Type'] !== 'application/json')
		{
			$this->response->status = 500;
			$this->response->body = '<h1>Internal server error</h1>';
			return;
		}

		$content = json_decode($content->body);
		$content = $this->_format_feed_array($content); 

		$info = array
		(
			'title'       => $this->_config['blog_name'],
			'link'        => Url::site(),
			'description' => $this->_config['description'],
			'language'    => $this->_config['language'],
			'copyright'   => $this->_config['copyright'],
			'pubDate'     => $this->_pub_date,
			'lastBuildDate' => $this->_last_build,
		);

		$this->response->headers['Content-Type'] = 'application/rss+xml';
		$this->response->body = Feed::create($info, $content);
	}

	protected function _format_feed_array($items)
	{
		$output = array();
		foreach ($items as $value)
		{
			$output[] = array
			(
				'title'       => $value->title,
				'link'        => date('Y/m/d/'.$value->slug, $value->created),
				'description' => '<![CDATA['.$value->cached_content.']]>',
				'pubDate'     => $value->created,
				'source'      => Url::site('feed.rss', 'http'),
			);

			// Handle pub/last build
			if ($value->created > $this->_pub_date)
				$this->_pub_date = $value->created;

			if (NULL !== $value->modified)
				$this->_last_build = $value->modified;
		}

		if (NULL === $this->_last_build)
			$this->_last_build = $this->_pub_date;

		return $output;
	}
}