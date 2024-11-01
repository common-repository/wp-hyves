<?php
/**
 * WPHyvesScrapsWidget.
 * @version 0.1
 * @author dligthart
 * @package wp-hyves
 */
class WPHyvesScrapsWidget extends WPHyvesWidget {

	/**
	 * @return unknown_type
	 */
	function __construct() {
		$this->WPHyvesScrapsWidget();
	}

	/**
	 * @return unknown_type
	 */
	function WPHyvesScrapsWidget() {
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
		
		echo '<span class="widgettitle">'.__('Hyves Scraps', 'wp-hyves').'</span>';

		echo $this->after_title;
		
		echo '<ul id="wphyves_scraps_list">';
		
		// Load scraps.
		$scraps = unserialize(stripslashes(get_option('wphyves_scraps')));

		if(null != $scraps && is_array($scraps)) {
			foreach($scraps as $scrap) {
				echo "<li>";
				
				echo '<img src="'. $scrap['avatar'] .'" alt="'. __('from', 'wp-hyves') . ' '. $scrap['name'] .'" width="48" height="48" />&nbsp;';
				
				echo '<span class="wphyves_scrap_from">';
				
				echo $scrap['name'];
				
				echo '</span>';
				
				echo '<p class="wphyves_scrap_body">';
				
				$scrap_body = $scrap['body'];
				
				$scrap_body = wpHyvesStripShortcodes($scrap_body);
				
				$scrap_body = wpHyvesReplaceEmoticonTags($scrap_body);
				
				echo $scrap_body;
				
				echo '</p>';
				
				echo '</li>';			
			}	
		}
		
		echo "</ul>";
		
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
				"title" => addslashes( __('Hyves Scraps', 'wp-hyves') )
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