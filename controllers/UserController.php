<?php

class UserController extends Controller
{

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
	
	public function actions()
	{
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

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
	    return array(
	            array('allow',
	                    'actions'=>array('detail','login','register','captcha','validate','logout','loggedIn','logoutForm','passwort','passwortVergessen','passwortLinkVerschickt','profil','mailTest','Switchback','neuesPasswortAnfordern'),
	                    'users'=>array('*'),
	            ),
	    		array('deny',
	    				'actions'=>array('login','register','dynamicSkin','dynamicFace','dynamicImage'),
	    				'users'=>array('Freigeschaltet'),
	    		),	    		
	    		array('allow',
	    				'actions' =>array('verwalten','getStatuses','updateUser','updateSquads','getActivities','getSquads','getUserSquads','loeschen'),
	    				'roles'=>array('UserAdmin','Superadmin'),
	    		),
	            array('allow',
	                    'actions'=>array('index', 'update', 'admin', 'view', 'delete', 'create','dynamicSkin','dynamicFace','dynamicImage'),
	                    //'roles'=>array('admin'),
	            		'roles'=>array('UserAdmin','Superadmin'),
	            ),
	    		array('allow',
	    				'actions'=>array('Switchuser'),
	    				//'roles'=>array('admin'),
	    				'roles'=>array('Superadmin'),
	    		),	    		
	            array('deny',  // deny all users
	                    'users'=>array('*'),
	            ),
	    );
	}

