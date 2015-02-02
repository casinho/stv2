<?php

class FileController extends Controller
{
	
	
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
		$model=new File;
		$model->setScenario('form');

		isset($_POST['myInput']) ? $myInput = $_POST['myInput'] : $myInput = 0;
		
		if(isset($_POST['term'])) {
			$model->name 		= $_POST['term'];
				
			$this->renderPartial('_form',array('model'=>$model,'myInput'=>$myInput));
			Yii::app()->end();
			/*
				if($model->save()) {
			header('Content-type: application/json');
			echo CJSON::encode($model);
			Yii::app()->end();
			}*/
		} elseif(isset($_POST['File'])) {
				
			$model->name 		= $_POST['File']['name'];
			$model->typ 		= 1;
			$model->poster_id	= Yii::app()->user->getId();
			$model->date		= new CDbExpression('NOW()');
				
			$valid=$model->validate();
			
			 
			
			if($valid){
				$model->save(false);
				$array = array('status'=>'success','id'=>$model->id,'text'=>$model->name,'myInput'=>$myInput);
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