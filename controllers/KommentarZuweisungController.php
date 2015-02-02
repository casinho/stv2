<?php

class KommentarZuweisungController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','delete','loeschen'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate() 	{
		$model = new KommentarZuweisung;

		$model->poster_ip 	= GFunctions::getIpAddress();
		$model->datum 		= new CDbExpression('NOW()');
		if(!Yii::app()->user->isGuest) {
			$model->name		= Yii::app()->user->name;
			$model->poster_id	= Yii::app()->user->getId();
		} else {
			$model->poster_id	= 0;
		}
		$model->poster_ip	= Yii::app()->request->getUserHostAddress();

		$this->performAjaxValidation($model);
		
		if(!empty($_POST['KommentarZuweisung'])) {
			sleep(1);
			if(Yii::app()->user->isGuest) {
				$model->name		= $_POST['KommentarZuweisung']['name'];
				$model->poster_id	= 0;
			} 
			$model->kommentar 	= $_POST['KommentarZuweisung']['kommentar'];
			$model->zuweisung 	= $_POST['KommentarZuweisung']['zuweisung'];
			$model->fremd_id 	= $_POST['KommentarZuweisung']['fremd_id'];		
			
			
			if($model->validate()) {
				$model->save(false);
				
				$response['status'] = 'succes';
				$response['msg'] = Yii::t('global','kommentar_gespeichert');
				//$response['msg'].= TbHtml::button(Yii::t('global','ok'),array('class'=>'btn','id'=>'okbtn'));
				
				header('Content-type: application/json');
				echo CJSON::encode($response);
				Yii::app()->end();					
			} 
		}		
		
		// Uncomment the following line if AJAX validation is needed
/*
		$this->performAjaxValidation($model);

		if(isset($_POST['KommentarZuweisung']))
		{
			$model->attributes=$_POST['KommentarZuweisung'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->kommentar_id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
*/		
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()	{
		
		$id = $_POST['kommentar_id'];
		
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$this->performAjaxValidation($model);
		
		if(!empty($_POST['KommentarZuweisung'])) {
			sleep(1);
			$model->kommentar 	= $_POST['KommentarZuweisung']['kommentar'];
			
			if($model->validate()) {
				$model->save(false);
				
				$response['status'] = 'success';
				$response['msg'] 	= Yii::t('global','kommentar_aktualisiert');
				$response['button'] = CHtml::button(Yii::t('global','ok'),array('class'=>'btn','data-dismiss'=>'modal'));
				
				header('Content-type: application/json');
				echo CJSON::encode($response);
				Yii::app()->end();					
			} 
		}	

		$this->renderPartial('_form',array('model'=>$model));
		
		
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionLoeschen() {
		
		if(isset($_POST['kommentar_id'])) {
			// we only allow deletion via POST request
			
			$id = $_POST['kommentar_id'];
			
			$this->loadModel($id)->delete();

			sleep(1);

			$data['status'] 	= 'success';
			$data['info'] 		= Yii::t('global','der_kommentar_wurde_geloescht');
			$data['button']		= TbHtml::button(Yii::t('global','ok'), array('data-dismiss' => 'modal'));
			
			header('Content-type: application/json');
			echo CJSON::encode($data);
			Yii::app()->end();
		} 

			
	}
	
	public function actionDelete()	{
		if(Yii::app()->request->isPostRequest) {

			$id = $_POST['kommentar_id'];
			
			echo CHtml::beginForm('','post',array('id'=>'deleteForm'));
			echo Yii::t('global','kommentar_wirklich_loeschen');
			//echo CHtml::tag('p',array(),$id);
			echo CHtml::hiddenField('kommentar_id',$id,array('id'=>'ajaxDeleteButton'));
			echo CHtml::endForm();
		//throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}	
	
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('KommentarZuweisung');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new KommentarZuweisung('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['KommentarZuweisung']))
			$model->attributes=$_GET['KommentarZuweisung'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=KommentarZuweisung::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='kommentar-zuweisung-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
