<?php
/*
Plugin Name: WP-Hyves
Plugin URI: http://wordpress.org/extend/plugins/wp-hyves/
Description: Import friends and Post to Hyves: a social networking website.
Version: 1.4.0.2
Author: Dave Ligthart
Author URI: http://www.daveligthart.com
*/

/**
 * Note!
 *
 * Leave the wpHyvesConsumerKey and wpHyvesConsumerSecret
 * empty if you want to create a consumer key and secret
 * for separate user accounts.
 *
 * Filling in these variables will enable a connection with the Hyves API
 * globally for all user accounts.
 *
 * Apply for a key and secret here: (create a new DESKTOP consumer)
 * http://www.hyves.nl/api/apply/
 *
 */

/** @var string consumer key */
$wpHyvesConsumerKey = '';

/** @var string consumer secret */
$wpHyvesConsumerSecret = '';

/**
 * END CONFIG --- No more editing beyond here.
 */

/**
 * Includes.
 */
include_once(dirname(__FILE__) . '/classes/util/com.daveligthart.util.wordpress.php');
include_once(dirname(__FILE__) . '/classes/util/com.daveligthart.php');
include_once(dirname(__FILE__) . '/classes/util/WPHyvesAPI.php');
include_once(dirname(__FILE__) . '/classes/util/WPHyvesWPPlugin.php');
include_once(dirname(__FILE__) . '/classes/util/WPHyvesAvatar.php');
include_once(dirname(__FILE__) . '/classes/model/WPHyvesAdminConfigForm.php');
include_once(dirname(__FILE__) . '/classes/action/WPHyvesAdminAction.php');
include_once(dirname(__FILE__) . '/classes/action/WPHyvesAdminConfigAction.php');
include_once(dirname(__FILE__) . '/classes/action/WPHyvesFrontEndAction.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesDashboardWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesWWWsDashboardWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesFriendsWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesFriendWWWsDashboardWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesFriendTipsDashboardWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesTipsWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesWWWsWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesScrapsDashboardWidget.php');
include_once(dirname(__FILE__) . '/classes/widget/WPHyvesScrapsWidget.php');

// Start widget.
add_action( 'plugins_loaded', create_function( '', 'global $wpHyvesFriendTipsDashWidget; $wpHyvesTipsDashWidget = new WPHyvesFriendTipsDashboardWidget();
global $wpHyvesFriendWWWsDashWidget; $wpHyvesWWWsDashWidget = new WPHyvesFriendWWWsDashboardWidget();
global $wpHyvesDashWidget; $wpHyvesDashWidget = new WPHyvesDashboardWidget();
global $wpHyvesWWWsDashWidget; $wpHyvesWWWsDashWidget = new WPHyvesWWWsDashboardWidget();
global $wpHyvesScrapsDashWidget; $wpHyvesScrapsDashWidget = new WPHyvesScrapsDashboardWidget();
' ) );

// Gives error, fix for next release.
//add_filter("get_avatar", "wphyves_avatar", 10, 4);

/**
 * WPHyves main.
 * @author dligthart <info@daveligthart.com>
 * @version 0.2
 * @package wp-hyves
 */
class WPHyvesMain extends WPHyvesWPPlugin {
	/**
	 * @var AdminAction admin action handler
	 */
	var $adminAction = null;

	/**
	 * @var FrontEndAction frontend action handler
	 */
	var $frontEndAction = null;

	/**
	 * Constructor.
	 * @param string $path
	 * @param string $key
	 * @param string $secret
	 */
	public function __construct($path, $key = '', $secret = '') {
		$this->WPHyvesMain($path, $key, $secret);
	}

	 /**
	  * WPHyvesMain
	  * @param string $path
	  * @param string $key
	  * @param string $secret
	  * @access public
	  */
	function WPHyvesMain($path, $key = '', $secret = '') {
		$this->register_plugin('wp-hyves', $path);
		if (is_admin()) {
			$this->adminAction = new WPHyvesAdminAction($this->plugin_name, $this->plugin_base, $key, $secret);
		} else {
			//$this->frontEndAction = new WPHyvesFrontEndAction($this->plugin_name, $this->plugin_base);
	 	}
	}
}

$wp_hyves = new WPHyvesMain(__FILE__, $wpHyvesConsumerKey, $wpHyvesConsumerSecret);

$wp_hyves_friends_widget = new WPHyvesFriendsWidget();
$wp_hyves_tips_widget = new WPHyvesTipsWidget();
$wp_hyves_wwws_widget = new WPHyvesWWWsWidget();
$wp_hyves_scraps_widget = new WPHyvesScrapsWidget();
?>