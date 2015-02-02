<?php

class YiiChatController extends Controller {
	
	public function filters() {
		return array(
				'accessControl', // perform access control for CRUD operations
				'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	public function actions() {
		return array(
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha'=>array(
						'class'=>'CCaptchaAction',
						'backColor'=>0xCCCCCC,
						'testLimit'=>5,
	
				),
				// page action renders "static" pages stored under 'protected/views/site/pages'
				// They can be accessed via: index.php?r=site/page&view=FileName
				'page'=>array(
						'class'=>'CViewAction',
				),
		);
	}
	
	public function actionAdd()	{
		
		
		if(Yii::app()->user->getId()==null) {
			$model = new ShoutForm('captchaRequired');
			$model->post_identity = -1;
		} else {
			$model = new ShoutForm();
			$model->post_identity 	= Yii::app()->user->getId();
			$model->owner 			= Yii::app()->user->name;
		}
		
		$data = 'not_used';
		
		if(isset($_POST['chat_id'])) {
			$model->chat_id 		= $_POST['chat_id'];
		}
		
		if(isset($_POST['ShoutForm'])) {
			
			try {
				$model->owner 			= $_POST['ShoutForm']['owner'];
				$model->created			= time();
				$model->text			= $_POST['ShoutForm']['text'];
				$model->chat_id 		= $_POST['ShoutForm']['chat_id'];
				$model->verifyCode 		= $_POST['ShoutForm']['verifyCode'];
				$model->data			= serialize($data);
				
				
				if($model->validate()) {
					
					$dbModel = new YiiChatPost();
					$dbModel->id				= time()+rand(10000,99999);
					$dbModel->chat_id 			= $model->chat_id;
					$dbModel->post_identity 	= $model->post_identity;
					$dbModel->owner 			= $model->owner;
					$dbModel->text 				= $model->text;
					$dbModel->created 			= $model->created;
					$dbModel->data				= $model->data;
					
					if(!$dbModel->validate()) {
						$error = CActiveForm::validate($model);
						if($error!='[]') {
							echo $error;
						}
						Yii::app()->end();						
					}
					
					
					$dbModel->save();
				 	
					//Yii::app()->session["lastShoutId"] = $dbModel->id;
					
					Yii::app()->session->add("captchaRequired", "true");
					header('Content-Type: application/json');
					echo CJSON::encode(array(
							'status'=>'success'
					));
					
					Yii::app()->end();
				} else{
					$error = CActiveForm::validate($model);
					if($error!='[]') {
						echo $error;
					}
					Yii::app()->end();
				}
	
			}catch(Exception $e) {
				GFunctions::pre($e->getMessage());
			}
		}
		//$this->layout = "//layouts/noneLayout";
		$this->renderPartial('add',array('model'=>$model),false,true);
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