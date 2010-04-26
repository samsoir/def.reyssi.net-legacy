<?php defined('SYSPATH') or die('No direct script access.');

return array
(
	// CHANGE THIS TO SOMETHING RANDOM
	'salt_pattern'=> '1, 3, 5, 9, 15, 19, 23, 26, 29',
	'hash_method' => 'sha1',
	'session_key' => 'kauus_auth',
	'lifetime'    => 60*60*24*30,
	'driver'      => NULL,
);