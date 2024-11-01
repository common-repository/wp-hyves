<?php
/**
 * WPHyvesProfileWidget.
 * @version 0.1
 * @author dligthart
 * @package wp-hyves
 */
class WPHyvesProfileWidget extends WPHyvesWidget {

	/**
	 * @return unknown_type
	 */
	function __construct() {
		$this->WPHyvesScrapsWidget();
	}

	/**
	 * @return unknown_type
	 */
	function WPHyvesProfileWidget() {
		$this->initWidget(__('Hyves Scraps', 'wp-hyves'));

	}

	/* (non-PHPdoc)
	 * @see wp-content/plugins/wp-hyves/classes/widget/WPHyvesWidget#widgetize()
	 */
	function widgetize($args) {
		
		extract( $args );
	
		$this->before_widget = $before_widget;
		$this->before_title = $before_title;
		$this->after_title = $after_title; 
   		$this->after_widget = $after_widget;
		
   		echo $this->before_widget;
   		
		echo '<div id="wphyves_scraps_widget">';
		
		echo $this->before_title;
		
		echo '<span class="widgettitle">'.__('Hyves Profile', 'wp-hyves').'</span>';

		echo $this->after_title;
		
		echo '</div>';
		
		echo $this->after_widget;
	}
	
	/**
	 * Get options.
	 * @return options array
	 * @access protected
	 */
	function getOptions() {
		$default_options = array(
				"show_widget" => 1,
				"title" => addslashes( __('Hyves Profile', 'wp-hyves') )
		);

		$options = get_option( $this->adminName );
		if( !empty( $options ) ){
			$curr_options = $options;
		}
		else{
			$curr_options = $default_options;
		}
		$this->options = $curr_options;
		return $this->options;
	}
}
?>