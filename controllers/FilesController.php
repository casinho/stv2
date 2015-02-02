<?php

class FilesController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/colums';

	/**
	 * @return array action filters
	 */

	public $maxHeight;
	public $maxWidth;
	public $jpegQualitaet = 85;
	public $pngKompression = 5;
	public $thumbnailPfad 	= 'files/news/gross/';
	public $uploadPfad 		= 'files/upload/';		
	
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
				'actions'=>array('create','update','upload','crop','cropZoom','handleCropZoom','cropSpeichern','showImage'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
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
	public function actionCreate() {
		
		$model=new Files;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if (isset($_POST['Files'])) {
			
			
			$rnd = rand(10000,99999);
			
			$uploadedFile=CUploadedFile::getInstance($model,'image');
			
			if ($model->save()) {
				$this->redirect(array('view','id'=>$model->file_id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionUpload() {
	

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValdation($model);
		
	
		$uploader = new FineUploadHandler();
		$uploader->allowedExtensions = array(); // all files types allowed by default
		
		// Specify max file size in bytes.
		$uploader->sizeLimit = 10 * 1024 * 1024; // default is 10 MiB
		
		// Specify the input name set in the javascript.
		$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
		
		// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
		$uploader->chunksFolder = "chunks";		
		
		$method = $_SERVER["REQUEST_METHOD"];
		if ($method == "POST") {
			//header("Content-Type: text/plain");
		
			// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
			$result = $uploader->handleUpload("files/upload/tmp");
		
			// To return a name used for uploaded file you can use the following line.
			$result["uploadName"] 	= $uploader->getUploadName();
			
			$result["imgWidth"] 	= $uploader->imgWidth;
			$result["imgHeight"] 	= $uploader->imgHeight;
			
			$result["imageUrl"]	= '/files/upload/tmp/'.$result['uuid'].'/'.$uploader->getUploadName();
		
			$result['success'] = "true";
			
			//echo json_encode($result);
			header('Content-type: application/json');
			echo CJSON::encode($result);
			Yii::app()->end();
				
		}
		
		else {
			header("HTTP/1.0 405 Method Not Allowed");
		}		
		Yii::app()->end();
	/*
		$this->render('create',array(
				'model'=>$model,
		));
		*/
	}
	public function actionCrop() {
		
		if(!empty($_POST['image'])) {
			$image 		= $_POST['image'];
			$verwendung = $this->getVerwendung($_POST['verwendung'],$_POST['width'],$_POST['height']);
		} else {
			$image = '/files/upload/tmp/24bcf55e-9fcc-44ec-b73a-bf4dee2c7c15/10245495_558654707584048_895727067392797002_n.jpg';
			
			$verwendung = array('',720,720);
		}

		if(isset($_POST['nocrop']) && $_POST['nocrop']=='true') {
			$nocrop = true;
		} else {
			$nocrop = false;
		}
		
		
		$outputJs = Yii::app()->request->isAjaxRequest;
		Yii::app()->clientScript->scriptMap=array('jquery.js'=>false,);
		$this->renderPartial('crop',array(
				'image' 	=> $image,
				'id'		=> 'news_big',
				'fotoame'	=> 'news_big_name',
				'art'		=> 'news_big_art',
				'verwendung'=> $verwendung,
				'nocrop'	=> $nocrop,
		),false,$outputJs);		
		
	}

	public function actionCropSpeichern() {
	
		$memlimit = ini_get('memory_limit');
		ini_set('memory_limit', '512M');
		Yii::import('ext.image.Image');
		
		/*
		$model = FotorechteSpieler::model()->findByPk(Yii::app()->request->getParam('foto_id'));
		if(!empty($model)) {
			if($model->nm_flag == 1) {
				FotorechteSpieler::model()->updateFotolisteNationalspieler($this->spieler->spieler_id);
			} else {
				FotorechteSpieler::model()->updateFotoliste($this->spieler->spieler_id);
				$model->aktiv = 'x';
			}
			$model->save();
			FotoUploadLog::model()->logPortrait($model,$this->spieler->spieler_id,'u');
		}*/
		Yii::import('ext.jcrop.EJCropper');
		
		$coords['x'] = $_POST['x1'];
		$coords['y'] = $_POST['y1'];
		
		$coords['w'] = $_POST['x2'] - $_POST['x1'];
		$coords['h'] = $_POST['y2'] - $_POST['y1'];
		
		//$coords['w'] = $_POST['width'];
		//$coords['h'] = $_POST['height'];
		
		
		// some settings ...
		$jcropper 					= new EJCropper();
		$jcropper->thumbPath 		= $this->thumbnailPfad;
		$jcropper->jpeg_quality 	= $this->jpegQualitaet;
		$jcropper->png_compression 	= $this->pngKompression;
		$jcropper->targ_w 			= $coords['w'];
		$jcropper->targ_h 			= $coords['h'];		
		
		$myImage = substr($_POST['image'],1); 

		$thumbnail = $jcropper->crop($myImage, $coords);
		
		$info = pathinfo($thumbnail);
		
		if($coords['w'] != $_POST['width'] || $coords['h'] != $_POST['height']) {
			
			$verwendung = $_POST['verwendung'];
			
			$saveImage = basename($thumbnail);
			
			$image = new Image($thumbnail);
			$image->resize($_POST['width'], $_POST['height']);
			$image->save('images/'.$verwendung.'/originals/'.$saveImage);
		}
		//$imageSize = getimagesize($_POST['image']);

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
                		
       	//GFunctions::deleteDir(dirname($thumbnail));
		
		ini_set('memory_limit', $memlimit);		
		
		
		$outputJs = Yii::app()->request->isAjaxRequest;
		
		// To return a name used for uploaded file you can use the following line.
		$result["img"] 		= $thumbnail;
		$result['status'] 	= "success";
		$result['file_id']  = $file->file_id;
		$result['file_name']= $saveImage;
			
		//echo json_encode($result);
		header('Content-type: application/json');
		echo CJSON::encode($result);
		Yii::app()->end();	
	}
	
	
	private function getVerwendung($verwendung,$oWidth,$oHeight) {
		switch($verwendung) {
			case 'news_big';
				$output['width'] 	= 550;
				$output['height'] 	= 240;
				$newWidth			= 220;
				$verwendung			= 'news';
				break;
			case 'potm';
				$output['width'] 	= 344;
				$output['height'] 	= 274;
				$newWidth			= 220;
				$verwendung			= 'potm';
				break;
			case 'maps';
				$output['width'] 	= 640;
				$output['height'] 	= 480;
				$newWidth			= 235;
				$verwendung			= 'maps';
				break;
			default:
				$output['width'] 	= 550;
				$output['height'] 	= 240;
				$newWidth			= 220;				
		}
		
		$aspect = $output['width'] / $output['height'];
		$newHeight	= round($newWidth / $aspect);	
		
		$output['verwendung']	 	= $verwendung;
		
		$output['previewWidth'] 	= $newWidth;
		$output['previewHeight'] 	= $newHeight;

		$output['oWidth'] 	= $oWidth;
		$output['oHeight'] 	= $oHeight;		
		
		$output['aspect'] = $aspect;
		
		return $output;
	}

	public function actionCreateImage() {
		$model=new Files;
	
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
	
		if (isset($_POST['Files'])) {
				
			if ($model->save()) {
				$this->redirect(array('view','id'=>$model->file_id));
			}
		}
	
		$this->render('create',array(
				'model'=>$model,
		));
	}	
	
	public function actionShowImage() {
		
		$image 	= $_POST['image'];
		$size 	= $_POST['size'];
		$group 	= $_POST['group'];
		
		$this->renderPartial('_imageView',array(
				'image' => basename($image),
				'size' => $size,
				'group' => $group,			
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

		if (isset($_POST['Files'])) {
			$model->attributes=$_POST['Files'];
			if ($model->save()) {
				$this->redirect(array('view','id'=>$model->file_id));
			}
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
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax'])) {
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
			}
		} else {
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Files');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Files('search');
		$model->unsetAttributes();  // clear any default values
		if (isset($_GET['Files'])) {
			$model->attributes=$_GET['Files'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Files the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Files::model()->findByPk($id);
		if ($model===null) {
			throw new CHttpException(404,'The requested page does not exist.');
		}
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Files $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if (isset($_POST['ajax']) && $_POST['ajax']==='files-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionCropZoom(){
		if(request()->getIsAjaxRequest()){
			//print_r($_POST);
			//die;
			Yii::import('ext.cropzoom.JCropZoom');
			$saveToFilePath = Yii::getPathOfAlias('webroot.assets').DIRECTORY_SEPARATOR .'cropZoomTest';
			JCropZoom::getHandler()->process($saveToFilePath,true)->process($saveToFilePath.'.jpeg');
			die($saveToFilePath);
		}
	
		$this->render('cropzoom');
	}
	
	/**
	 * @return void
	 */
	public function actionHandleCropZoom(){
		if(request()->getIsAjaxRequest()){
			//print_r($_POST);
			//die;
			Yii::import('ext.cropzoom.JCropZoom');
			$saveToFilePath = Yii::getPathOfAlias('webroot.assets').DIRECTORY_SEPARATOR .'cropZoomTest';
			JCropZoom::getHandler()->process($saveToFilePath,true)->process($saveToFilePath.'.jpeg');
			die('cropped file path : '.$saveToFilePath);
		}
	}	
	
}