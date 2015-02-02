<?php
class adminSpracheDetailUpdateVersionForm extends CFormModel {
	public $version;
	
	public function rules() {
		return array(
			array('version', 'required'),
		);
	}
}
?>
