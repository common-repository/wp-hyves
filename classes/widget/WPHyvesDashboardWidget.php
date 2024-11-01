<?php
/**
 * WPHyvesDashboardWidget.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-hyves
 */
class WPHyvesDashboardWidget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->WPHyvesDashboardWidget();
	}

	/**
	 * Constructor.
	 */
	function WPHyvesDashboardWidget() {
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}
	/**
	 * Register widget.
	 * @access protected
	 */
	function register_widget() {
		wp_register_sidebar_widget('wphyves_friends', __('WP-Hyves - My Friends Cloud', 'wp-hyves'),
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
		if ( !isset($wp_registered_widgets['wphyves_friends']) ) return $widgets;
		array_splice( $widgets, 2, 0, 'wphyves_friends' );
		return $widgets;
	}
	/**
	 * Widget.
	 */
	function widget($args = array()) {
		if (is_array($args))
			extract( $args, EXTR_SKIP );

		echo $before_widget.$before_title.$widget_name.$after_title;

		// Get current users.
		$blogusers = get_users_of_blog();
		$i = 0;
		if(is_array($blogusers) && count($blogusers) > 0) {
			foreach($blogusers as $bu) {
				setup_userdata($bu->user_id);
				global $user_url;
				global $user_login;
				//print_r($bu);
				$id = get_usermeta($bu->user_id, 'hyves_userid');
				if($id != '') { // show thumb.
					echo '<a href="'. get_bloginfo('wpurl') .'/wp-admin/user-edit.php?user_id='.$bu->user_id.'" title="'.$user_login.'">';
					echo '<img src="'.wpHyvesGetUploadPath(false, true) . $id . '-medium.jpg" alt="" />';
					echo '</a>';
					$i++;
				}
			}
		}

		echo '<p>';
		_e('Hyves Friends Added', 'wp-hyves');
		echo '('. $i .')';
		echo '<br/><a href="' . get_bloginfo('wpurl') .'/wp-admin/users.php?page=wp-hyves" title="add friends page" />' . __('add friends', 'wp-hyves') . '</a>';
		echo '</p>';
		echo $after_widget;
	}
}
?>