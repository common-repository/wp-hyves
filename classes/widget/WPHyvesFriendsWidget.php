<?php
/**
 * WPHyvesFriendsWidget.
 * @version 0.1
 * @author dligthart
 * @package wp-hyves
 */
class WPHyvesFriendsWidget extends WPHyvesWidget {
	function __construct() {
		$this->WPHyvesFriendsWidget();
	}
	/**
	 * WPHyvesFriendWidget.
	 */
	function WPHyvesFriendsWidget() {
		$this->initWidget(__('Hyves Friend Cloud', 'wp-hyves'));
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
			
			echo '<div id="wphyves_myfriends_widget">';
			
			echo $this->before_title;
			
			echo '<span class="widgettitle">'.__('Hyves Friend Cloud', 'wp-hyves').'</span>';
			
			echo $this->after_title;
			
			foreach($blogusers as $bu) {

				setup_userdata($bu->user_id);

				global $user_url;
				global $user_ID;

				$id = get_usermeta($user_ID, 'hyves_userid');

				if($id != ''){ // show thumb.

					echo '<a class="wphyves_myfriend_link" href="'. $user_url. '" target="_blank" title="'. $user->first_name . ' ' . $user->last_name .' profile">';
					echo '<img src="'.wpHyvesGetUploadPath(false, true) . $id . '-medium.jpg" alt="'.$user->first_name . ' ' . $user->last_name . '" class="wphyves_myfriend_image" style="width:15%;"/>';
					echo '</a>';

					$i++;
				}
			}
			
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
				"title" => addslashes( __('Hyves Friend Cloud', 'wp-hyves'))
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