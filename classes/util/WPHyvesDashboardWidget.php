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
		wp_register_sidebar_widget('wphyves_friends', 'WP-Hyves - My Friends Cloud',
			array(&$this, 'widget'),
			array(
			'all_link' => 'http://www.daveligthart.com',
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
				//print_r($bu);
				$id = get_usermeta($bu->user_id, 'hyves_userid');
				if($id != ''){ // show thumb.

					echo '<a href="'. get_bloginfo('wpurl') .'/wp-admin/user-edit.php?user_id='.$bu->user_id.'" title="'. $user->first_name . ' ' . $user->last_name .' profile" />';
					echo '<img src="'.wpHyvesGetUploadPath(false, true) . $id . '-medium.jpg" alt="'.$user->first_name . ' ' . $user->last_name . '" />';
					echo '</a>';
					$i++;
				}
			}
		}

		echo '<p>';
		_e('Hyves Friends Added', 'wphyves');
		echo '('. $i .')';
		echo '<br/><a href="' . get_bloginfo('url') .'/wp-admin/options-general.php?page=wp-hyves" title="add friends page" />' . __('add friends', 'wphyves') . '</a>';
		echo '</p>';
		echo $after_widget;
	}
}

// Start widget.
add_action( 'plugins_loaded', create_function( '', 'global $wpHyvesDashWidget; $wpHyvesDashWidget = new WPHyvesDashboardWidget();' ) );

?>
