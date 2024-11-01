<?php
/**
 * WPHyvesWWWsWidget.
 * @version 0.1
 * @author dligthart
 * @package wp-hyves
 */
class WPHyvesWWWsWidget extends WPHyvesWidget {
	
	/**
	 * @return unknown_type
	 */
	public function __construct() {
		$this->WPHyvesWWWsWidget();
	}
	
	/**
	 * WPHyvesFriendWidget.
	 */
	function WPHyvesWWWsWidget() {
		$this->initWidget(__('Hyves WWWs', 'wp-hyves'));
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

   		
   		
		// Get current users.
		$blogusers = get_users_of_blog();

		$i = 0;
		if(is_array($blogusers) && count($blogusers) > 0) {
			
			echo $this->before_widget;
			
			echo '<div id="wphyves_wwws_widget">';
			
			echo $this->before_title;
			
			echo '<span class="widgettitle">'.__('Hyves WWWs', 'wp-hyves').'</span>';
			
			echo $this->after_title;
			
			echo '<ul class="wphyves_wwws_list">';
			
			foreach($blogusers as $bu):

				setup_userdata($bu->user_id);

				global $user_url;
				global $user_ID;

				$id = get_usermeta($user_ID, 'hyves_userid');

				if($id != ''){ // show thumb.

					$cur_www = get_usermeta($bu->user_id, 'hyves_www');

					if('' != $cur_www && '@' != $cur_www) {

						$cur_www = wpHyvesStripShortcodes($cur_www); // remove hyves shortcodes.
						
						$cur_www = wpHyvesReplaceEmoticonTags($cur_www); // add emoticons.
						
						echo '<li>';
						echo '<p>';
						echo '<a href="'.$user_url.'" title="go to hyve" target="_blank">';
						echo '<img src="'.wpHyvesGetUploadPath(false, true) . $id . '-medium.jpg" alt="avatar" width="25" height="25" />';
						echo '</a>&nbsp;';
						
						echo '<span class="wphyves_username">' . $bu->user_login . ':</span>&nbsp;' . '<br/><span class="wphyves_cur_www">' . $cur_www . '</span>';
						echo '</p>';
						echo '</li>';

						$i++;
					}
				}

			endforeach;
			
			echo '</ul>';
			
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
				"title" => addslashes(__('Hyves WWWs', 'wp-hyves'))
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