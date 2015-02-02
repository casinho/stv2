<?php
class Kommentare extends CWidget {
	
	public $zuweisung;
	public $fremd_id;
	public $toggle = false;

	public function init() {
	}
	
	public function run() {
		
		$model = new KommentarZuweisung();
		
		$criteria = new CDbCriteria();
		$criteria->condition = 'zuweisung=:zuweisung AND fremd_id=:fremd_id';
		$criteria->params = array(':zuweisung' => $this->zuweisung, 'fremd_id' => $this->fremd_id);
		
		$kommentare = KommentarZuweisung::model()->findAll($criteria);
		
		$sort = new CSort();
		$sort->defaultOrder = 'datum DESC';
		
		$dataProvider = new CArrayDataProvider($kommentare,array(
			'keyField'	=> 'kommentar_id',
			'sort'		=> $sort,
 			'pagination'=>array(
        		'pageSize'=>5,
    		),		
		));
		
		$this->render('kommentare',array('kommentare'=>$dataProvider,'model'=>$model));
	}
	
	protected function performAjaxValidation($model) {
        if(isset($_POST['ajax']) && $_POST['ajax']==='kommentar-form') {
			$error = CActiveForm::validate($model);
            if($error!='[]') {
				echo $error;
              	Yii::app()->end();
	        }
        }
	}	
}
?>