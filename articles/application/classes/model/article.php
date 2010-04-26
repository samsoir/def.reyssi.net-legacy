<?php defined('SYSPATH') or die('No direct script access.');

class Model_Article extends Sprig {

	protected $_title_key = 'title';

	protected function _init()
	{
		$this->_fields += array(
			'id'       => new Sprig_Field_Auto,
			'user'     => new Sprig_Field_BelongsTo(array(
				'Model'      => 'User',
			)),
			'title'    => new Sprig_Field_Char(array(
				'empty'      => FALSE,
				'rules'      => array(
					'max_length'    => array(50),
				),
			)),
			'description' => new Sprig_Field_Char(array(
				'rules'      => array(
					'max_length'    => array(500),
				),
			)),
			'content'     => new Sprig_Field_Text(array(
				'empty'      => FALSE,
			)),
			'cached_content' => new Sprig_Field_Text,
			'slug'        => new Sprig_Field_Char(array(
				'unique'     => TRUE,
				'rules'      => array(
					'regex'      => array('/[\pL]/'),
					'max_length' => array(50),
				),
			)),
			'article'     => new Sprig_Field_Boolean(array(
				'default'    => FALSE,
			)),
			'published'   => new Sprig_Field_Boolean(array(
				'default'    => FALSE,
			)),
			'comment_status' => new Sprig_Field_Boolean(array(
				'default'    => FALSE,
			)),
			'created'     => new Sprig_Field_Timestamp(array(
				'empty'      => FALSE,
			)),
			'modified'    => new Sprig_Field_Timestamp(array(
				'empty'      => TRUE,
			)),
			'comments'    => new Sprig_Field_HasMany(array(
				'Model'      => 'Comment',
			)),
			'tags'        => new Sprig_Field_ManyToMany(array(
				'Model'      => 'Tag',
			)),
		);
	}

	public function create()
	{
		$this->_cache_content();

		parent::create();
	}

	public function update()
	{
		$this->_cache_content();

		parent::update();
	}

	public function next_article()
	{
		$query = 'SELECT articles.id, articles.user_id AS user, articles.title, articles.description, articles.content, articles.cached_content, articles.slug, articles.article, articles.published, articles.comment_status, articles.created, articles.modified FROM articles WHERE articles.published = 1 AND articles.article = 1 AND articles.created >= :created AND articles.id != :id ORDER BY articles.created ASC LIMIT 1';

		$result = DB::query(Database::SELECT, $query)
			->param(':created', $this->created)
			->param(':id', $this->id)
			->as_object('Model_Article')
			->execute();

		return $result->count() ? $result->current() : FALSE;
	}

	public function prev_article()
	{
		$query = 'SELECT articles.id, articles.user_id AS user, articles.title, articles.description, articles.content, articles.cached_content, articles.slug, articles.article, articles.published, articles.comment_status, articles.created, articles.modified FROM articles WHERE articles.published = 1 AND articles.article = 1 AND articles.created <= :created AND articles.id != :id ORDER BY articles.created DESC LIMIT 1';

		$result = DB::query(Database::SELECT, $query)
			->param(':created', $this->created)
			->param(':id', $this->id)
			->as_object('Model_Article')
			->execute();

		return $result->count() ? $result->current() : FALSE;
	}

	/**
	 * Encodes the content into HTML based on Markdown
	 *
	 * @return  void
	 */
	protected function _cache_content()
	{
		$this->cached_content = Markdown::encode($this->content);

		if (preg_match_all('/<p>(.*)<\/p>/', $this->cached_content, $matches))
		{
			$this->description = substr(strip_tags($matches[1][0]), 0, 500);
		}
	}
}