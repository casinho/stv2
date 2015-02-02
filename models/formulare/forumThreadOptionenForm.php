<?php
class forumThreadOptionenForm extends CFormModel {
	
	public $sticky_flag;
	public $closed_flag;
	
	public $writeAsAdmin = false;
	
	public function rules() {
		return array(
			array('', 'required'),
			array('sticky_flag, closed_flag', 'length' ,'max'=> 1),
		);
	}

	
	public function attributeLabels() {
		return array(
			'sticky_flag' => Yii::t('forum','oben_festpinnen'),
			'closed_flag' => Yii::t('forum','thread_schliessen'),
			'is_archiv' => Yii::t('forum','thread_archivieren'),
			'writeAsAdmin' => Yii::t('forum','als_admin_schreiben'),
			'postcounter' => Yii::t('forum','postcounter'),
			'closed_type' => 'ClosedType',
		);
	}	
	
	public function convertToBooleans() {
		$attributes = array('sticky_flag', 'closed_flag', 'writeAsAdmin');

		foreach ($attributes as $attr) {
			$this->$attr = ($this->$attr == 'x') ? true : false;
		}		
		
	}
	
	public function convertToStrings() {
		$attributes = array('sticky_flag', 'closed_flag', 'writeAsAdmin');
		foreach($_POST['forumThreadOptionenForm'] as $attr => $value) {
			$this->$attr = ($value == '1') ? 1 : 0;
		}		
	}
	
	public function convertAttributeToBoolean($attr,$value='x') {
		$this->$attr = ($this->$attr == $value) ? true : false;
	}	
	
	
	
}
?>
