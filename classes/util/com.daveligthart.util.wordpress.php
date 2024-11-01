<?php
/**
 * Utils Extra WordPress Functions.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package com.daveligthart.util.wordpress
 */

if(!function_exists('dl_load_admin_block')):
/**
 * Block template loader.
 * @param string $name
 * @param array $vars
 * @access public
 */
function dl_load_admin_block($name, $vars = array()) {
	if(count($vars) > 0 &&  is_array($vars)) {
		foreach($vars as $key=>$value){
			$$key = $value;
		}
	}
	include(dirname(__FILE__) . '/../../view/admin/blocks/' . $name . '.php');
}
endif;

if(!function_exists('dl_mkdirr')):
function dl_mkdirr($pathname, $mode = 0777) { // Recursive, Hat tip: PHP.net
	// Check if directory already exists
	if ( is_dir($pathname) || empty($pathname) )
		return true;

	// Ensure a file does not already exist with the same name
	if ( is_file($pathname) )
		return false;

	// Crawl up the directory tree
	$next_pathname = substr( $pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR) );
	if ( dl_mkdirr($next_pathname, $mode) ) {
		if (!file_exists($pathname))
			return mkdir($pathname, $mode);
	}

	return false;
}
endif;

if(!function_exists('dl_create_user')):

function dl_create_user($username, $password, $url = '', $email = '') {
	global $wpdb;

	$user_login = $wpdb->escape($username);
	$user_email = $wpdb->escape($email);
	$user_pass = $password;
	$user_url =  $url;

	$userdata = compact('user_login', 'user_email', 'user_pass', 'user_url');
	return wp_insert_user($userdata);
}

endif;


if(!function_exists('dl_generatePassword')):
function dl_generatePassword ($length = 8) {

  // start with a blank password
  $password = "";

  // define possible characters
  $possible = "0123456789bcdfghjkmnpqrstvwxyz";

  // set up a counter
  $i = 0;

  // add random characters to $password until $length is reached
  while ($i < $length) {

    // pick a random character from the possible ones
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

    // we don't want this character if it's already in the password
    if (!strstr($password, $char)) {
      $password .= $char;
      $i++;
    }

  }

  // done!
  return $password;

}
endif;
?>