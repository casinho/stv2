<?php

class UtCharacterFaceController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	public $files;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('@'),
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
	public function actionCreate()
	{
		$model=new UtCharacterFace;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['UtCharacterFace']))
		{
			$model->attributes=$_POST['UtCharacterFace'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->face_id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['UtCharacterFace']))
		{
			$model->attributes=$_POST['UtCharacterFace'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->face_id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('UtCharacterFace');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		
		$this->files = GFunctions::read_all_files(Yii::getPathOfAlias("webroot.images.skin.originals"));
		
		GFunctions::pre($_POST);
		
		if(isset($_POST['Skin'])) {
			foreach($_POST['Skin'] as $id => $img) {
				//$model = UtCharacterFace::model()->findByPk($id);
				//GFunctions::pre($model->attributes);
				UtCharacterFace::model()->updateByPk($id,array('image'=>$img));
			}
		}
		
		$model=new UtCharacterFace('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['UtCharacterFace']))
			$model->attributes=$_GET['UtCharacterFace'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	protected function gridDropDown($data,$row) {

		
		$files = array();
		foreach($this->files['files'] as $k => $v) {
			$files[$v] = $v;
		}
		
		return CHtml::dropDownList('Skin['.$data["face_id"].']',
									$data['image'],
									$files,
									array('prompt'=>Yii::t('user','bitte_waehlen'), 'class' => 'js_chosen', 'options' => array($data['image'] => array('selected' => 'selected')))
		);
		
		
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return UtCharacterFace the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=UtCharacterFace::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param UtCharacterFace $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='ut-character-face-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
