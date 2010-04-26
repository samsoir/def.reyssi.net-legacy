<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{
		$user = Sprig::factory('User');
		$user->id = 1;
		$user->load();

	}

} // End Welcome
