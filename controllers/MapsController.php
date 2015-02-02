<?php

class MapsController extends Controller
{
	
	public $wertungenTyp = array(array('id'=>3,'typ' => 'Sieg'), array('id'=>2,'typ' => 'Niedelagen'), array('id'=>1,'typ' => 'Unentschieden'));
	
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
		$alleMaps = File::holeAlleMaps();
		$this->render('mapsVerwalten', array('alleMaps' => $alleMaps));
	}	
	
	
	
	
	public function actionDetail($id) {
		
		$map = File::model()->with()->findByPk($id);
		
		$alleMaps = Clanwars::holeSquadMaps($id);
		
		$squadData = $this->chartStatistik($id);
		
		$this->render('detail',array('map'=>$map,'alleMaps'=>$alleMaps,'squadData'=>$squadData));
	}

	
	private function chartStatistik($map_id = 0) {
	
		$criteria = new CDbCriteria();

		$sql = "SELECT s.*,m2c.* FROM map2clanwar AS m2c INNER JOIN clanwars AS c ON c.id = m2c.clanwar_id INNER JOIN squad AS s ON s.squad_id = c.squad_id WHERE m2c.wertung > 0 AND m2c.map_id = ".$map_id." GROUP BY c.squad_id";

		$squads = Yii::app()->db->createCommand($sql)->queryAll();

		$squadData 	= array();
	
		$squadRow = array();
		$squadRow[0][] = 'Squad';
	
		$i = 0;
	
		foreach($squads as $k => $v) {
	
			$squadData[$k] = $v;
	
			$squadVal[$k][$i] = $v['squad_name'];
	
			foreach($this->wertungenTyp as $kk => $vv) {
	
				$i+=1;
	
				$typus = Clanwars::getClanwarStatus($vv['id']);
				if(!in_array($typus,$squadRow[0])) {
					$squadRow[0][] = $typus;
				}
	
				$sql = "SELECT COUNT(*) FROM clanwars AS c INNER JOIN map2clanwar AS m2c ON m2c.clanwar_id = c.id WHERE c.squad_id = ".$v['squad_id']." AND m2c.wertung = ".$vv['id']." AND m2c.map_id = ".$map_id." GROUP BY m2c.map_id";
				$squadVal[$k][$i] = (int)Yii::app()->db->createCommand($sql)->queryScalar();
			}
	
			$i=0;
		}
	
		$data = array_merge($squadRow,$squadVal);
		
		return $data;
	}	
	
	public function actionCreate()	{
	
		//Yii::app()->assetManager->forceCopy = true;
	
	
		$model=new File();
		$model->typ = 1;
	
		$imageBig = null;
		
		$this->performAjaxValidation($model);
	
		$maps = $this->getMaps();
		
		$model->date = Yii::app()->dateFormatter->format("dd.MM.yyyy",time());
		
		if(isset($_POST['File'])) {
	
			$model->name		= $_POST['File']['name'];
			if(!empty($_POST['File']['bildurl'])) {
				$model->bild = $maps[$_POST['File']['bildurl']];
			} else {
				$model->bild		= $this->handleMapsUpload($_POST['File']['bild']);
			}
			$model->coment		= $_POST['File']['coment'];
			$model->show_it		= $_POST['File']['show_it'];
			//$model->image_id	= $_POST['Potm']['image_id'];
			$model->poster_id		= Yii::app()->user->getId();
			$model->date		= new CDbExpression('NOW()');
	
			if($model->validate()) {
				$model->save(false);
				$this->redirect($model->getLink());
			}
			
		}
		$this->render('create', array(
				'model' => $model,
				'imageBig' => $imageBig,
				'aktion' => Yii::t('maps','map_erstellen'),
				'size' => 'medium',
				'group' => 'maps',				
		));
	
	}	
	
	private function handleMapsUpload($image) {

		
		$memlimit = ini_get('memory_limit');
		ini_set('memory_limit', '512M');
		Yii::import('ext.image.Image');
		

		$myImage = substr($image,1);
		
		$saveImage 	= basename($myImage);
				
		$image = new Image($myImage);
		$image->quality(75);
		$image->save('images/maps/originals/'.$saveImage);

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
	
		if($model->date !=0) {
			$model->date = Yii::app()->dateFormatter->format("dd.MM.yyyy",$model->date);
		} else {
			$model->date = Yii::app()->dateFormatter->format("dd.MM.yyyy",time());
		}
		
		$maps = $this->getMaps();
		
		if(isset($_POST['File'])) {
		
			$model->name		= $_POST['File']['name'];
			
			if(!empty($_POST['File']['bildurl'])) {
				$model->bild = $maps[$_POST['File']['bildurl']];
			} else {
				if($_POST['File']['bild_h']!=$_POST['File']['bild']) {
					$model->bild		= $this->handleMapsUpload($_POST['File']['bild']);
				} else {
					$model->bild		= $_POST['File']['bild'];
				}
			}			
			
			$model->coment		= $_POST['File']['coment'];
			$model->show_it		= $_POST['File']['show_it'];
			//$model->image_id	= $_POST['Potm']['image_id'];
			$model->poster_id		= Yii::app()->user->getId();
			$model->date		= new CDbExpression('NOW()');
		
			if($model->validate()) {
				$model->save(false);
				$this->redirect($model->getLink());
			} else {
				print_r($model->getErrors());
			}
				
		}		

		$this->render('create', array(
				'model' => $model,
				'imageBig' => $imageBig,
				'aktion' => Yii::t('potm','potm_bearbeiten'),
				'size' => 'medium',
				'group' => 'potm',
				'maps' => $maps,				
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
		$model=File::model()->findByPk($id);
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
	
	
	private function getMaps() {
		
		$dir = $_SERVER["DOCUMENT_ROOT"].'/images/maps/originals';
		
		$handle=opendir($dir);
		
		$fileListOfDirectory = array ();
		while ($datei = readdir($handle)) {
			if($datei != '.' && $datei != '..') {
				$fileListOfDirectory[] = $datei;
			}
		}
		
		return $fileListOfDirectory;
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