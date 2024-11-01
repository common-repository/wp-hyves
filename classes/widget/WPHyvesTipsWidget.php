<?php
/**
 * WPHyvesTipsWidget.
 * @version 0.1
 * @author dligthart
 * @package wp-hyves
 */
class WPHyvesTipsWidget extends WPHyvesWidget {
	
	/**
	 * @return unknown_type
	 */
	public function __construct() {
		$this->WPHyvesTipsWidget();
	}
	
	/**
	 * WPHyvesFriendWidget.
	 */
	function WPHyvesTipsWidget() {
		$this->initWidget(__('Hyves Tips from Friends', 'wp-hyves'));
	}
	
	/**
	 * Output widget.
	 * @access protected
	 */
	function widgetize($args) {
		extract( $args );
	
		$this->before_widget = $before_widget;
		$this->before_title = $before_title;
		$this->after_title = $after_title; 
   		$this->after_widget = $after_widget;
		
		// Get current users.
		$blogusers = get_users_of_blog();

		$i = 0;
		if(is_array($blogusers) && count($blogusers) > 0) {

			echo $this->before_widget;
			
			echo '<div id="wphyves_tips_widget">';
			
			echo $this->before_title;
			echo '<span class="widgettitle">'.__('Hyves Tips from Friends', 'wp-hyves').'</span>';
			echo $this->after_title;
			
			foreach($blogusers as $bu):

				setup_userdata($bu->user_id);

				global $user_url;
				global $user_ID;

				$id = get_usermeta($user_ID, 'hyves_userid');

				if($id != ''){ // show thumb.

					$cur_tip = get_usermeta($user_ID, 'hyves_tip');

					if('' != $cur_tip) {
						$cur_tip = wpHyvesReplaceEmoticonTags($cur_tip);

						$cur_tip = wpHyvesReplaceShortcode($cur_tip);

						echo '<p><a href="'.$user_url.'" title="go to hyve" target="_blank" >';
						echo '<img src="'.wpHyvesGetUploadPath(false, true) . $id . '-medium.jpg" alt="" width="25" height="25" />';
						echo '</a>&nbsp;';
						echo '<strong>' . $bu->user_login . '</strong>:&nbsp;<br/>' . $cur_tip;
						echo '</p>';

						$i++;
					}
				}

			endforeach;
			echo '</div>';
			
			echo $this->after_widget;
		}
	}
	
	/**
	 * Get options.
	 * @return options array
	 * @access protected
	 */
	function getOptions() {
		$default_options = array(
				"show_widget" => 1,
				"title" => addslashes( __("Hyves Tips from Friends", 'wp-hyves') )
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