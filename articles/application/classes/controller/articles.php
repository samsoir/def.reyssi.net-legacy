<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Articles extends Controller_REST {

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

	protected $_limit = 100;

	/**
	 * Required for Kuaas_Authorise
	 *
	 * @return string
	 */
	public function _resource_name()
	{
		return 'articles';
	}

	public function action_create()
	{
		if ( ! $this->authorised())
			return $this->action_not_found();

		$article = Sprig::factory('Article');
		$article->values($_POST);
		// Stops people overloading the user id in post data
		$article->user = $this->_user->id;

		try
		{
			$article->create();
			$result = $article->as_array();
			$this->response->status = 201;
			$this->response->body = $this->_parse_output($result);
		}
		catch (Validate_Exception $e)
		{
			$this->response->status = 400;
			$this->response->body = $this->_parse_output($e->array->errors());
		}
	}

	public function action_read()
	{
		// Create an empty article
		$article = Sprig::factory('Article');

		// Discover the true RESTful action
		if ($this->request->param('article') == 'all')
		{
			// Test for a count request
			$get_count = isset($_GET['count']);

			$articles = Arr::get($_GET, 'articles', 1);
			$orderby = Arr::get($_GET, 'orderby', 'created');
			$direction = Arr::get($_GET, 'direction', 'DESC');
			$limit = NULL;

			// Setup SELECT query
			$select = DB::select()
				->where('article', '=', $articles)
				->order_by($orderby, $direction);

			// Setup additional parameters if get count
			if ( ! $get_count)
			{
				// Get limit
				$limit = Arr::get($_GET, 'limit', $this->_limit);
				$offset = Arr::get($_GET, 'offset', 0);
				$select->offset($offset);
			}

			$auth = FALSE;
			// Discover if user is authorised
			if (NULL === $this->_user)
				$select->where('published', '=', 1);
			elseif ( ! $this->authorised())
				$select->where('published', '=', 1);

			$articles = $article->load($select, $limit);

			if ($get_count)
				$response = $this->_parse_output(array('count' => $articles->count()));
			else
				$response = $this->_parse_output($articles);

			$this->response->body = $response;
		}
		else
		{
			$article->slug = $this->request->param('article');
			$article->load();

			// Check permissions
			if (NULL === $this->_user and $article->published === FALSE)
				return $this->action_not_found();
			elseif ( ! $this->authorised() and $article->published === FALSE)
				return $this->action_not_found();

			$output = $article->as_array();

			if ($article->article == 1)
			{
				$next = $article->next_article();
				$prev = $article->prev_article();
				$output['user'] = $article->user->load()->name;
				$output['next'] = $next ? $next->as_array() : FALSE;
				$output['prev'] = $prev ? $prev->as_array() : FALSE;
				$output['tags'] = $article->tags->as_array();
			}

			$this->response->body = $this->_parse_output($output);
		}
	}

	public function action_update()
	{
		$article = Sprig::factory('Article', array('id' => $_POST['id']))->load();
		$article->values($_POST);
		// Stops people overloading the user id in post data
		$article->user = $this->_user->id;

		try
		{
			$article->update();
			$result = $article->as_array();
			$this->response->status = 202;
			$this->response->body = $this->_parse_output($result);
		}
		catch (Validate_Exception $e)
		{
			$this->response->status = 400;
			$this->response->body = $this->_parse_output($e->array->errors());
		}
	}

	public function action_delete()
	{
		$article = Sprig::factory('Article', array('id' => $_POST['id']));

		try
		{
			$article->delete();
			$result = array('status' => 'deleted');
			$this->response->status = 202;
			$this->response->body = $this->_parse_output($result);
		}
		catch (Validate_Exception $e)
		{
			$this->response->status = 400;
			$this->response->body = $this->_parse_output($e->array->errors());
		}
	}

	protected function _parse_output($input)
	{
		// Convert content to array
		if ($input instanceof Sprig)
		{
			$data = (object) $input->as_array();
			$multiple = FALSE;
		}
		elseif ($input instanceof Kohana_Database_Result)
		{
			$multiple = TRUE;
			$_data = $input->as_array();
			$data = array();
			foreach ($_data as $key => $value)
			{
				$data[$key] = (object) $value->as_array();
				$data[$key]->user = $value->user->load()->name;
				$data[$key]->tags = $value->tags->as_array();
			}
		}
		elseif (is_array($input))
		{
			$data = (object) $input;
		}
		else
		{
			throw new Kohana_Exception('Unable to parse the data as it is type :type', array(':type' => gettype($data)));
		}

		$this->response->headers['Content-Type'] = 'application/json';
		return json_encode($data);

	}
}