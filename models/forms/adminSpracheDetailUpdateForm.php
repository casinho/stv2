<?php
class adminSpracheDetailUpdateForm extends CFormModel {
	public $kategorie;
	public $version;
	public $key;
	public $value_de;
	public $value;
	
	public function rules() {
		return array(
			array('kategorie, version, key, value_de, value', 'required'),
		);
	}
}
?>
