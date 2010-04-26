<?php defined('SYSPATH') or die('No direct script access.');

// Include the Markdown file (must exist in PEAR)
include('markdown.php');

/**
 * A simple OO wrapper for the Markdown encoder
 * from PEAR
 *
 * @package  Markdown
 * @category Markdown
 * @author   Sam de Freyssinet
 */
class Markdown {

	/**
	 * Encodes the supplied string from Markdown to HTML
	 * format
	 *
	 * @param   string   Markdown
	 * @return  string   HTML
	 */
	static public function encode($string)
	{
		if ( ! function_exists('Markdown'))
			throw new Kohana_Exception('Unable to find the Markdown encoding library');

		// Return the HTML encoded version of the Markdown string
		return Markdown($string);
	}

	final public function __construct() {}

} // End Markdown_Name