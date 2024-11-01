<?php
/**
 * WPHyvesUtils.
 * @version 0.2
 * @author dligthart <info@daveligthart.com>
 * @package wp-hyves
 */

/**
 * Get request token from session.
 * @param string $oauth_token
 * @return token
 * @access public
 */
function getRequestTokenFromSession($oauth_token) {
	$script_url = wpHyvesGetScriptUrl();
	if (!isset($_SESSION['requesttoken_'.$oauth_token])) {
		header("Location: ".$script_url."&action=invalidsession");
	}
	return unserialize($_SESSION['requesttoken_'.$oauth_token]);
}

/**
 * Get Access token from session.
 * @param string $local_token
 * @return token
 * @access public
 */
function getAccessTokenFromSession($local_token) {
	$script_url = wpHyvesGetScriptUrl();
	if (!isset($_SESSION['localtoken_'.$local_token])) {
		header("Location: ".$script_url."&action=invalidsession");
	}
	return unserialize($_SESSION['localtoken_'.$local_token]);
}

/**
 * Cache hyves image.
 * @param string $url
 * @param string $filename
 * @access public
 */
function wpHyvesCacheImage($url, $filename) {

	// Set path to upload.
	$hyves_image_cache_dir =  wpHyvesGetUploadPath();

	// Create dir if not exists.
	if(!file_exists($hyves_image_cache_dir)){
		dl_mkdirr($hyves_image_cache_dir);
	}

	// Write image to dir.
	if(file_exists($hyves_image_cache_dir) && is_writable($hyves_image_cache_dir)) {
		$dir = $hyves_image_cache_dir;
		$f = fopen( $dir . '/' . $filename , 'w' );
		$img      = file_get_contents($url);
		if($img) {
			fwrite($f, $img);
			fclose($f);
			flush();
		}
	}
}
/**
 * Get upload path to Hyves downloaded resources.
 * @param boolean $getBaseDir
 * @param boolean $relative get relative path
 * @return upload path
 * @access public
 */
function wpHyvesGetUploadPath($getBaseDir = false, $relative = false) {
	$pdir = get_option('upload_path');

	if(defined('ABSPATH')) {
		if(!stristr($pdir, ABSPATH) == ABSPATH && !$relative){
			$pdir = ABSPATH . $pdir;

			// Check if path exists.
			if(file_exists($pdir)){
				chmod($pdir, 0777);
			} else { // otherwise create it.
				mkdir($pdir, 0777);
			}
		}
		else if($relative) {
			$pdir = str_replace(ABSPATH, '', $pdir);
		}
	}

	if($relative) {
		$pdir = get_bloginfo('wpurl') . '/' . $pdir;
	}

	$dir = $pdir . '/wp-hyves/';

	if($getBaseDir) {
		$dir = $pdir;
	}
	return $dir;
}

/**
 * Get resources path.
 * @return path
 * @access public
 */
function wpHyvesGetResourcesPath() {
	return get_bloginfo ('wpurl') . '/wp-content/plugins/wp-hyves/resources';
}

/**
 * Get script url.
 * @return url
 * @access public
 */
function wpHyvesGetScriptUrl() {
	$url = get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=wp-hyves';
	return $url;
}

/**
 * Import users script url.
 * @return import url
 * @access public
 */
function wpHyvesImportUsersScriptUrl() {
	return get_bloginfo('wpurl') . '/wp-admin/users.php?page=wp-hyves';
}

/**
 * Regex for hyves shortcodes.
 * @return regex
 * @access public
 */
function wpHyvesGetShortcodePattern() {
	$tagnames = array('hyver','media','url');
	$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
	return '\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\1\])?';
}

/**
 * Strip hyves shortcodes from content.
 * @param string $content
 * @return stripped content
 * @access public
 */
function wpHyvesStripShortcodes($content) {
	$pattern = wpHyvesGetShortcodePattern();
	return preg_replace('/'.$pattern.'/s', '', $content);
}

/**
 * Replace emoticon short tags with emoticon img tags.
 * @param string $content
 * @return replaced content
 * @access public
 */
function wpHyvesReplaceEmoticonTags($content) {
	$pattern = ':(.*?):';
	return preg_replace('/'.$pattern.'/s', '<img src="' . wpHyvesGetResourcesPath() . '/images/emoticons/smiley_${1}.gif" alt="${1}" />', $content);
}

/**
 * Replace shortcodes.
 * @param string $content
 * @return replaced content
 * @access public
 */
function wpHyvesReplaceShortcode($content) {
	$content = strip_tags($content);

	$pattern = wpHyvesGetShortcodePattern();

	preg_match_all('/'.$pattern.'/s', $content, $matches);

	$i = 0;
	foreach($matches[0] as $full) {
		//echo $full;
		switch($matches[1][$i]) {
			case 'url':
				$content = str_replace($full, $matches[2][$i], $content);
			break;
		}
		$i++;
	}

	$content = wpHyvesStripShortcodes($content);

	$content = wpHyvesReplaceUrlWithLink($content);

	return $content;
}

/**
 * Find http:// url pattern and replace with anchor.
 * @param string $content
 * @return replaced content
 * @access public
 */
function wpHyvesReplaceUrlWithLink($content) {
	$a = $content;
	$a = preg_replace("/(ftp:\/\/|http:\/\/|https:\/\/|www|[a-zA-Z0-9-]+\.|[a-zA-Z0-9\.-]+@)(([a-zA-Z0-9-][a-zA-Z0-9-]+\.)+[a-zA-Z0-9-\.\/\_\?\%\#\&\=\;\~\!\(\)]+)/","<a target=\"_blank\" href=\"http://\\1\\2\">\\1\\2</a>",$a);
	$a = preg_replace('/http:\/\/http:\/\//',"http://",$a);
	$a = preg_replace('/http:\/\/([a-zA-Z0-9\.-]+@)/',"mailto:\\1",$a);
	$a = preg_replace("/\[<a([^>]*>)[^<]*<\/a>\|(.*?)\]/" , "<a target=\"_top\"\\1\\2</a>" ,$a);
	$a = preg_replace('/\[([a-zA-Z0-9#._]+)\|(.*?)\]/',"<a href=\"$1\">$2</a>",$a);
	return $a;
}

?>