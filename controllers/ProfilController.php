<?php
/*
 * TODO: cron für Userfreischaltung: info an Upload dass inzwischen xxx User auf Freischaltung warten
 */
class ProfilController extends Controller {
	
	public $user_id;
	public $userprofil;
	public $meinProfil = false;
	
	public $scriptAutor;
	
	
	
	public function filters() {
		return array(
				'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function accessRules() {
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('captcha','logout', 'login', 'registrieren', 'passwort', 'validate', 'erfolgreich_registriert'),
						'users'=>array('*'),
				),
				array('allow',
						'actions' => array('index', 'einstellungen', 'userdaten', 'fansuche', 'avatarauswahl', 'avatarupload', 'upload', 'ajaxcrop', 'letzte_beitraege', 'loeschen', 'passwort', 'signatur', 'benachrichtigungen', 'forum', 'favoriten'),
						'users'=>array('@'),
						//'roles' => array('authentifiziert', 'Authenticated')
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}	

	public function actions() {
		return array(
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha'=>array(
						'class'=>'CCaptchaAction',
						'backColor'=>0xFFFFFF,
				),
				'aclist'=>array(
					'class'=>'application.extensions.EAutoCompleteAction',
					'model'=>'Vereinsnamen', //My model's class name
					'attribute'=>'name', //The attribute of the model i will search
				),
		);
	}	
	

/*
 * Vor jeder Aktion im Userprofil wird das aktulle Userprofil geladen.
 * Ausnahmen hiervon werden im Array $public definifiert.
 * Kann man sicherlich mit ->scenario anders lösen?
 */	
	
	public function beforeAction($action) {
		//Yii::app()->user->logout();
		if (parent::beforeAction($action)) {
			$public = array('registrieren','login','validate','captcha');
			
			if(in_array($action->id, $public)) {
				return true;
			}

			if($user_id = Yii::app()->request->getParam('id')) {
				$this->user_id = $user_id;
				if($user_id == Yii::app()->user->getId()) {
					$this->meinProfil = true;
				}
			} else {
				$this->user_id = Yii::app()->user->getId();
				$this->meinProfil = true;
			}
			$this->userprofil = User::model()->findByPk($this->user_id);
			
			if(!is_object($this->userprofil)) {
/*
 * TODO: kann hier nicht false returnen, da ansonsten das Captcha nicht mehr funktioniert!
 */				
				return false;
			}
			
			return true;			
		} else {
			return false;
		}
	}	
	
	public function actionIndex() {

		if(isset($_POST['User'])) {
			$user->tr_erinnerung_flag = $_POST['User']['tr_erinnerung_flag'];
			$user->pn_erinnerung_flag = $_POST['User']['pn_erinnerung_flag'];
			$user->newsletter_flag = $_POST['User']['newsletter_flag'];
		}

		$meinTMBox 			= array(0=>1);
		$managerBox 		= array(1 => 0);
		$datenpflegeBox 	= array(1 => 0);
		$aktivitaetenBox 	= array(1 => 0);
		$alarmBox 			= array(1 => 0);
		$favoriten			= null;
		
		
		$vereinsBox 		= array();
		
		if($this->meinProfil === true) {

			if($this->userprofil->verein_id > 0) {
				$vereinsBox = array(0=>1);
			} 

			
			$favoriten		= Favoriten::model()->holeUserFavoriten($this->user_id);
			
			$file = 'index';
		} else {
			$file = 'public';
		}
			
		$this->render($file, array(
				'meinTMBox' 		=> $meinTMBox,
				'vereinsBox' 		=> $vereinsBox,
				'managerBox' 		=> $managerBox,
				'datenpflegeBox'	=> $datenpflegeBox,
				'aktivitaetenBox'	=> $aktivitaetenBox,
				'favoriten'			=> $favoriten,
			)
		);
	}
	
	public function actionForum() {
	
		if(isset($_POST['User'])) {
			$user->tr_erinnerung_flag = $_POST['User']['tr_erinnerung_flag'];
			$user->pn_erinnerung_flag = $_POST['User']['pn_erinnerung_flag'];
			$user->newsletter_flag = $_POST['User']['newsletter_flag'];
		}
	
		
		$attributes['user_id'] = Yii::app()->user->getId();
		
		$abos 			= ForumAbo::model()->holeUserAbos();	
		$guteBeitraege 	= ForumGuterBeitrag::model()->holeUserWertungen();
			
		$this->render('forum', array(
				'abos' 			=> $abos,
				'guteBeitraege'	=> $guteBeitraege,
			)
		);
	}	
	
