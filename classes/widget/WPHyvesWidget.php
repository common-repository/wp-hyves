<?php
/**
 * WPHyvesWidget.
 * @version 0.1
 * @author dligthart
 * @package wp-hyves
 */
class WPHyvesWidget extends WP_Widget{

	var $title = '';
	var $options = array();
	var $before_widget;
    var $before_title;
	var $after_title; 
    var $after_widget;
	

	public function __construct() {
		$this->WPHyvesWidget();
	}

	function WPHyvesWidget() {

	}

	/**
	 * Register widget.
	 * @param string $title
	 * @access protected
	 */
	function register() {
		if(function_exists('register_sidebar_widget') ) {
			register_sidebar_widget($this->title, array(&$this, 'widgetize'));
		}
	}

	/**
	 * Override.
	 */
	function widgetize() {

	}

	/**
	 * Init widget.
	 * @access protected
	 */
	function initWidget($title = '') {
		if($this->title == '') {
			$this->title = $title;
		}
		add_action('init', array(&$this, 'register'));
	}

	/**
	 * Set title.
	 * @param string $title
	 * @access protected
	 */
	function setTitle($title = '') {
		$this->title = $title;
	}

	function getOptions() {

	}
}
?>