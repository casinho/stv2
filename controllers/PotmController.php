<?php

class PotmController extends Controller
{
	
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
	public function accessRules()
	{
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('index','view','clans','member','maps','detail','squad','search'),
						'users'=>array('*'),
				),
				array('allow', // allow authenticated user to perform 'create' and 'update' actions
						'actions'=>array('create','update','verwalten','getSquadMember','addMap','delete','addSpieler','sort','edit'),
						'roles'=>array('NewsAdmin'),
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
	
	public function actionVerwalten() {
		$allePics = Potm::holeAllePics();
		$this->render('potmVerwalten', array('allePics' => $allePics));
	}	
	
	
	public function actionDetail($id) {
		
		$potm = Potm::Model()->with('autor')->findByPk($id);
		
		$this->render('detail',array('potm'=>$potm));
	}

	public function actionCreate()	{
	
		//Yii::app()->assetManager->forceCopy = true;
	
	
		$model=new Potm();
	
		$imageBig = null;
		
		$this->performAjaxValidation($model);
	
		$model->datum = Yii::app()->dateFormatter->format("dd.MM.yyyy",time());
		
		if(isset($_POST['Potm'])) {
	
			$model->name		= $_POST['Potm']['name'];
			$model->url			= $this->handlePotmUpload($_POST['Potm']['url']);
			$model->text		= $_POST['Potm']['text'];
			$model->aktiv		= $_POST['Potm']['aktiv'];
			$model->image_id	= $_POST['Potm']['image_id'];
			$model->user_id		= Yii::app()->user->getId();
			$model->datum		= new CDbExpression('NOW()');
	
			if($model->validate()) {
				$model->save(false);
				$this->redirect($model->getLink());
			}
			
		}
		$this->render('create', array(
				'model' => $model,
				'imageBig' => $imageBig,
				'aktion' => Yii::t('potm','potm_erstellen'),
				'size' => 'medium',
				'group' => 'potm',				
		));
	
	}	
	
	private function handlePotmUpload($image) {
		
		$memlimit = ini_get('memory_limit');
		ini_set('memory_limit', '512M');
		Yii::import('ext.image.Image');
		

		$myImage = substr($image,1);
		
		$saveImage 	= basename($myImage);
				
		$image = new Image($myImage);
		$image->quality(75);
		$image->save('images/potm/originals/'.$saveImage);

		/*
		$file = new Files();
		
		$file->file_name 	= $saveImage;
		$file->path 		= dirname($thumbnail);
		$file->extension 	= $info['extension'];
		$file->file_hash	= md5_file($thumbnail);
		$file->user_id		= Yii::app()->user->getId();
		$file->datum		= new CDbExpression('NOW()');
		$file->image_flag	= 1;
		if($file->validate()) {
			$file->save();
		}
		*/
		//GFunctions::deleteDir(dirname($thumbnail));
		ini_set('memory_limit', $memlimit);
		return $saveImage;
		
		
		
		$outputJs = Yii::app()->request->isAjaxRequest;		
	}
	
	public function actionUpdate($id)	{
	
		$model=$this->loadModel($id);
	
		$imageBig = null;
		
		$this->performAjaxValidation($model);
	
		$model->datum = Yii::app()->dateFormatter->format("dd.MM.yyyy",$model->datum);
		
		if(isset($_POST['Potm'])) {
	
			$model->name		= $_POST['Potm']['name'];
			if($_POST['Potm']['url_h']!=$_POST['Potm']['url']) {
				$model->url		= $this->handlePotmUpload($_POST['Potm']['url']);
			} else {
				$model->url		= $_POST['Potm']['url'];
			}
			$model->text		= $_POST['Potm']['text'];
			$model->aktiv		= $_POST['Potm']['aktiv'];
			$model->image_id	= $_POST['Potm']['image_id'];
			$model->user_id		= Yii::app()->user->getId();
			$model->datum		= Yii::app()->dateFormatter->format("yyyy-MM-dd",strtotime($_POST['datum']));
	
			if($model->validate()) {
				$model->save(false);
				$this->redirect($model->getLink());
			}
			
		}
		$this->render('create', array(
				'model' => $model,
				'imageBig' => $imageBig,
				'aktion' => Yii::t('potm','potm_bearbeiten'),
				'size' => 'medium',
				'group' => 'potm',				
		));
	}	

	public function actionAjaxCrop() {
		$news = News::model()->findByPk(Yii::app()->request->getParam('id'));
	
		Yii::import('ext.jcrop.EJCropper');
	
		$jcropper = new EJCropper();
		$jcropper->thumbPath = 'images/potm/originals';
	
		// get the image cropping coordinates (or implement your own method)
		$coords = $jcropper->getCoordsFromPost('imageId');
			
		// some settings ...
		$jcropper->targ_w = $coords['w'];
		$jcropper->targ_h = $coords['h'];
		$jcropper->jpeg_quality = 85;
		$jcropper->png_compression = 8;
	
		// returns the path of the cropped image, source must be an absolute path.
		$memlimit = ini_get('memory_limit');
		ini_set('memory_limit', '512M');
		$thumbnail = $jcropper->crop('images/potm/upload/'.$news->bild, $coords);
		ini_set('memory_limit', $memlimit);
	}	
	
	public function loadModel($id)
	{
		$model=Potm::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}	
	
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='potm-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	
	public function actionDelete($id) {
		$this->loadModel($id)->delete();
	
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) {
			//$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('verwalten'));
	
			$data['success'] = true;
	
			header('Content-Type: application/json');
			echo CJSON::encode($data);
			Yii::app()->end();
		}
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