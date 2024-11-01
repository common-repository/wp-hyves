<?php
/**
 * AdminConfigAction
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-hyves
 */
class WPHyvesAdminConfigAction extends WPHyvesWPPlugin{
	/**
	 * @var unknown_type
	 */
	var $adminConfigForm = null;

	/**
	 * @var unknown_type
	 */
	var $wpHyvesApi;

	/**
	 * Constructor.
	 * @param unknown_type $plugin_name
	 * @param unknown_type $plugin_base
	 * @param unknown_type $api
	 * @return unknown_type
	 */
	function WPHyvesAdminConfigAction($plugin_name, $plugin_base, $api){
		$this->plugin_name = $plugin_name;
		$this->plugin_base = $plugin_base;

		$this->adminConfigForm = new WPHyvesAdminConfigForm();

		$this->wpHyvesApi = $api;
	}

	/* (non-PHPdoc)
	 * @see wp-content/plugins/wp-hyves/classes/util/WPHyvesWPPlugin#render($ug_name, $ug_vars, $action)
	 */
	function render(){
		$this->render_admin('admin_config', array(
				'form'=>$this->adminConfigForm,
				'api'=>$this->wpHyvesApi,
				'plugin_base_url'=>$this->url(),
				'plugin_name'=>$this->plugin_name
			)
		);
	}
}