	public function actionEinstellungen() {
/*
 * TODO: Prüfen, dass nur ich diese Aktion durchführen darf // ggf ein Superadmin mit tmp Userzuweisung?!?
 */	
		$dynamicForm 	= new DynamicFormModel();
		$passForm 		= new ProfilPasswortForm;		
		$deleteForm 	= new ProfilDeleteForm;
		
				
		//TMFunctions::pre($passForm);
		
		if(isset($_POST['User2Daten'])) {

			//TMFunctions::pre($_POST);
			
			
			foreach($dynamicForm->models as $k => $v) {
				$dynamicForm->{$v->datentyp} = $_POST['User2Daten'][$k]['datenwert'];
					
				$v->daten->user_id 			=  Yii::app()->user->getId();
				$v->daten->datentyp_id		=  $v->datentyp_id;
				$v->daten->datenwert 		=  $_POST['User2Daten'][$k]['datenwert'];
				$v->daten->daten_anzeige 	=  $_POST['User2Daten'][$k]['daten_anzeige'];
				
				$dynamicForm->models[$k]['daten'] = $v->daten;
				
				//TMFunctions::pre($dynamicForm->models[$k]['daten']->attributes);
			}
			
			
			if($dynamicForm->validate()){	
				
				
				foreach($dynamicForm->models as $k => $v) {
					$v->daten->save();
				}				
				
				
				$response['success'] = true;
				$response['message'] = Yii::t('profil','einstellungen_erfolgreich_gespeichert');
				
				header('Content-type:application/json');
				echo CJSON::encode($response);
				Yii::app()->end();
			} else {
				$error = CActiveForm::validate($dynamicForm);
				if($error!='[]') {
					echo $error;
				}
				Yii::app()->end();
			}
		} 
		$this->render('einstellungen', array(
							'dynamicForm'=>$dynamicForm, 
							'passForm' => $passForm,
							'deleteForm' => $deleteForm,
		));
	}	

	public function actionBenachrichtigungen() {
		/*
		 * TODO: Prüfen, dass nur ich diese Aktion durchführen darf // ggf ein Superadmin mit tmp Userzuweisung?!?
		*/
	
		//TMFunctions::pre($_POST);
		
		$bereiche = array('privatenachrichten','forum','managerspiel','meintm','tipprunde','verein','korrektur','transfermarkt','transferliste','tv');
		
		$user = User::model()->findByPk(Yii::app()->user->getId());
		
		if($user->tld_sprache != 'de') {
			unset($bereiche[8], $bereiche[9]);
		}
		
		
		$attr['user_id'] = Yii::app()->user->getId();
		
		if(isset($_POST['benachrichtigungen'])) {
	
			if(isset($_POST['benachrichtigungen']['cb'])) {
		
				foreach($_POST['benachrichtigungen']['cb'] as $k => $v) {

					$attr['infotyp_id'] = $k;
					
					$User2Info = User2Info::model()->findByAttributes($attr);

					if(!is_object($User2Info)) {
						$User2Info = new User2Info;
						$User2Info->user_id 	= $attr['user_id'];
						$User2Info->infotyp_id	= $attr['infotyp_id'];						
					}
					
					$info_flag = 0;
					
					if(isset($v['mail']) && isset($v['pn'])) {
						$info_flag = 3;
					} elseif(isset($v['mail']) && !isset($v['pn'])) {
						$info_flag = 1;
					} elseif(!isset($v['mail']) && isset($v['pn'])) {
						$info_flag = 2;
					} 	

					if(isset($v['notification'])) {
						$notification_flag = 1;
					} else {
						$notification_flag = 0;
					}
					
					
					$User2Info->datum 				= date('Y-m-d H:i:s');
					$User2Info->info_flag 			= $info_flag;
					$User2Info->notification_flag 	= $notification_flag;
					
					$User2Info->save();
					
					$response['success'] = true;
					$response['message'] = Yii::t('profil','einstellungen_erfolgreich_gespeichert');
					
					header('Content-type:application/json');
					echo CJSON::encode($response);
					Yii::app()->end();
				}
			}
			
			if(isset($_POST['benachrichtigungen']['rb'])) {
			
				
				foreach($_POST['benachrichtigungen']['rb'] as $k => $v) {
			
					$attr['infotyp_id'] = $k;
						
					$User2Info = User2Info::model()->findByAttributes($attr);
			
					if(!is_object($User2Info)) {
						$User2Info = new User2Info;
						$User2Info->user_id 	= $attr['user_id'];
						$User2Info->infotyp_id	= $attr['infotyp_id'];
					}
						
					$User2Info->info_flag 			= $v['value'];
					$User2Info->datum 				= date('Y-m-d H:i:s');
					$User2Info->notification_flag 	= 0;

					$User2Info->save();
					
					$response['success'] = true;
					$response['message'] = Yii::t('profil','benachrichtigung_einstellungen_erfolgreich_gespeichert');
						
					header('Content-type:application/json');
					echo CJSON::encode($response);
					Yii::app()->end();
				}
			}
				
			
		}
		$this->render('benachrichtigungen', array(
				'bereiche'		=> $bereiche,
		));
	}
	
