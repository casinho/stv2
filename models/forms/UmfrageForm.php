<?php
class UmfrageForm extends CFormModel {
	
	public $is_multiple = 1;
	public $is_closed = '';
	public $bereits_abgestimmt = false;	
	
	public function rules() {
		return array(
			array('', 'required'),
			array('is_sticky, is_closed, is_archiv', 'length' ,'max'=> 1),
		);
	}

	
	public function attributeLabels() {
		return array(
			'is_multiple' => Yii::t('forum','mehrere_antwortoptionen_moeglich'),
			'is_closed' => Yii::t('forum','thread_schliessen'),
		);
	}	
	
	public function convertToBooleans() {
		$attributes = array('is_sticky', 'is_closed', 'is_archiv', 'postcounter', 'writeAsAdmin');

		foreach ($attributes as $attr) {
			$this->$attr = ($this->$attr == 'x') ? true : false;
		}		
		
	}
	
	public function convertToStrings() {
		$attributes = array('is_sticky', 'is_closed', 'is_archiv', 'postcounter', 'writeAsAdmin');
		foreach($_POST['forumThreadOptionenForm'] as $attr => $value) {
			$this->$attr = ($value == '1') ? 'x' : '';
		}		
	}
	
	public function convertAttributeToBoolean($attr,$value='x') {
		$this->$attr = ($this->$attr == $value) ? true : false;
	}	
	
	
	
}
?>