	public function actionLogin() {
		$this->breadcrumbs = array('Login');
		
	    $model=new LoginForm;

	    if(Yii::app()->request->isAjaxRequest) {
			
	    	if(isset($_POST['LoginForm'])) 	{
	    		$model->attributes=$_POST['LoginForm'];
	    		$valid=$model->validate();
	    		if($model->validate() && $model->login()){
/*
	    			if(isset($_POST['url'])) {
	    				 echo json_encode(array('redirect'=>$_POST['url']));
	    			} else {
	    				echo CJSON::encode(array(
	    						'status'=>'success'
	    				));
	    				Yii::app()->end();
	    			}*/
	    			if(isset($_POST['url']) && strpos($_POST['url'],'login')===false && strpos($_POST['url'],'logout')===true) {
	    				$array = array('status'=>'success','url'=>$_POST['url']);
	    			} else {
	    				$array = array('status'=>'success');
	    			}
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

		if(isset($_POST['LoginForm'])) {
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid (ct)
			if($model->validate() && $model->login()) {
				$this->redirect(Yii::app()->user->returnUrl);
			}
		}
		// Wenn ajaxrequest-lade loginbox in den HEader, ansonsten normaler Seite (ct)
		if(Yii::app()->request->isAjaxRequest) {
			$this->renderPartial('login',array('model'=>$model), false, true);
		} else {
			$this->render('login',array('model'=>$model));
		}
	}
	
	public function actionLoggedIn() {
		$this->renderPartial('_loggedIn');
	}
	
	
	public function actionRegister() {

		$model   			= new RegisterForm;
	    $newUser 			= new User;
	    $User2Validierung 	= new User2Validierung;
	    
    	if(isset($_POST['RegisterForm'])) {
    		$model->attributes=$_POST['RegisterForm'];
	    		
    		if($model->validate()){
    			
    			
    			$newUser->user_nick     				= $model->user_nick;
    			$newUser->user_pwd						= $newUser->hashPassword($model->passwort1);
    			//$newUser->vorname						= $model->vorname;
    			//$newUser->nachname					= $model->nachname;
    			$newUser->email 						= $model->email;
    			/*
    			$newUser->datenschutzerklaerung_flag	= $model->datenschutzerklaerung_flag; 
    			*/
    			$newUser->nutzungsbedingungen_flag		= $model->nutzungsbedingungen_flag;
    			 
    			$newUser->datum_registriert 			= date('Y-m-d H:i:s');
    			$newUser->sprache	 					= Yii::app()->params->language;
    			$newUser->letzte_ip						= GFunctions::getIpAddress();
    			
    			$User2Validierung->user_mail				= $newUser->email;
    			$User2Validierung->datum_angefordert		= date('Y-m-d H:i:s');
    			$User2Validierung->daten					= '';
    			
    			$User2Validierung->user_ip_angefordert		= GFunctions::getIpAddress();
    			$User2Validierung->validierungs_typ			= 'account_aktivierung';
    			$User2Validierung->validierungs_schluessel	= GFunctions::createRandomString(2);	    			
	    			
    			try {
    				/*
    				 * Ich mag Transaktionen! (ct)
    				 * -> Erst wenn User valide ist, können die Validerungsdaten gespeichert werden
    				 */
    				if($newUser->validate()) {
    					$transaction = Yii::app()->db->beginTransaction();
    				
    				
    				
	    				if($newUser->save()) {
	    					Yii::app()->user->setFlash('error', $newUser->errors);
	    				}
    				
    				
	    				$User2Validierung->user_id				= $newUser->user_id;
	    				$User2Validierung->user_id_angefordert	= $newUser->user_id;
	    				
	    				if($User2Validierung->validate()) {
	    					$User2Validierung->save();
	    				} else {
	    					die('yyy');	    					
	    				}
	    				
	    			// Wenn ajaxrequest-lade registerbox in den HEader, ansonsten normaler Seite (ct)
	    				try {
	    					$message = new YiiMailMessage(Yii::app()->params['clan'].': '.Yii::t('user', 'registrierung'));
	    					$message->view = 'registrierung';
	    					$message->setBody(array('userModel'=>$newUser,'validierung'=>$User2Validierung,'host'=>Yii::app()->request->getHostInfo(),'projekt'=>Yii::app()->params['clan']), 'text/html');
	    					$message->setTo(array($newUser->email=>$newUser->email));
	    					$message->setFrom(array(Yii::app()->params['noReplyMail'] => Yii::app()->params['clan']));
	    					Yii::app()->mail->send($message);
	    					
	    				} catch(Exception $y) {
	    					GFunctions::pre($y->getMessage());
	    					die('xxxxx');
	    				}    			    				
	    				
	    				Yii::app()->user->setFlash('registrieren',Yii::t('user', 'registrierung_erfolgreich'));
	    				$transaction->commit();
    				} else {
    					#GFunctions::pre($newUser->getErrors());
    					#die('xxx');    					
    				}    				
    			} catch (Exception $e)	{

    				#GFunctions::pre($e->getMessage());
    				#die('ooo');
    				
    				$transaction->rollback();
    				$error = CActiveForm::validate($model,$newUser,$User2Validierung);
    				if($error!='[]') {
    					echo $error;
    				}
    			}	
    		} else {
    			#GFunctions::pre($model->getErrors());
    			#die();
    		}	
    			    		
	    }	    
		$this->render('registrieren', array('model'=>$model));
	}	
	
	public function actionMailTest() {
		
		
		if(!function_exists("fsockopen")) {
			echo "Function exists!";
		} else {
			echo "Function doesn't exist!";
		}
				
		$newUser = User::model()->findByPk(2);
		
		$nachricht = "tesertaewr";
		

		
		try {

			#mail('darth@santitan.de', 'TESTEST', $nachricht);
			#mail('carsten-tetzlaff@web.de', 'TESTEST', $nachricht);
		
			$message = new YiiMailMessage(Yii::app()->params['clan'].': '.Yii::t('user', 'registrierung'));
			$message->view = 'mailtest';
			$message->setBody(array('userModel'=>$newUser), 'text/html');
			$message->setTo(array($newUser->email=>$newUser->email));
			$message->setFrom(array(Yii::app()->params['noReplyMail'] => Yii::app()->params['clan']));
	
			try { 
				Yii::app()->mail->send($message); 
			} catch (Exception $e) { 
				GFunctions::pre($e); 
			}
			
			
	
			
		} catch(Exception $y) {
			GFunctions::pre($y->getMessage());
			die('xxxxx');
		}		
	}
	
	public function actionLoeschen() {
	
		$deleteForm = new ProfilDeleteForm;
	
		if(isset($_POST['ProfilDeleteForm'])){
			if(isset($_POST['ProfilDeleteForm']['delete']) && $_POST['ProfilDeleteForm']['delete']==1) {
				$deleteForm->delete = $_POST['ProfilDeleteForm'];
	
				if($deleteForm->validate()) {
	
					$loeschung	= new GUserGeloescht();
					$loeschung->user_id 	= Yii::app()->user->getId();
					$loeschung->user_nick 	= Yii::app()->user->name;
					$loeschung->user_mail 	= User::model()->findByPk(Yii::app()->user->getId())->user_mail;
					$loeschung->user_ip		= Yii::app()->request->userHostAddress;
	
					$loeschung->save();
					
					if(!empty($_POST['ProfilDeleteForm']['loeschgrund']) || !empty($_POST['ProfilDeleteForm']['grund_id']))  {
	
						$statistik	= new GUserGeloeschtStatistik();
	
						if(!empty($_POST['ProfilDeleteForm']['grund_id'])) {
							$statistik->grund_id 	= $_POST['ProfilDeleteForm']['grund_id'];
						}
						if(!empty($_POST['ProfilDeleteForm']['grund_id'])) {
							$statistik->grund 		= $_POST['ProfilDeleteForm']['loeschgrund'];
						}
						$statistik->save();
					}
					
	
					$response['success'] = true;
					$response['message'] = Yii::t('member','account_geloescht');
	
					/*
					 * 	NOT WORKING: User::model()->deleteByPk(Yii::app()->user->getId());
					*  INFO: bei ->deleteByPk ist ein Aufruf von afterDelete nicht mÃ¶glich,
					*  deswegen wird das Object erstellt und dann gelÃ¶scht (ct)
					*/
	
					header('Content-type:application/json');
					echo CJSON::encode($response);
	
					Yii::app()->user->logout();
					Yii::app()->end();
				} else {
					TMFunctions::pre($deleteForm->getErrors());
					$error = CActiveForm::validate($deleteForm);
					if($error!='[]') {
						echo $error;
					}
					Yii::app()->end();
				}
			}
		}
	}
	
	
	public function actionVerwalten() {
		
		$criteria = new CDbCriteria();
		
		
		if(isset($_POST['usersuche']) && !empty($_POST['usersuche'])) {
			$criteria->condition = 'user_nick LIKE :usersuche';
			$criteria->params = array(':usersuche'=>'%'.$_POST['usersuche'].'%');
			$criteria->order = 'user_nick ASC';
			$info = Yii::t('user','suchergebnis_str',array('{str}'=>$_POST['usersuche']));
		} else {
			$criteria->order = 'user_id DESC';
			$info = '';
		}
		
		$alleUser = User::model()->findAll($criteria);
		
		$squads = $this->getSquads();

		$this->render('userVerwalten', array('alleUser' => $alleUser,'squads'=>$squads,'info'=>$info));
	}
	
	
	public function actionGetStatuses() {
		$output = User::getUserFlags();
		echo json_encode($output);		
	}

	public function actionGetSquads() {

		$criteria = new CDbCriteria();
		$criteria->condition = 'st_flag =:st_flag';
		$criteria->params = array(':st_flag'=>1);
		
		$squads = Squad::model()->findAll($criteria);
		
		$output = array();
		foreach($squads as $k => $v) {
			$output[] = array('id' => $v->squad_id, 'text' => $v->squad_name);
		}
		
		echo json_encode($output);
	}

	public function actionGetUserSquads($id) {
		$model = User::model()->findByPk($id);
		$squads = $model->getSquads();	
		$j['out'] = array();
		$j['tag'] = array();
		$j['ids'] = array();
		if($squads != null) {
			foreach($squads as $k => $v) {
				$j['tag'][] = $v->squad->squad_tag;
				$j['ids'][] = $v->squad_id;
				$j['out'][] = array('id'=>$v->squad_id,'text'=>$v->squad->squad_tag);
			}
		}
		
		if(empty($j['tag'])) {
			$j['tag'][] = 'Click to edit';
		} 
		echo CJSON::encode($j);
		Yii::app()->end();
	}	
	
	public function getSquads() {
		$criteria = new CDbCriteria();
		$criteria->condition = 'st_flag =:st_flag';
		$criteria->params = array(':st_flag'=>1);
		
		$squads = Squad::model()->findAll($criteria);
		
		$output = array();
		foreach($squads as $k => $v) {
			$output[] = array('id' => $v->squad_id, 'text' => $v->squad_name);
		}
		return $output;		
	}
	
	protected function gridSquads($data,$row) {
		$squads = $this->getSquads();
		return $this->renderPartial('_gridSquads',array('data'=>$data,'squads'=>$squads),true);
	}
	
	
	public function actionGetActivities() {
		$output = User::getUserActivities();
		echo json_encode($output);
	}	

	public function actionUpdateUser() {
		if(!empty($_POST)) {
			User::model()->updateByPk($_POST['pk'], array($_POST['name'] => $_POST['value']));
		}
	}
	

	public function actionUpdateSquads() {
		if(!empty($_POST)) {
			
			if(!empty($_POST)) {
					
				$oldSquadData = User::model()->findByPk($_POST['pk'])->getSquads();
				
				$oldSquads = array();
				foreach($oldSquadData as $k => $v) {
					$oldSquads[] = $v->squad_id; 
				}
				
				if(isset($_POST['value'])) {
					$newSquads = $_POST['value'];
				} else {
					$newSquads = array();
				}
				
				$attributes = array();
				
				$attributes['user_id'] = $_POST['pk'];
				
				if(!empty($oldSquads)) {
					
					if(empty($newSquads)) {
						User2Squad::model()->deleteAllByAttributes($attributes);
					} else {

						foreach($oldSquads as $k => $v) {
							if(!in_array($v,$newSquads)) {
								$attributes['squad_id'] = $v;
								User2Squad::model()->deleteAllByAttributes($attributes);
								unset($oldSquads[$k]);
							}
						}
						
						foreach($newSquads as $k => $v) {
							if(!in_array($v,$oldSquads)) {
								$attributes['squad_id'] = $v;
								
								$model = new User2Squad();
								$model->attributes = $attributes;
								$model->save(false);
							}
						}
						
					}
				} else {
					foreach($newSquads as $k => $v) {
						$attributes['squad_id'] = $v;
						$model = new User2Squad();
						$model->attributes = $attributes;
						$model->save(false);
					}					
				}

				//User::model()->updateByPk($_POST['pk'], array($_POST['name'] => $_POST['value']));
			}
		}
	}
	
	public function actionSwitchuser($id) {
		
		if(Yii::app()->user->checkAccess('Superadmin')) {
			
			$user = User::model()->findByPk($id);
			
			$identity = new SwitchIdentity($user->user_id, $user->user_nick);
			$identity->setState('oldIdentity', array('id' => Yii::app()->user->id, 'name' => Yii::app()->user->name));
			Yii::app()->user->login($identity);
			
			$this->redirect(Yii::app()->createUrl('index'));
			
		}
	
		#$this->render('index');
	}
	
		public function actionSwitchback() {
			if(Yii::app()->user->hasState('oldIdentity') && $oldIdentity = Yii::app()->user->getState('oldIdentity')) {
				$identity = new SwitchIdentity($oldIdentity['id'], $oldIdentity['name']);
				Yii::app()->user->login($identity);
				$this->redirect(Yii::app()->createUrl('index'));
			}
	
		#$this->render('index');
		}	
	
	
	public function actionProfil() {
		
		$passForm 		= new ProfilPasswortForm;
		$deleteForm 	= new ProfilDeleteForm;
		
		$user = User::model()->findByPk(Yii::app()->user->getId());
		
		if(isset($_POST['face_id'])) {
			$face = UtCharacterFace::model()->findByPk($_POST['face_id']);
			if($face != null) {
				$user->skin = $face->image;
				$user->save();
				
				echo CHtml::tag('div',array('class'=>'flash-success'),Yii::t('user','profil_bild_geaendert',array('{bildname}'=>$face->face)));
				
				Yii::app()->end();
				
			}
		}
		
		
		if($user==null) {
			throw new CHttpException(404,'Existiert nicht');
		} else {
			
			$class_id = 0;
			$skin_id = 0;
			$face_id = 0;
			
			$skinSelect = array();
			$faceSelect = array();
				
			if($user->skin != '') {
				$face = UtCharacterFace::model()->with('skin')->findByAttributes(array('image'=>$user->skin));
				if($face != null) {
					$class_id 	= $face->skin->class_id;
					$skin_id	= $face->skin_id;
					$face_id	= $face->face_id;
					
					$criteria = new CDbCriteria();
					$criteria->condition = 'class_id=:class_id';
					$criteria->params = array(':class_id'=>$class_id);					
					
					$skinSelect = CHtml::listData(UtCharacterSkin::model()->findAll($criteria),'skin_id','skin');
					
					$criteria = new CDbCriteria();
					$criteria->condition = 'skin_id=:skin_id';
					$criteria->params = array(':skin_id'=>$skin_id);
						
					$faceSelect = CHtml::listData(UtCharacterFace::model()->findAll($criteria),'face_id','face');

				}
			}
			
		}
		
		$this->performAjaxValidation($user);
		
		if(isset($_POST['User'])) {
			$user->user_nick 		= $_POST['User']['user_nick'];
			$user->email 			= $_POST['User']['email'];
			$user->realname 		= $_POST['User']['realname'];
			$user->flaggen_id 		= $_POST['User']['flaggen_id'];
			$user->motto 			= $_POST['User']['motto'];
			$user->clanhistory 		= $_POST['User']['clanhistory'];
			$user->fav_weapons 		= $_POST['User']['fav_weapons'];
			$user->fav_maps 		= $_POST['User']['fav_maps'];
			$user->hate_maps 		= $_POST['User']['hate_maps'];

			
			if($user->member_flag == 1) {

				$user->aufgaben 		= $_POST['User']['aufgaben'];
				$user->member_since 	= isset($_POST['User']['member_since']) ? $_POST['User']['member_since']: $_POST['member_since'];				
				$user->ort 				= $_POST['User']['ort'];
				$user->plz 				= $_POST['User']['plz'];
				$user->str 				= $_POST['User']['str'];						
				$user->handy			= $_POST['User']['handy'];

			}	
			
			if($user->validate()) {
				$user->save();
				Yii::app()->user->setFlash('gespeichert',Yii::t('user','deine_daten_erfolgreich_gespeichert'));
			} else {
				//GFunctions::pre($user->getErrors());
			}
			
		}
		
		$this->render('profil', array(
				'user'=>$user,
				'class_id' => $class_id,
				'skin_id' => $skin_id,
				'face_id' => $face_id,
				'faceSelect' => $faceSelect,
				'skinSelect' => $skinSelect,
				'passForm' => $passForm,
				'deleteForm' => $deleteForm
		));
	}
	
	
	public function actionDynamicSkin() {

		
		$data=UtCharacterSkin::model()->findAll('class_id=:class_id',
				array(':class_id'=>(int) $_POST['class_id']));
	
		$data=CHtml::listData($data,'skin_id','skin');
		
		echo CHtml::tag('option',array('value'=>''),CHtml::encode(Yii::t('user','bitte_waehlen')));
		foreach($data as $value=>$name)
		{
			echo CHtml::tag('option',
					array('value'=>$value),CHtml::encode($name),true);
		}
	}	

	public function actionDynamicFace() {
		
		$data=UtCharacterFace::model()->findAll('skin_id=:skin_id',
				array(':skin_id'=>(int) $_POST['skin_id']));
	
		$data=CHtml::listData($data,'face_id','face');
		
		echo CHtml::tag('option',array('value'=>''),CHtml::encode(Yii::t('user','bitte_waehlen')));
		
		foreach($data as $value=>$name)
		{
			echo CHtml::tag('option',
					array('value'=>$value),CHtml::encode($name),true);
		}
	}	

	public function actionDynamicImage() {
	
		$data=UtCharacterFace::model()->find('face_id=:face_id',
				array(':face_id'=>(int) $_POST['face_id']));
	
		return $this->widget('ext.SAImageDisplayer', array(
			'image' => $data['image'] ? $data['image'] : 'no_picture.jpg',
			'size' => 'big',
			'group' => 'skin',
			'title' => null,
			'alt' => $data['image'] ? $data['image'] : 'no_picture.jpg',
			'class' => '',
			'id' => 'profilbild',
		));
	}	
	
	public function actionValidate() {
		$aktivierungscode = Yii::app()->request->getQuery('key');
	
		if(isset($aktivierungscode) && !empty($aktivierungscode)) {
			$model = User2Validierung::model()->find('validierungs_schluessel=:key AND validierungs_typ =:typ AND validiert_flag =:flag', array(':key'=>$aktivierungscode, ':typ'=>'account_aktivierung', ':flag'=>0));
			if($model != null) {
				if($aktivierungscode == $model->validierungs_schluessel) {
					$model->validiert_flag		= 1;
					$model->validiert_datum		= date('Y-m-d H:i:s');

					if($model->save()) {
						$user = User::model()->findByPk($model->user_id);
						$user->freigeschaltet_flag = 1;
						$user->save();

						Yii::app()->authManager->assign('Freigeschaltet',$user->user_id);
						
						$ersetzen['{user_nick}'] = $user->user_nick;
						$ersetzen['{button}'] =	CHtml::link(
													Yii::t('user','einloggen'),
													Yii::app()->createUrl('user/login'),
													array(
														'data-target'	=> '#myModal',
														'data-toggle'	=> 'modal',
														'id'			=> 'loginBtn2',
														'class'			=> 'loginBtn'
													)
												);
						$message = Yii::t('user', 'validierung_erfolgreich', $ersetzen);
						Yii::app()->user->setFlash('validieren', $message);
					}
				}
			}
		} else {
			$model = null;
		}
		 
		$this->render('validate',array('model'=>$model));
	}	

	public function actionLogoutForm() {
		$this->renderPartial('_logoutForm');
	}	
	
	public function actionLogout() {
		Yii::app()->user->logout();
		echo CJSON::encode(array(
	    		'status'=>'success'
	    	));
		Yii::app()->end();		
		//$this->redirect(Yii::app()->homeUrl);
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
	public function actionCreate()	{
		$model= new User;
		$model->setScenario('admin-create');
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$class_id = 0;
		$skin_id = 0;
		$face_id = 0;
			
		$skinSelect = array();
		$faceSelect = array();		

		$criteria = new CDbCriteria();
		$criteria->condition = 'st_flag =:st_flag';
		$criteria->params = array(':st_flag'=>1);
		
		$squads = Squad::model()->findAll($criteria);
		
		$this->performAjaxValidation($model);
		
		$transaction = Yii::app()->db->beginTransaction();
		
		if(isset($_POST['User'])) {
				
			$model->user_nick 		= $_POST['User']['user_nick'];
			$model->email 			= $_POST['User']['email'];
			$model->realname 		= $_POST['User']['realname'];
			$model->flaggen_id 		= $_POST['User']['flaggen_id'];
			$model->motto 			= $_POST['User']['motto'];
			$model->clanhistory 	= $_POST['User']['clanhistory'];
			$model->fav_weapons 	= $_POST['User']['fav_weapons'];
			$model->fav_maps 		= $_POST['User']['fav_maps'];
			$model->hate_maps 		= $_POST['User']['hate_maps'];
			$model->aufgaben 		= $_POST['User']['aufgaben'];
			$model->member_since 	= $_POST['member_since'];
			$model->ort 			= $_POST['User']['ort'];
			$model->plz 			= $_POST['User']['plz'];
			$model->str 			= $_POST['User']['str'];
			$model->handy			= $_POST['User']['handy'];
			$model->geburtsdatum	= $_POST['geburtsdatum'];
			$model->member_flag		= $_POST['User']['member_flag'];
			$model->status			= $_POST['User']['status'];
			$model->skin			= $_POST['User']['skin'];
			$model->user_pwd 		= $_POST['User']['user_pwd'];
			$model->user_pwd2 		= $_POST['User']['user_pwd2'];
		

		
			if($model->validate()) {
				
				$model->user_pwd 		= $model->hashPassword($_POST['User']['user_pwd']);
				$model->save(false);
				
				$atts['user_id'] = $model->user_id;
					
				if(isset($_POST['member'])) {
						
					foreach($_POST['member']['fighter'] as $k => $v) {
						$newSquads[$k] = $k;
					}
					if(isset($_POST['member']['leader'])) {
						foreach($_POST['member']['leader'] as $k => $v) {
							$newSquads[$k] = $k;
						}
					}
					if(isset($_POST['member']['orga'])) {
						foreach($_POST['member']['orga'] as $k => $v) {
							$newSquads[$k] = $k;
						}
					}
						
					foreach($newSquads as $k => $v) {
						$atts['squad_id'] = $v;
								
						$mod = new User2Squad();
						$mod->attributes = $atts;
								
						$mod->save(false);
					}
				}
				$transaction->commit();
				Yii::app()->user->setFlash('gespeichert',Yii::t('member','user_erfolgreich_angelegt'));
				$this->redirect(array('update','id'=>$model->user_id));
			} else {
				$transaction->rollBack();
				//GFunctions::pre($model->getErrors());
			}
		}
		
		$this->render('create',array(
				'model'=>$model,
				'squads' => $squads,
				'class_id' => $class_id,
				'skin_id' => $skin_id,
				'face_id' => $face_id,
				'faceSelect' => $faceSelect,
				'skinSelect' => $skinSelect,
				'action' => 'create'
		));
		
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		
		$model	= $this->loadModel($id);
		$model->setScenario('admin-update');
		
		if(isset($_POST['face_id'])) {
			$face = UtCharacterFace::model()->findByPk($_POST['face_id']);
			if($face != null) {
				$model->skin = $face->image;
				$model->save();
		
				echo CHtml::tag('div',array('class'=>'flash-success'),Yii::t('user','profil_bild_geaendert',array('{bildname}'=>$face->face)));
		
				Yii::app()->end();
		
			}
		}
		
		$class_id = 0;
		$skin_id = 0;
		$face_id = 0;
			
		$skinSelect = array();
		$faceSelect = array();
		
		if($model->skin != '') {
			$face = UtCharacterFace::model()->with('skin')->findByAttributes(array('image'=>$model->skin));
			if($face != null) {
				$class_id 	= $face->skin->class_id;
				$skin_id	= $face->skin_id;
				$face_id	= $face->face_id;
					
				$criteria = new CDbCriteria();
				$criteria->condition = 'class_id=:class_id';
				$criteria->params = array(':class_id'=>$class_id);
					
				$skinSelect = CHtml::listData(UtCharacterSkin::model()->findAll($criteria),'skin_id','skin');
					
				$criteria = new CDbCriteria();
				$criteria->condition = 'skin_id=:skin_id';
				$criteria->params = array(':skin_id'=>$skin_id);
		
				$faceSelect = CHtml::listData(UtCharacterFace::model()->findAll($criteria),'face_id','face');
		
			}
		}		
		
		
		$criteria = new CDbCriteria();
		$criteria->condition = 'st_flag =:st_flag';
		$criteria->params = array(':st_flag'=>1);
		
		$squads = Squad::model()->findAll($criteria);
		
		$oldSquads = array();
		$newSquads = array();
		
		$attributes['user_id'] = $id; 
		foreach($squads as $k => $v) {
			$attributes['squad_id'] = $v['squad_id'];
			$zuweisung = User2Squad::model()->findByAttributes($attributes);
			$squads[$k]['zuweisung'] = $zuweisung;
			if($zuweisung != null) {
				$oldSquads[] = $v['squad_id'];
			}
		}
		
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['User'])) {
			
			$model->user_nick 		= $_POST['User']['user_nick'];
			$model->email 			= $_POST['User']['email'];
			$model->realname 		= $_POST['User']['realname'];
			$model->flaggen_id 		= $_POST['User']['flaggen_id'];
			$model->motto 			= $_POST['User']['motto'];
			$model->clanhistory 	= $_POST['User']['clanhistory'];
			$model->fav_weapons 	= $_POST['User']['fav_weapons'];
			$model->fav_maps 		= $_POST['User']['fav_maps'];
			$model->hate_maps 		= $_POST['User']['hate_maps'];
			$model->aufgaben 		= $_POST['User']['aufgaben'];
			$model->member_since 	= $_POST['member_since'];
			$model->ort 			= $_POST['User']['ort'];
			$model->plz 			= $_POST['User']['plz'];
			$model->str 			= $_POST['User']['str'];
			$model->handy			= $_POST['User']['handy'];
			$model->geburtsdatum	= $_POST['geburtsdatum'];
			$model->member_flag		= $_POST['User']['member_flag'];
			$model->status			= $_POST['User']['status'];

			$atts['user_id'] = $id;							
			
			if(!isset($_POST['member'])) {
				User2Squad::model()->deleteAllByAttributes($atts);
			} else {
				if(isset($_POST['member']['fighter'])) {			
					foreach($_POST['member']['fighter'] as $k => $v) {
						$newSquads[$k] = $k;
					}
				}
				if(isset($_POST['member']['leader'])) {
					if(isset($_POST['member']['leader'])) {
						foreach($_POST['member']['leader'] as $k => $v) {
							$newSquads[$k] = $k;
						}
					}
				}				
				if(isset($_POST['member']['orga'])) {
					if(isset($_POST['member']['orga'])) {				
						foreach($_POST['member']['orga'] as $k => $v) {
							$newSquads[$k] = $k;
						}
					}
				}				

				foreach($oldSquads as $k => $v) {
					$atts['squad_id'] = $v;
					if(!in_array($v,$newSquads)) {
						User2Squad::model()->deleteAllByAttributes($atts);
						unset($oldSquads[$k]);
					} else {
						
						$mod = User2Squad::model()->findByAttributes($atts);
						if(isset($_POST['member']['leader'][$v])) {
							$mod->leader_flag = 1;
						} else {
							$mod->leader_flag = 0;
						}
						if(isset($_POST['member']['orga'][$v])) {
							$mod->orga_flag = 1;
						} else {
							$mod->orga_flag = 0;
						}	
						
						$mod->save();					
					}
				}
				
				foreach($newSquads as $k => $v) {
					if(!in_array($v,$oldSquads)) {
						$atts['squad_id'] = $v;
				
						$mod = new User2Squad();
						$mod->attributes = $atts;
						
						$mod->save(false);
					}
				}				
				
			}
				
			
			
			if($model->validate()) {
				$model->save();
				Yii::app()->user->setFlash('gespeichert',Yii::t('member','daten_erfolgreich_gespeichert'));
			} else {
				GFunctions::pre($model->getErrors());
			}
		}

		$criteria = new CDbCriteria();
		$criteria->condition = 'st_flag =:st_flag';
		$criteria->params = array(':st_flag'=>1);
		
		$squads = Squad::model()->findAll($criteria);
		
		$oldSquads = array();
		$newSquads = array();
		
		$attributes['user_id'] = $id;
		foreach($squads as $k => $v) {
			$attributes['squad_id'] = $v['squad_id'];
			$zuweisung = User2Squad::model()->findByAttributes($attributes);
			$squads[$k]['zuweisung'] = $zuweisung;
			if($zuweisung != null) {
				$oldSquads[] = $v['squad_id'];
			}
		}		
		
		$this->render('update',array(
			'model'=>$model,
			'squads' => $squads,
			'class_id' => $class_id,
			'skin_id' => $skin_id,
			'face_id' => $face_id,
			'faceSelect' => $faceSelect,
			'skinSelect' => $skinSelect,	
			'action' => 'update'
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
	    $model=$this->loadModel($id);
		if(Yii::app()->user->checkAccess('admin') && $model->chef_flag == 1) {
		    throw new CHttpException('401');
		}
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
		$dataProvider=new CActiveDataProvider('User');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

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
		$model=User::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
	public function actionPasswort() {
	
		$passForm = new ProfilPasswortForm;
		
		if(isset($_POST['ProfilPasswortForm'])){
			
			if(isset($_POST['scenario'])) {
				$passForm->setScenario($_POST['scenario']);
			}
			
			
			if(isset($_POST['user_id'])) {
				$user_id = $_POST['user_id'];
			} else {
				$user_id = Yii::app()->user->getId();
			}
			
			$passForm->attributes = $_POST['ProfilPasswortForm'];
	
			if($passForm->validate()) {
				$user = User::model()->findByPk($user_id);
				$user->user_pwd = $user->hashPassword($passForm->passwort1);
				
		
				
				if($user->save()) {
					$response['success'] = true;
					$response['message'] = Yii::t('member','passwort_geandert');
				}
	
				header('Content-type:application/json');
				echo CJSON::encode($response);
				Yii::app()->end();
			} else {
				$error = CActiveForm::validate($passForm);
				if($error!='[]') {
					echo $error;
				}
				Yii::app()->end();
			}
		}
	}
	
	public function actionPasswortLinkVerschickt() {
		$this->render('pw_link_zugeschickt');
	}
	
	public function actionPasswortVergessen() {

		if(!Yii::app()->user->isGuest) {
			$model=new PasswortVergessenForm();
		} else {
			$model=new PasswortVergessenForm('captchaRequired');
		}
	
		if(isset($_POST['PasswortVergessenForm'])) 	{
			$model->attributes=$_POST['PasswortVergessenForm'];
			$valid=$model->validate();
			if($model->validate()){
				$User2Validierung = new User2Validierung();
				$User2Validierung->user_mail				= $model->email;
				$User2Validierung->datum_angefordert		= date('Y-m-d H:i:s');
				$User2Validierung->daten					= '';

				$User2Validierung->user_ip_angefordert		= Yii::app()->request->userHostAddress;
				$User2Validierung->validierungs_typ			= 'passwort_vergessen';
				$User2Validierung->validierungs_schluessel	= GFunctions::createRandomString(2);

				$user = User::model()->findByAttributes(array('email' => $model->email));

				$User2Validierung->user_id = $user->user_id;
				$User2Validierung->user_id_angefordert = $user->user_id;

				if(!$User2Validierung->validate()) {
					throw new CHttpException(404,Yii::t('global','seite_fehlerhaft_aufgerufen'));
				} else {
					$User2Validierung->save(false);
				}

				Yii::app()->user->setFlash('passwort_vergessen',Yii::t('user', 'pwd_vergessen_success_info'));

				$message = new YiiMailMessage(Yii::app()->params['clan'].': '.Yii::t('user', 'Neues Passwort anfordern'));
				$message->view = 'neuesPasswortAnfordern';
				$message->setBody(array('userModel'=>$user,'validierung'=>$User2Validierung,'host'=>'http://www.santitan.de','projekt'=>Yii::app()->params['clan'],'domain'=>'santitan.de'), 'text/html');
				$message->addTo($model->email);
				$message->setFrom(array(Yii::app()->params['noReplyMail'] => Yii::app()->params['clan']));
				Yii::app()->mail->send($message);
		
				Yii::app()->session->add("captchaRequired", "true");

				//$this->render('passwortVergessen',array('model'=>$model));
				
				//$response['success'] = 'success';
				//

			} else{
				//$error = CActiveForm::validate($model);
				//if($error!='[]') {
					//Yii::app()->user->setFlash('passwort_vergessen',Yii::t('user', 'pwd_vergessen_success_info'));
				//}
			}
		}
	
		// Wenn ajaxrequest-lade passwortVergessenBox in den Header, ansonsten normaler Seite
		#if(Yii::app()->request->isAjaxRequest) {
			#$outputJs = Yii::app()->request->isAjaxRequest;
			#$this->renderPartial('passwortVergessen', array('model'=>$model), false, $outputJs);
		#} else {
		$this->render('passwortVergessen',array('model'=>$model));
		#}
	}
	
	public function actionNeuesPasswortAnfordern() {
		$aktivierungscode = Yii::app()->request->getQuery('key');
	
		if(isset($aktivierungscode) && !empty($aktivierungscode)) {
			$model = User2Validierung::model()->find('validierungs_schluessel=:key AND validierungs_typ =:typ AND validiert_flag =:flag', array(':key'=>$aktivierungscode, ':typ'=>'passwort_vergessen', ':flag'=>0));
			if($model != null) {
				if($aktivierungscode == $model->validierungs_schluessel) {
					$model->validiert_flag		= 1;
					$model->validiert_datum		= date('Y-m-d H:i:s');
	
					if($model->save()) {
	
						$passwort = GFunctions::createRandomString(5);
	
						$user = User::model()->findByPk($model->user_id);
						$user->user_pwd = $user->hashPassword($passwort);
						$user->save();
	
						$message = new YiiMailMessage(Yii::app()->params['clan'].': Dein Passwort');
						$message->view = 'neuesPasswort';
						$message->setBody(array('userModel'=>$user,'neues_passwort'=>$passwort,'host'=>'http://www.santitan.de','projekt'=>Yii::app()->params['clan'],'domain'=>'santitan.de'), 'text/html');
						$message->addTo($user->email);
						$message->setFrom(array(Yii::app()->params['noReplyMail'] => Yii::app()->params['clan']));
						Yii::app()->mail->send($message);
	
						Yii::app()->user->setFlash('validieren',Yii::t('user', 'pwd_vergessen_success_info2'));
							
					}
				}
			}
		} else {
			$model = null;
		}
			
		$this->render('validate',array('model'=>$model));
	}
	
	
	public function actionSignatur() {
	
		$signaturForm = new ProfilSignaturForm;
	
		// datentyp_ids
	
		if(isset($_POST['ProfilSignaturForm'])){
			$signaturForm->signatur 			= $_POST['ProfilSignaturForm']['signatur'];
			$signaturForm->signaturen_anzeigen 	= $_POST['ProfilSignaturForm']['signaturen_anzeigen'];
			$signaturForm->signatur_deaktivieren= $_POST['ProfilSignaturForm']['signaturen_anzeigen'];
	
			if($signaturForm->validate()) {
	
				foreach($signaturForm->models as $k => $v) {
					$v->daten->datenwert 		=  $signaturForm->{$v->datentyp};
					if(!empty($signaturForm->{$v->datentyp})) {
						$v->daten->daten_anzeige 	=  1;
					} else {
						$v->daten->daten_anzeige 	=  0;
					}
					$v->daten->save();
				}
	
				$response['success'] = true;
				$response['message'] = Yii::t('profil','signatur_einstellungen_gespeichert');
	
				header('Content-type:application/json');
				echo CJSON::encode($response);
				Yii::app()->end();
			} else {
				$error = CActiveForm::validate($signaturForm);
				if($error!='[]') {
					echo $error;
				}
				Yii::app()->end();
			}
		}
	}	
	
}
