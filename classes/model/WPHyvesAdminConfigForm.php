<?php
/**
 * WPHyvesAdminConfigForm model object.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-hyves
 */
include_once('WPHyvesBaseForm.php');
class WPHyvesAdminConfigForm extends WPHyvesBaseForm{
	var $wphyves_key;
	var $wphyves_secret;

	public function __construct() {
		$this->WPHyvesAdminConfigForm();
	}

	function WPHyvesAdminConfigForm(){
		parent::WPHyvesBaseForm();
		if($this->setFormValues()){
			$this->saveOptions();
		}
		$this->loadOptions();
	}

	function getKey(){
		return $this->wphyves_key;
	}

	function getSecret(){
		return $this->wphyves_secret;
	}

	function setKey($key = '') {
		$this->wphyves_key = $key;
	}

	function setSecret($secret = '') {
		$this->wphyves_secret = $secret;
	}
}
?>