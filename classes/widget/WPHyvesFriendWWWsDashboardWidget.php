<?php
/**
 * WPHyvesFriendWWWsDashboardWidget.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-hyves
 */
class WPHyvesFriendWWWsDashboardWidget {

	/**
	 * Constructor.
	 */
	function __construct() {
		$this->WPHyvesFriendWWWsDashboardWidget();
	}

	/**
	 * Constructor.
	 */
	function WPHyvesFriendWWWsDashboardWidget() {
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}
	/**
	 * Register widget.
	 * @access protected
	 */
	function register_widget() {
		wp_register_sidebar_widget('wphyves_friend_wwws', __('WP-Hyves - Friend WWWs', 'wp-hyves'),
			array(&$this, 'widget'),
			array(
			'all_link' => '',
			'feed_link' => '',
			'edit_link' => 'options.php' )
		);
	}
	/**
	 * Add widget.
	 * @param array $widgets
	 * @access protected
	 */
	function add_widget( $widgets ) {
		global $wp_registered_widgets;
		if ( !isset($wp_registered_widgets['wphyves_friend_wwws']) ) return $widgets;
		array_splice( $widgets, 2, 0, 'wphyves_friend_wwws' );
		return $widgets;
	}
	/**
	 * Widget.
	 */
	function widget($args = array()) {
		if (is_array($args))
			extract( $args, EXTR_SKIP );

		echo $before_widget.$before_title.$widget_name.$after_title;
?>
<p class="sub"><?php _e("What are your friends doing?", 'wp-hyves'); ?>
<img src="<?php bloginfo ('wpurl') ?>/wp-content/plugins/wp-hyves/resources/images/icon_24.png" alt="hyves logo" style="float:right;"/>
</p>
<a href="<?php echo wpHyvesGetScriptUrl().'&action=authorize_and_get_wwws'?>" title="import friend wwws">
<input id="publish" class="button-primary" type="button" value="<?php _e('Import WWWs', 'wp-hyves'); ?>" name="wphyves_import_wwws" />
</a>
<p>
<?php
// Display Friend Hyver WWWs.
$blogusers = get_users_of_blog();
$i = 0;
foreach($blogusers as $bu) {
	if(null != $bu && '' != $bu->user_id) {
		setup_userdata($bu->user_id);
		global $user_url;
		$cur_www = get_usermeta($bu->user_id, 'hyves_www');
		$cur_www = wpHyvesStripShortcodes($cur_www); // remove hyves shortcodes.
		$cur_www = wpHyvesReplaceEmoticonTags($cur_www); // add emoticons.
		$id = get_usermeta($bu->user_id, 'hyves_userid');

		if('' != $id) {
			$i++; // count friends.
		}

		if('' != $cur_www && '@' != $cur_www && '' != $id) {
			echo '<a href="'.$user_url.'" title="go to hyve" target="_blank" >';
			echo '<img src="'.wpHyvesGetUploadPath(false, true) . $id . '-medium.jpg" alt="" width="25" height="25" />';
			echo '</a>&nbsp;';
			echo '<strong>' . $bu->user_login . ':</strong>&nbsp;' . $cur_www;
			echo '<br/>';
		}
	}
}

if($i == 0) {
	echo '<p>' . __('(0) Friends found', 'wp-hyves');
	echo '<br/>';
	echo '<a href="' . get_bloginfo('wpurl') .'/wp-admin/users.php?page=wp-hyves" title="add friends page" />' . __('add friends', 'wp-hyves') . '</a>';
	echo '</p>';
}
?>
</p>
<?php
		echo $after_widget;
	}
}
?>