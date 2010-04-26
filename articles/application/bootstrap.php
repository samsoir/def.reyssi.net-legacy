<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------
Kohana::$profiling = TRUE;
/**
 * Set the default time zone.
 *
 * @see  http://docs.kohanaphp.com/features/localization#time
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/London');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://docs.kohanaphp.com/features/autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array('base_url' => '/', 'index_file' => ''));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'kuaas'      => MODPATH.'kuaas',
	// 'auth'       => MODPATH.'auth',       // Basic authentication
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	'database'   => MODPATH.'database',   // Database access
	'sprig'      => MODPATH.'sprig',      // Sprig modelling layer
	'cache'      => MODPATH.'cache',      // Cache library
	// 'image'      => MODPATH.'image',      // Image manipulation
	// 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
	'pagination' => MODPATH.'pagination', // Paging of results
	'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('feed', 'feed<format>', array('format' => '\.\w+'))
	->defaults(array(
		'controller' => 'feed',
	));

Route::set('article', 'article(s)/<article>(<format>)', array('format' => '\.\w+'))
	->defaults(array(
		'format'     => '.xhtml',
		'controller' => 'articles',
	));

Route::set('archive', 'archive(/page(/<page>))(.html)', array('page' => '\d+'))
	->defaults(array(
		'page'       => 1,
		'controller' => 'archive',
		'action'     => 'index',
	));

// POST
Route::set('post', '<year>/<month>/<day>/<article>(.html)', array('year' => '\d{4}', 'month' => '\d{1,2}', 'day' => '\d{1,2}'))
	->defaults(array(
		'action'     => 'item',
		'controller' => 'article',
	));

// AUTHENTICATION
Route::set('login', '<action>(.html)', array('action' => 'log(in|out)|register'))
	->defaults(array(
		'controller' => 'auth',
	));

// HOME PAGE
Route::set('home', '(index.html)')
	->defaults(array(
		'controller' => 'index',
		'action'     => 'index',
	));

// STATIC CONTENT
Route::set('page', '<page>.html', array('page' => '[a-zA-Z0-9_/\-]+'))
	->defaults(array(
		'action'     => 'index',
		'controller' => 'page',
	));

// 404 NOT FOUND
Route::set('404', '404.html')
	->defaults(array(
		'controller' => 'index',
		'action'     => 'not_found',
	));

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
try
{
	echo Request::instance()
		->execute()
		->send_headers()
		->body();
}
catch (Exception $e)
{
	throw $e;
}