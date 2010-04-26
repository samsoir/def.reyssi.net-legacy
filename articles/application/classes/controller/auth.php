<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Auth extends Controller_Template {

	public $template = 'auth/base.phtml';

	protected $_kuaas;
	protected $_title = 'def.reyssi.net';

	public function before()
	{
		parent::before();
		$this->template->bind_global('title', $this->_title);

		$this->_kuaas = Kuaas::instance();
	}

	public function action_login()
	{
		$this->template->action = 'Login';

		// Error message
		$error = NULL;

		if (NULL !== $this->_user)
		{
			$this->response->headers['Location'] = Url::site();
			return;
		}

		// Try to login the user
		if ($this->request->method === 'POST')
		{
			if ($this->_kuaas->login($_POST['email'], $_POST['password']))
			{
				$this->request->redirect(Route::get('home')->uri(), 202);
			}

			$this->response->status = 401;
			$error = 'Email address or password was not found! Have you changed your email address recently?';
		}

		// Show the login page
		$user = Sprig::factory('User');

		// Create for inputs
		$form = array(
			$user->label('email') => $user->input('email', array('id' => 'email')),
			$user->label('password') => $user->input('password', array('id' => 'password')),
		);

		$this->template->login = $form;
		$this->template->error = $error;
	}

	// public function action_register()
	// {
	// 	// Nothing yet
	// }

	public function action_logout()
	{
		// Logout the current user
		$this->_kuaas->logout();
		// Return the to the homepage
		$this->request->redirect(Route::get('home')->uri(), 202);
	}
}