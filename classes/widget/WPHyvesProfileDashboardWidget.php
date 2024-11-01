<?php
/**
 * WPHyvesProfileDashboardWidget.
 * @version 0.1
 * @author dligthart
 * @package wp-hyves
 */
class WPHyvesProfileDashboardWidget extends WPHyvesWidget {

	function __construct() {
		$this->WPHyvesProfileDashboardWidget();
	}

	function WPHyvesProfileDashboardWidget() {
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}

	function register_widget() {
		wp_register_sidebar_widget('wphyves_scraps', __('WP-Hyves - Profile', 'wp-hyves'),
		array(&$this, 'widget'),
		array(
			'all_link' => '',
			'feed_link' => '',
			'edit_link' => 'options.php' )
		);
	}

	function add_widget( $widgets ) {
		global $wp_registered_widgets;
		if ( !isset($wp_registered_widgets['wphyves_scraps']) ) return $widgets;
		array_splice( $widgets, 2, 0, 'wphyves_scraps' );
		return $widgets;
	}

	function widget($args = array()) {
		if (is_array($args))
		extract( $args, EXTR_SKIP );

		echo $before_widget.$before_title.$widget_name.$after_title;
		?>

<p class="sub"><?php _e('Import your hyves profile', 'wp-hyves'); ?> <img
	src="<?php bloginfo ('wpurl') ?>/wp-content/plugins/wp-hyves/resources/images/icon_24.png"
	alt="hyves logo" style="float: right;" /></p>
<a
	href="<?php echo wpHyvesGetScriptUrl().'&action=authorize_and_get_profile'?>"
	title="import scraps"> <input id="publish" class="button-primary"
	type="button" value="<?php _e('Import Profile', 'wp-hyves'); ?>"
	name="wphyves_import_scraps" /></a>
	<br/>
	<br/>
		<?php
		
echo $after_widget;
	}
}
?>