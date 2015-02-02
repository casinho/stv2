<?php

class SiteController extends Controller
{
	public $layout='column2';

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
			'login.' =>  array('class'=>'application.components.loginProvider'),
			'yiichat'=>array('class'=>'YiiChatAction')
		);
	}


	public function actionDynamic() {
		GFunctions::pre($_POST);
		echo "hallo welt!";
	}
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionIndex()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}	
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionKontakt()
	{
		
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate()) {

				$message = new YiiMailMessage('ct.de: Kontaktanfrage'); 
				$message->view = 'kontakt'; 
				$message->setBody(array('model'=>$model), 'text/html'); 
				$message->addTo('carsten-tetzlaff@web.de'); 
				$message->from = $model->email; 
				Yii::app()->mail->send($message);
					
				Yii::app()->user->setFlash('contact','Danke für die Kontaktanfrage. Ich werde mich zeitnah zu Deinem Anliegen melden.');
				$this->refresh();				
			}
		}
		$this->render('kontakt',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}