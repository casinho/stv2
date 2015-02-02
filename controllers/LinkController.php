<?php

class LinkController extends Controller {
	
	public function filters() {
		return array(
				'accessControl', // perform access control for CRUD operations
				'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	public function accessRules() {
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('index','view','detail','search'),
						'users'=>array('*'),
				),
				array('allow',
						'actions'=>array('add'),
						'users'=>array('@'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}	
	
	public function actionAdd() {
		$model=new Link;
		$model->setScenario('form');
		if(isset($_POST['term'])) {
			$model->text 		= $_POST['term'];
			$model->typ 		= 2;
			$model->poster_id 	= Yii::app()->user->getId();
			$model->land_id 	= Yii::app()->params['unbekannt_land_id'];
			
			$this->renderPartial('_form',array('model'=>$model));
			Yii::app()->end();
			/*
			if($model->save()) {
				header('Content-type: application/json');  			
				echo CJSON::encode($model);
				Yii::app()->end();
			}*/
		} elseif(isset($_POST['Link'])) {
			
			$model->text 		= $_POST['Link']['text'];
			$model->link 		= $_POST['Link']['link'];
			$model->typ 		= 2;
			$model->tag 		= $_POST['Link']['tag'];
			$model->poster_id 	= Yii::app()->user->getId();
			$model->land_id 	= $_POST['Link']['land_id'];			
			
			$valid=$model->validate();
			if($valid){
				$model->save(false);
				$array = array('status'=>'success','id'=>$model->id,'text'=>$model->text);
				echo CJSON::encode($array);
				Yii::app()->end();
			} else{
				$error = CActiveForm::validate($model);
				if($error!='[]') {
					echo $error;
				} 
				Yii::app()->end();
			}			
		}

		
		
	}	
	
	
	public function actionSearch($id, $index) {
		
		$string = trim($id);
		
		
		$retval = array();
	
		if($index == 'id') {
			//$result = Yii::app()->db->createCommand("SELECT l.text,l.id FROM link AS l INNER JOIN clanwars AS c ON c.liga_id = l.id WHERE l.text LIKE :match OR l.tag LIKE :match GROUP BY l.id")->bindValue(':match',"%".$q."%")->queryAll();
			$result = Yii::app()->db->createCommand("SELECT l.text,l.id FROM link AS l WHERE l.id = :id")->bindValue(':id',$id)->queryRow();
			if(!empty($result)) {
				$retval= array('id' => $result['id'], 'name' => $result['text']);
			}
			
		}
			
		header('Content-Type: application/json');
		echo CJSON::encode($retval);
		Yii::app()->end();
	}
	
	

	public function actionDelete()
	{
		$this->render('delete');
	}

	public function actionEdit()
	{
		$this->render('edit');
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}