<?php
class maKommentarForm extends CFormModel {
	
	public $is_sticky;
	public $is_closed;
	public $is_archiv;
	
	public $closed_type;
	public $writeAsAdmin = false;
	
	public $postcounter;
	
	public function rules() {
		return array(
			array('', 'required'),
			array('is_sticky, is_closed, is_archiv', 'length' ,'max'=> 1),
		);
	}

	
	public function attributeLabels() {
		return array(
			'is_sticky' => Yii::t('forum','oben_festpinnen'),
			'is_closed' => Yii::t('forum','thread_schliessen'),
			'is_archiv' => Yii::t('forum','thread_archivieren'),
			'writeAsAdmin' => Yii::t('forum','als_admin_schreiben'),
			'postcounter' => Yii::t('forum','postcounter'),
			'closed_type' => 'ClosedType',
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
