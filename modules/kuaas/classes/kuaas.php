<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Kohana User Authentication & Authorisation System
 * 
 * Provides authentication and authorisation
 *
 * @package  KUAAS
 * @category Auth
 * @author Sam de Freyssinet
 */
class Kuaas {

	/**
	 * Create an instance of the Kuaas library
	 *
	 * @param  array     config 
	 * @return void
	 */
	static public function instance(array $config = array())
	{
		static $instance;

		empty($instance) and $instance = new Kuaas($config);

		return $instance;
	}

	/**
	 * @var  Kohana_Config
	 */
	protected $_config;

	/**
	 * @var  Kohana_Session 
	 */
	protected $_session;

	/**
	 * Constructs the object ready for use
	 *
	 * @param   Kohana_Config 
	 */
	public function __construct(array $config = array())
	{
		if ( ! $config)
			$config = Kohana::config('kuaas');

		$config['salt_pattern'] = preg_split('/,\s*/', $config['salt_pattern']);

		// Set the config
		$this->_config = $config;

		if (NULL === $this->_session)
			$this->_session = Session::instance();
	}

	/**
	 * Returns the current logged in user
	 *
	 * @return  void|Model_User
	 */
	public function get_user()
	{
		return $this->_session->get($this->_config['session_key']);
	}

	/**
	 * Logs in a user with correct credentials
	 *
	 * @param   string   user 
	 * @param   string   password 
	 * @return  boolean
	 */
	public function login($user, $password)
	{
		if (empty($password))
			return FALSE;

		// Create a real model
		$user = $this->_load_user($user);

		if ( ! $this->authenticate($user, $password))
			return FALSE;

		return $this->_complete_login($user);
	}

	/**
	 * Authenticates users credentials
	 *
	 * @param   string   user 
	 * @param   string   password 
	 * @return  boolean
	 */
	public function authenticate($user, $password)
	{
		if (empty($password))
			return FALSE;

		// Change to proper user type
		if ( ! $user instanceof Model_User)
		{
			$user = $this->_load_user($user);
			if ( ! $user->loaded())
				return FALSE;
		}

		// Find the password salt
		$salt = $this->find_salt($user->password);
		$password = $this->hash_password($password, $salt);

		return ($user->password === $password);
	}

	public function authorise($resource, $action, Model_User $user = NULL)
	{
		// Cache the internal authorisation
		static $authorise_cache;

		// Get the current user if none supplied
		if (NULL === $user)
		{
			// Load the current user
			$user = $this->get_user();

			if (NULL === $user)
				return FALSE;
		}

		// Normalise the resource
		if ($resource instanceof Kuaas_Authorise)
			$resource = $resource->_resource_name();

		// Test the request cache
		if (isset($authorise_cache[$user->id][$resource][$action]))
			return $authorise_cache[$user->id][$resource][$action];

		// Big JOIN query
		$query = 'SELECT rgts.id, rgts.resource_id as resource, rgts.right, rgts.description, rgts.create, rgts.read, rgts.update, rgts.delete
					FROM users 
					LEFT JOIN (roles rls, roles_users rus)
					ON rls.id = rus.role_id AND rus.user_id = users.id
					LEFT JOIN (roles rls2, rights_roles rr, rights rgts, resources rs) 
					ON rls2.id = rr.role_id AND rr.right_id = rgts.id AND rgts.resource_id = rs.id 
					WHERE rs.resource = :resource AND users.id = :user LIMIT 1';

		// Do the big query
		$query = DB::query(Database::SELECT, $query);

		$rights = $query->param(':user', $user->id)
			->param(':resource', $resource)
			->as_object('Model_Right')
			->execute();

		// If there are no results there are no rights
		if ( ! $rights->count())
			return $authorise_cache[$user->id][$resource][$action] = FALSE;

		// Check the action against the right
		$right = $rights->current();

		$authorised = ($right->$action === TRUE);

		// Cache the result
		return $authorise_cache[$user->id][$resource][$action] = $authorised;
	}

	/**
	 * Logs out any users currently logged in
	 *
	 * @return boolean
	 */
	public function logout()
	{
		// Store username in session
		$this->_session->delete($this->_config['session_key']);

		// Regenerate session_id
		$this->_session->regenerate();

		return TRUE;
	}

	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 *
	 * @param   string  plaintext password
	 * @return  string  hashed password string
	 */
	public function hash_password($password, $salt = FALSE)
	{
		if ($salt === FALSE)
		{
			// Create a salt seed, same length as the number of offsets in the pattern
			$salt = substr($this->hash(uniqid(NULL, TRUE)), 0, count($this->_config['salt_pattern']));
		}

		// Password hash that the salt will be inserted into
		$hash = $this->hash($salt.$password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
		$password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($this->_config['salt_pattern'] as $offset)
		{
			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part.array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;
		}

		// Return the password, with the remaining hash appended
		return $password.$hash;
	}

	/**
	 * Perform a hash, using the configured method.
	 *
	 * @param   string  string to hash
	 * @return  string
	 */
	public function hash($str)
	{
		return hash($this->_config['hash_method'], $str);
	}

	/**
	 * Finds the salt from a password, based on the configured salt pattern.
	 *
	 * @param   string  hashed password
	 * @return  string
	 */
	public function find_salt($password)
	{
		$salt = '';

		foreach ($this->_config['salt_pattern'] as $i => $offset)
		{
			// Find salt characters, take a good long look...
			$salt .= substr($password, $offset + $i, 1);
		}

		return $salt;
	}

	/**
	 * Completes the authorisation process
	 *
	 * @param   Model_User $user 
	 * @return  boolean
	 */
	protected function _complete_login($user)
	{
		// Regenerate session_id
		$this->_session->regenerate();

		// Store username in session
		$this->_session->set($this->_config['session_key'], $user);

		return TRUE;
	}

	/**
	 * Load a user based on the user id/email
	 *
	 * @param   string|int    user id or email
	 * @return  void
	 */
	protected function _load_user($id)
	{
		if ($id instanceof Model_User)
			return $user;
		if (is_string($id))
		{
			$options = ctype_digit($id) ? 
				array('id' => $id) :
				array('email' => $id);
		}
		else
			$options = array('id' => $id);

		$user = Sprig::factory('User', $options)
			->load();

		return $user;
	}
}