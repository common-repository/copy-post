<?php
/*
The MIT License

Dayspring Technologies, Inc.  (http://www.dayspring-tech.com)
Copyright 2009, Dayspring Technologies, Inc.

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/

/*
Plugin Name: Copy Post
Plugin URI: http://blog.dayspring-tech.com/2009/06/wordpress-copy-post-plugin/
Description: Copies a post from the current wordpress instance to another
Author: Dayspring Technologies
Version: 0.2.0
Author URI: http://www.dayspring-tech.com/
*/



add_action('admin_menu', 'my_plugin_menu');
add_action( 'admin_init', 'register_mysettings' );

function my_plugin_menu() {
add_menu_page('Copy Post', 'Copy Post', 8, 'copy-post/copy_post.php');
add_options_page('Copy Post Options', 'Copy Post', 8, 'copy-post/options.php');

}

function register_mysettings() { // whitelist options
  register_setting( 'myoption-group', 'remote_url' );
  register_setting( 'myoption-group', 'remote_db_host' );
  register_setting( 'myoption-group', 'remote_db_user' );
  register_setting( 'myoption-group', 'remote_db_pass' );
  register_setting( 'myoption-group', 'remote_db_name' );
}


if (function_exists('mysql_set_charset') === false) {
	/**
	* Sets the client character set.
	*
	* Note: This function requires MySQL 5.0.7 or later.
	*
	* @see http://www.php.net/mysql-set-charset
	* @param string $charset A valid character set name
	* @param resource $link_identifier The MySQL connection
	* @return TRUE on success or FALSE on failure
	*/
	function mysql_set_charset($charset, $link_identifier = null)
	{
		if ($link_identifier == null) {
			return mysql_query('SET NAMES "'.$charset.'"');
		} else {
			return mysql_query('SET NAMES "'.$charset.'"', $link_identifier);
		}
	}
}


?>