	public function actionFavoriten () {
		if(isset($_POST['User'])) {
			$user->tr_erinnerung_flag = $_POST['User']['tr_erinnerung_flag'];
			$user->pn_erinnerung_flag = $_POST['User']['pn_erinnerung_flag'];
			$user->newsletter_flag = $_POST['User']['newsletter_flag'];
		}
		
		
		$attributes['user_id'] = Yii::app()->user->getId();
		
		$favoriten		= Favoriten::model()->findAllByAttributes($array('user_id'));
			
		$this->render('favoriten', array(
				'favoriten' => $favoriten,
		));		
	}
	
	
	public function actionPasswort() {
		
		$passForm = new ProfilPasswortForm;
		
		if(isset($_POST['ProfilPasswortForm'])){
			$passForm->attributes = $_POST['ProfilPasswortForm']; 

			if($passForm->validate()) {
				$user = User::model()->findByPk(Yii::app()->user->getId());
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
	
	public function actionUserdaten() {
	

		
		if(isset($_POST['ProfilDatenForm'])){
			
			$args['user_id'] 		= Yii::app()->user->getId();
			$args['datentyp_id'] 	= $_POST['ProfilDatenForm']['datentyp_id'];
			
			$fanForm = new ProfilDatenForm();
			$fanForm->setUser2DatenObject($args);			
			
			$fanForm->attributes = $_POST['ProfilDatenForm'];
				
			if($fanForm->validate()) {
	
				$fanForm->aktualisiereTeilnahme($_POST['ProfilDatenForm']['teilnahme']);
	
				$response['success'] = true;
				//respond with the saved contact in case the model/db changed any values
				if($fanForm->teilnahme == 1) {
					$response['message'] = Yii::t('profil','teilnahme_{datentyp}_erfolgreich', array('{datentyp}' => $fanForm->datentyp));
				} else {
					$response['message'] = Yii::t('profil','abmeldung_{datentyp}_erfolgreich', array('{datentyp}' => $fanForm->datentyp));
				}
				header('Content-type:application/json');
				//encode the response as json:
				echo CJSON::encode($response);
				Yii::app()->end();
			} else {
				$error = CActiveForm::validate($fanForm);
				if($error!='[]') {
					echo $error;
				}
				Yii::app()->end();
			}
		}
	}	
	
	public function actionFansuche() {
	
		$fanForm = new ProfilFansucheForm();
		
		if(isset($_POST['ProfilFansucheForm'])){
			
			$fanForm->attributes = $_POST['ProfilFansucheForm'];
			
			if($fanForm->validate()) {
				
				$fanForm->aktualisiereTeilnahme($_POST['ProfilFansucheForm']['teilnahme']);
				
				$response['success'] = true;
				//respond with the saved contact in case the model/db changed any values
				if($fanForm->teilnahme == 1) {
					$response['message'] = Yii::t('profil','teilnahme_fansuche_erfolgreich');
				} else {
					$response['message'] = Yii::t('profil','abmeldung_fansuche_erfolgreich');
				}
				header('Content-type:application/json');
				//encode the response as json:
				echo CJSON::encode($response);
				Yii::app()->end();					
			} else {
				$error = CActiveForm::validate($fanForm);
				if($error!='[]') {
					echo $error;
				}
				Yii::app()->end();
			}
		}
	}	
	
	
	public function actionLoeschen() {
		
		$deleteForm = new ProfilDeleteForm;
		
		if(isset($_POST['ProfilDeleteForm'])){
			if(isset($_POST['ProfilDeleteForm']['delete']) && $_POST['ProfilDeleteForm']['delete']==1) {
				$deleteForm->delete = $_POST['ProfilDeleteForm'];

				if($deleteForm->validate()) {
					
					$loeschung	= new TmUserGeloescht();
					$loeschung->user_id 	= Yii::app()->user->getId();
					$loeschung->user_nick 	= Yii::app()->user->name;
					$loeschung->user_mail 	= User::model()->findByPk(Yii::app()->user->getId())->user_mail;
					$loeschung->user_ip		= Yii::app()->request->userHostAddress;					
					
					$loeschung->save();
					
					if(!empty($_POST['ProfilDeleteForm']['loeschgrund']) || !empty($_POST['ProfilDeleteForm']['grund_id']))  {
										
						$statistik	= new TmUserGeloeschtStatistik();
						
						if(!empty($_POST['ProfilDeleteForm']['grund_id'])) {
							$statistik->grund_id 	= $_POST['ProfilDeleteForm']['grund_id'];
						}						
						if(!empty($_POST['ProfilDeleteForm']['grund_id'])) {
							$statistik->grund 		= $_POST['ProfilDeleteForm']['loeschgrund'];
						}						
						$statistik->save();
					}
					
					$response['success'] = true;
					$response['message'] = Yii::t('profil','account_geloescht');					
					
					/*
					 * 	NOT WORKING: User::model()->deleteByPk(Yii::app()->user->getId());
					 *  INFO: bei ->deleteByPk ist ein Aufruf von afterDelete nicht möglich,
					 *  deswegen wird das Object erstellt und dann gelöscht (ct)
					 */ 
					$user = User::model()->findByPk(Yii::app()->user->getId());
					$user->delete();
					
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

	public function actionLogin() {
		$this->breadcrumbs = array('Login');
		
	    $model=new LoginForm;

	    if(Yii::app()->request->isAjaxRequest) {
			
	    	if(isset($_POST['LoginForm'])) 	{
	    		$model->attributes=$_POST['LoginForm'];
	    		$valid=$model->validate();
	    		if($model->validate() && $model->login()){
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

	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	
	
	public function actionRegistrieren() {

		$model   			= new RegisterForm;
	    $newUser 			= new User;
	    $User2Validierung 	= new User2Validierung;
	    
    
    	if(isset($_POST['RegisterForm'])) {
    		$model->attributes=$_POST['RegisterForm'];
	    		
    		if($model->validate()){
    			
    			$newUser->user_nick     				= $model->user_nick;
    			$newUser->user_pwd						= $newUser->hashPassword($model->passwort1);
    			$newUser->vorname						= $model->vorname;
    			$newUser->nachname						= $model->nachname;
    			$newUser->user_mail 					= $model->email;
    			$newUser->datenschutzerklaerung_flag	= $model->datenschutzerklaerung_flag; 
    			$newUser->nutzungsbedingungen_flag		= $model->nutzungsbedingungen_flag; 
    			$newUser->datum_registriert 			= date('Y-m-d H:i:s');
    			$newUser->tld_sprache 					= Yii::app()->params->language;
    			$newUser->tld_registriert				= Yii::app()->params->language;
    			$newUser->letzte_ip						= Yii::app()->request->userHostAddress;
    			
    			$User2Validierung->user_mail				= $newUser->user_mail;
    			$User2Validierung->datum_angefordert		= date('Y-m-d H:i:s');
    			$User2Validierung->daten					= '';
    			
    			$User2Validierung->user_ip_angefordert		= Yii::app()->request->userHostAddress;
    			$User2Validierung->validierungs_typ			= 'account_aktivierung';
    			$User2Validierung->validierungs_schluessel	= TMFunctions::createRandomString(2);	    			
	    			
    			try {
    				/*
    				 * Ich mag Transaktionen! (ct)
    				 * -> Erst wenn User valide ist, können die Validerungsdaten gespeichert werden
    				 */
    				$transaction = Yii::app()->db->beginTransaction();
    				
    				if(!$newUser->save()) {
    					throw new Exception($newUser->getErrors());
    				}
    				
    				
    				$User2Validierung->user_id				= $newUser->user_id;
    				$User2Validierung->user_id_angefordert	= $newUser->user_id;
    				
    				if(!$User2Validierung->save()) {
    					throw new Exception($User2Validierung->getErrors());
    				}	 

    				//$this->afterSave($newUser);
    				
    				$transaction->commit();
    					
    				$message = new YiiMailMessage(Yii::app()->name.': '.Yii::t('profil', 'registrierung'));
    				$message->view = 'registrierung';
    				$message->setBody(array('userModel'=>$newUser,'validierung'=>$User2Validierung,'host'=>Yii::app()->request->getHostInfo(),'projekt'=>Yii::app()->name), 'text/html');
    				$message->addTo($newUser->user_mail);
    				$message->setFrom(array(Yii::app()->params['noReplyEmail'] => Yii::app()->name));
    				Yii::app()->mail->send($message);
    				
    			} catch (Exception $e)	{
    				//print_r($e->getMessage());
    				$transaction->rollback();
    				$error = CActiveForm::validate($model,$newUser);
    				if($error!='[]') {
    					echo $error;
    				}
    				//Yii::app()->user->setFlash('error', "{$e->getMessage()}");
    				//$this->refresh();
    			}	
    			// Wenn ajaxrequest-lade registerbox in den HEader, ansonsten normaler Seite (ct)
    			
    			if(Yii::app()->request->isAjaxRequest) {
    				echo CJSON::encode(array(
    					'status'=>'success'
    				));
    			} else {	
    				Yii::app()->user->setFlash('registrieren',Yii::t('profil', 'registrierung_erfolgreich'));
					$this->refresh();
    			}	
    			Yii::app()->end();	    			
    			
    		} else {
    			if(Yii::app()->request->isAjaxRequest) {
	    			$error = CActiveForm::validate($model,$newUser);
	    			if($error!='[]') {
		    			echo $error;
		    		}
    			}
	    		Yii::app()->end();
    		}	    			    		
	    }	    
	    
		if(Yii::app()->request->isAjaxRequest) {
			// ist das hier wiklich nötig? (ct) - evtl wieder rausschmeißen
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery-1.9.1.js', CClientScript::POS_HEAD);
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/assets/9cfc337a/ajaxqueue.js', CClientScript::POS_HEAD);
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/assets/9cfc337a/jquery.yiiactiveform.js', CClientScript::POS_HEAD);
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/functions.js', CClientScript::POS_HEAD);
			$this->renderPartial('registrieren', array('model'=>$model), false, true);
		} else {
			$this->render('registrieren', array('model'=>$model));
		}
	}

	public function getSignaturFormular() {

		$signaturForm = new ProfilSignaturForm;		
		
		$preview = $this->renderPartial('_profilSignatur',array(
				'signaturForm' 	=> $signaturForm,
			), true, false); 
		
		return $preview;
	}	
	
	public function actionErfolgreich_registriert() {
		// Erfolgsmeldung für Registrierung über die Box im Header (ct)
		$this->renderPartial('erfolgreichRegistriert');
	}	
	
	protected function getErrorSummary($model)	{
		// Um Fehlerin den Boxen abfangen zu können, brauche ich JSON! (ct)
		if(Yii::app()->request->isAjaxRequest) {
			$errors=CActiveForm::validate($model);
			if($errors !== '[]') Yii::app()->end($errors); 
		}
	}	
	
	public function actionValidate() {
		$aktivierungscode = Yii::app()->request->getQuery('key');
	
		if(isset($aktivierungscode) && !empty($aktivierungscode)) {
			$model = User2Validierung::model()->find('validierungs_schluessel=:key AND validierungs_typ =:typ AND validiert_flag =:flag', array(':key'=>$aktivierungscode, ':typ'=>'account_aktivierung', ':flag'=>0));
			if(is_object($model)) {
				if($aktivierungscode == $model->validierungs_schluessel) {
					$model->validiert_flag		= 1;
					$model->validiert_datum		= date('Y-m-d H:i:s');

					if($model->save()) {
						$user = User::model()->findByPk($model->user_id);
						$user->freigeschaltet_flag = 2;
						$user->save();

						$ersetzen['{user_nick}'] = $user->user_nick;
						$message = Yii::t('profil', 'validierung_erfolgreich', $ersetzen);
						Yii::app()->user->setFlash('validieren', $message);
					}
				}
			}
		}
		 
		$this->render('validate',array('model'=>$model));
	}
	
	
	protected function afterSave(&$model) {
		if (!$model->isNewRecord) {
			
			$user_id = $model->user_id;
			
			$User2Daten = new User2Daten;

			$typen = array('vorname','nachname','email');
				
			$datentyp = Datentyp::model()->findAllByAttributes(array('datentyp'=>$typen));
				
			foreach($datentyp as $k => $v) {
			
				$User2Daten->user_id = $user_id;
				$User2Daten->datentyp_id 	= $v->datentyp_id;

				if(isset($this->attributes[$v->datentyp])) {
					$User2Daten->datenwert 	= $this->attributes[$v->datentyp];
				} else {
					$User2Daten->datenwert 	= $this->attributes['user_mail'];
				}
			
				$User2Daten->daten_anzeige	= 0;
			
				$User2Daten->save(false);
			
			}
				
			$User2Posts = new User2Posts;
			$User2Posts->user_id = $user_id;
			$User2Posts->save(false);
			
			return true;			
		}
	}	
	
}