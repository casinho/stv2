<?php
class adminTitelUndErfolgeVerknuepfenForm extends CFormModel {
	public $spielertitel;
	public $vereinserfolg;
	
	public function rules() {
		return array(
			array('spielertitel, vereinserfolg', 'required'),
		);
	}
}
?>
