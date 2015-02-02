<?php

class UtserverController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

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
				'actions'=>array('index','view','detail'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('create','update','verwalten','admin','delete'),
				'roles'=>array('Superadmin','ClanwarAdmin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionVerwalten() {
		$alleServer = Utserver::holeAlleServer();
		$this->render('serverVerwalten', array('alleServer' => $alleServer));
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

	public function actionDetail($id) {
		
		$model = $this->loadModel($id);
		
		if(is_file(Yii::getPathOfAlias('application').'/../downloads/server.xml')) {
			$xml = simplexml_load_file(Yii::getPathOfAlias('application').'/../downloads/server.xml');
			$qry = '/serverlist/server[@serverid="'.$id.'"]';
			$nodes = $xml->xpath($qry);
		} else {
			$xml = false;
			$nodes = false;
			throw new CHttpException(503,Yii::t('utserver','daten_nicht_aufrufbar_try_it_later'));
		}
		
		
		$this->render('detail',array(
				'model'=> $model,
				'nodes'=> $nodes,
		));
	}	
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()	{
	
		//Yii::app()->assetManager->forceCopy = true;
	
	
		$model=new Utserver();
	
		$this->performAjaxValidation($model);
	
		if(isset($_POST['Utserver'])) {
	
			$model->name		= $_POST['Utserver']['name'];
			$model->quick		= $_POST['Utserver']['quick'];
			$model->ip			= $_POST['Utserver']['ip'];
			$model->poster_id	= Yii::app()->user->getId();
			$model->port		= $_POST['Utserver']['port'];
	
			if($model->validate()) {
				$model->save(false);
				$this->redirect(Yii::app()->createUrl('utserver/verwalten'));
			}
		}
		
		$this->render('create', array(
				'model' => $model,
				'aktion' => Yii::t('utserver','server_erstellen'),
		));
	
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)	{
	
		$model=$this->loadModel($id);
	
		$this->performAjaxValidation($model);
	
			if(isset($_POST['Utserver'])) {
	
			$model->name		= $_POST['Utserver']['name'];
			$model->quick		= $_POST['Utserver']['quick'];
			$model->ip			= $_POST['Utserver']['ip'];
			$model->poster_id	= Yii::app()->user->getId();
			$model->port		= $_POST['Utserver']['port'];
	
			if($model->validate()) {
				$model->save(false);
				$this->redirect(Yii::app()->createUrl('utserver/verwalten'));
			}
		}
		
		
		$this->render('create', array(
			'model' => $model,
			'aktion' => Yii::t('utserver','server_bearbeiten'),
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
		#$dataProvider=new CActiveDataProvider('Utserver');
		
		$file = Yii::getPathOfAlias('application').'/../downloads/server.xml';
		
		if(is_file($file)) {
		
		
			$xml = simplexml_load_file($file);
	
			$server = array();
			$i = 0;
			foreach($xml->server as $k => $v) {
				#GFunctions::pre($v);
				
				$attr = $v[0]->attributes();
				$serverid = (int)$attr['serverid'];
				
				$server[$i]['serverid'] = $serverid;
				
				$attr = $v[0]->serverdata;
				
				$server[$i]['data'] = (string)$attr->data;
				$server[$i]['ip'] = (string)$attr->ip;
				$server[$i]['port'] = (string)$attr->port;
				
				$attr = $v[0]->match;
				
				$server[$i]['mapname'] = (string)$attr->mapname;
				$server[$i]['map_bild'] = (string)$attr->map_bild;
				$server[$i]['maxplayers'] = (int)$attr->maxplayers;
				$server[$i]['timelimit'] = (int)$attr->timelimit;
				$server[$i]['numplayers'] = (int)$attr->numplayers;
				$server[$i]['gametype'] = (string)$attr->gametype;			
				$server[$i]['gamemode'] = (string)$attr->gamemode;
				
				$i+=1;
			}
		} else {
			$server = array();
		}	

		$server = new CArrayDataProvider($server, array(
		  		'keyField'=>'serverid',
                   'sort'=>array(
						'defaultOrder' => 'numplayers DESC',                       
                   		'attributes'=>array(
                            'numplayers','mapname','data'
                       ),
                   ),
                   'pagination'=>array(
                       'pageSize'=>4,
                   ),
				
		));
		
		
		$this->render('index',array(
			'server'=>$server,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Utserver('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Utserver']))
			$model->attributes=$_GET['Utserver'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Utserver the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Utserver::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Utserver $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='utserver-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
