<?php

class AnalyticsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters() {
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
				'actions'=>array('index','alphabet','detail','callback'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('create','update','delete','admin','view'),
				'roles'=>array('super-admin'),
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
		$model=new Quelle;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Quelle']))
		{
			$model->attributes=$_POST['Quelle'];
			if($model->save())
				$this->redirect(array('admin'));
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

		if(isset($_POST['Quelle']))
		{
			$model->attributes=$_POST['Quelle'];
			if($model->save())
				$this->redirect(array('admin'));
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
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	
	public function actionIndex() {

		$startdatum = date('Y-m-d', (time()-(60*60*24*7)));
		//$enddatum 	= date('Y-m-d', (time()-(60*60*24*1)));
		
		$googleAnalytics = StatistikGa::model()->findAll(array(
				'condition'=>'datum >= :startdatum AND datum < CURDATE()',
				'params'=>array(':startdatum'=>$startdatum),
				'order' => 'datum ASC',
				//'limit' => 7,
		));
		
		$avgData = array();
		$avgData['visits'] 				= 0;
		$avgData['pageviews']			= 0;
		$avgData['unique_pageviews']	= 0;
		$avgData['avgtimeonpage']		= 0;
		$avgData['entrancebouncerate']	= 0;
		$avgData['exitrate']			= 0;
		
		
		
		foreach($googleAnalytics as $k => $v) {
			$avgData['visits'] 				+= $v['visits'];
			$avgData['pageviews']			+= $v['pageviews'];
			$avgData['unique_pageviews']	+= $v['unique_pageviews'];
			$avgData['avgtimeonpage']		+= $v['avgtimeonpage'];
			$avgData['entrancebouncerate']	+= $v['entrancebouncerate'];
			$avgData['exitrate']			+= $v['exitrate'];
				
			$ts = GFunctions::macheTimeStamp($v['datum']);
			$timestamp = $ts - (60*60*24*7);
				
			$datum = date('Y-m-d',$timestamp);
				
			$attributes['datum'] = $datum;
				
			$data = StatistikGa::model()->findByAttributes($attributes);
			$googleAnalytics[$k]['vorwoche'] = $data['visits'];
			#echo $datum.': '.$googleAnalytics[$k]['vorwoche'].'<br />';
			#echo $v['datum'].': '.$v['visits'].'<br /><br />';
		}
		
		
		$avgData['avgtimeonpage']		= ($avgData['avgtimeonpage'] / 7);
		$avgData['entrancebouncerate']	= ($avgData['entrancebouncerate'] / 7);
		$avgData['exitrate']			= ($avgData['exitrate'] / 7);		
	

		
		$chartDataWoche = $this->getChartData('woche');
		
		$chartDataMonat = $this->getChartData('monat');
		//GFUnctions::pre($chartData);
		
		$this->render('index',array(
				'googleAnalytics' 	=> $googleAnalytics,
				'avgData'			=> $avgData,
				'chartDataWoche'	=> $chartDataWoche,
				'chartDataMonat'	=> $chartDataMonat,
			)
		);		
	}
	
	private function getChartData($case) {
		
		if($case == 'woche') {

			/* Graph data */
			
			$startdatum = date('Y-m-d', (time()-(60*60*24*7)));
			$enddatum 	= date('Y-m-d', (time()-(60*60*24*1)));
			
			//GFunctions::pre($enddatum);
			
			$gdata['aktuell'] = $this->getData($startdatum,$enddatum,$case);
			
			//GFunctions::pre($gdata);
			
			$startdatum = date('Y-m-d', (time()-(60*60*24*14)));
			$enddatum 	= date('Y-m-d', (time()-(60*60*24*8)));
			$gdata['vorherig'] = $this->getData($startdatum,$enddatum,$case);
			
			$titelData = array(
					array('Datum','Aktuelle Woche','Vorherige Woche'),
			);			
			
			foreach($gdata['aktuell'] as $k => $v) {
				$chartGData[$k][0] = Yii::app()->dateFormatter->formatDateTime($v->datum,'short',false);
				$chartGData[$k][1] = (int)$v->visits;
			}
			
			foreach($gdata['vorherig'] as $k => $v) {
				$chartGData[$k][2] = (int)$v->visits;
			}
				
		} elseif($case == 'monat') {
			
			$woche = date('W');
			
			$startdatum = $woche - 8;
			$enddatum 	= $woche;
			$gdata['aktuell'] = $this->getData($startdatum,$enddatum,$case);
				
			$startdatum = $woche - 17;
			$enddatum 	= $woche - 9;
			$gdata['vorherig'] = $this->getData($startdatum,$enddatum,$case);

			$titelData = array(
					array('Kalenderwoche','letzte 8 Wochen','davor'),
			);
				
			foreach($gdata['aktuell'] as $k => $v) {
				$chartGData[$k][0] = $v->week;
				$chartGData[$k][1] = (int)$v->visits;
				$chartGData[$k][2] = (int)0;
			}
				
			foreach($gdata['vorherig'] as $k => $v) {
				$chartGData[$k][2] = (int)$v->visits;
			}			
			
		}
		

		$output = array_merge($titelData,$chartGData);
		return $output;
		
	}
	
	private function getData($start,$ende,$case) {

		$startdatum = $start;
		$enddatum 	= $ende;		
		
		if($case == 'woche') {
			$googleAnalytics = StatistikGa::model()->findAll(array(
					'condition'=>'datum >= :startdatum AND datum <= :enddatum',
					'params'=>array(':startdatum'=>$startdatum,':enddatum'=>$enddatum),
					'order' => 'datum ASC',
					//'limit' => 7,
			));

		} elseif($case == 'monat') {
			$googleAnalytics = StatistikGa::model()->findAll(array(
					'select' => 'SUM(visits) AS visits,week',
					'condition'=>'week >= :startdatum AND week < :enddatum',
					'params'=>array(':startdatum'=>$startdatum,':enddatum'=>$enddatum),
					'group' => 'week',
					'order' => 'week ASC',
					//'limit' => 7,
			));
			
		}

		return $googleAnalytics;
		
	}
	
	private function formatDate($date) {
		$y = substr($date, 0, -4);  // gibt "abcde" zurück
		$m = substr($date, 4, -2);  // gibt "cde" zurück
		$d = substr($date, 6);  // gibt false zurück
		$datum = $y.'-'.$m.'-'.$d;
		return $datum;
	}	
	
	public function actionIndex2()
	{
    	//$service = Yii::app()->JGoogleAPI->getService('Drive');
		
    	$service = Yii::app()->JGoogleAPI->getService('Drive','webappAPI');
		GFunctions::pre($service);
    	 
    	 
        $jgoogleapi = Yii::app()->JGoogleAPI;
        GFunctions::pre($jgoogleapi);
 
    	try {
        	$client = $jgoogleapi->getClient();
   		
    		if(!isset(Yii::app()->session['auth_token'])) {
        		
        		//Get the instance of the client from the api
	            
	            //or
	            //$client = Yii::app()->JGoogleAPI->getClient();    #Without creating an extension instance            
	 
	            //Web Application User authentication
	            //You want to use a persistence layer like the DB or memcached to store the token for the current user
	            $client->authenticate();

	            //or
	            //$jgoogleapi->getClient()->authenticate();
	            //or
	            //Yii::app()->JGoogleAPI->getClient()->authenticate();
	 
	            Yii::app()->session['auth_token']=$client->getAccessToken();
 
	        } else {
	        	$client->setAccessToken(Yii::app()->session['auth_token']);
	            //List files from Google Drive
	            $files = $jgoogleapi->getService('Drive')->files->listFiles();
	            //Check the api documentation to see other ways to interact with api
	 
	            // We're not done yet. Remember to update the cached access token.
	            // Remember to replace $_SESSION with a real database or memcached.
	            Yii::app()->session['auth_token'] = $client->getAccessToken();
    	    }
	    }catch(Exception $exc) {
	        //Becarefull because the Exception you catch may not be from invalid token
	        Yii::app()->session['auth_token']=null;
	        throw $exc;
	    }  
	
	}

	/**
	 * Manages all models.
	 */
	public function actionCallback()
	{
					/**
		* @var apiPlusService $service
		*/
		$plus = Yii::app()->GoogleApis->serviceFactory('Plus');
		 
		/**
		* @var apiClient $client
		*/
		$client = Yii::app()->GoogleApis->client;
		
		 
		try {
			if(!isset(Yii::app()->session['auth_token']) || is_null(Yii::app()->session['auth_token'])) {
		    // You want to use a persistence layer like the DB for storing this along
		    // with the current user
		    	Yii::app()->session['auth_token'] = $client->authenticate();
		  	} else {
		    	$activities = '';
		    	$client->setAccessToken(Yii::app()->session['auth_token']);
		    	$activities = $plus->activities->listActivities('me', 'public');
		    	print 'Your Activities: <pre>' . print_r($activities, true) . '</pre>';
		  	}  
		} catch(Exception $e) {
		    // This needs some love as not every exception means that the token
		    // was invalidated
		    Yii::app()->session['auth_token'] = null;
		    GFunctions::pre($e);
		    throw $e;
		}	
		
		echo "hallo";
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Quelle::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='quelle-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	private function secondMinute($seconds) {
		$minResult = floor($seconds/60);
		if($minResult < 10){$minResult = 0 . $minResult;}
		$secResult = ($seconds/60 - $minResult)*60;
		if($secResult < 10){$secResult = 0 . round($secResult);}
		else { $secResult = round($secResult); }
		return $minResult.":".$secResult;
	}	
	
}
