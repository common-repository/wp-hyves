<?php
/**
 * WPHyvesWWWsDashboardWidget.
 * @author dligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-hyves
 */
class WPHyvesWWWsDashboardWidget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->WPHyvesWWWsDashboardWidget();
	}

	/**
	 * Constructor.
	 */
	function WPHyvesWWWsDashboardWidget() {
		add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
		add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
	}
	/**
	 * Register widget.
	 * @access protected
	 */
	function register_widget() {
		wp_register_sidebar_widget('wphyves_wwws', __('WP-Hyves - WWWs', 'wp-hyves'),
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
		if ( !isset($wp_registered_widgets['wphyves_wwws']) ) return $widgets;
		array_splice( $widgets, 2, 0, 'wphyves_wwws' );
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
<p class="sub"><?php _e("Show your friends what you're doing", 'wp-hyves'); ?>
<img src="<?php bloginfo ('wpurl') ?>/wp-content/plugins/wp-hyves/resources/images/icon_24.png" alt="hyves logo" style="float:right;" /></p>
<form id="wphyves_www_form" name="wphyves_www_form" method="post" action="<?php echo wpHyvesGetScriptUrl(); ?>">
  <input name="action" type="hidden" id="action" value="authorize_and_post_www" />
  <label>what <br />
  <input name="what" type="text" id="wphyves_what" size="20" maxlength="255" />
  </label>
  <label><br />
  where
  <br />
  <input name="where" type="text" id="wphyves_where" size="20" maxlength="255" />
  </label>
  <br />
  <br />
  <input id="wphyves_add_www" class="button-primary" type="submit" value="Add WWW" name="wphyves_add_www"/>
</form>
<?php
		echo $after_widget;
	}
}
?>