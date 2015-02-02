<?php
class cropForm extends CFormModel {

	public $cropID;
	public $cropX;
	public $cropY;
	public $cropW;
	public $cropH;
	
	public function rules() {
		return array(
			array('cropX,cropY,cropW,cropY', 'numerical'),
		);
	}
}

?>