<?php
/**
 * WPHyvesFrontEndAction.
 * @author dligthart
 * @version 0.1
 * @package wp-hyves
 */
class WPHyvesFrontEndAction extends WPHyvesWPPlugin{

	public function __construct($plugin_name, $plugin_base) {

	}

	function WPHyvesAdminAction($plugin_name, $plugin_base){
		$this->plugin_name = $plugin_name;
		$this->plugin_base = $plugin_base;
	}
}
?>
