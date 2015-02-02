<?php

class PnController extends Controller {
	
	protected $offene_pn_id = null;
	protected $filter_absender_id = null;
	private $indexMethoden = array('Delete', 'Read', 'UnRead', 'Archive');
	private $archivMethoden = array('inPosteingang', 'deleteArchiv');
	CONST PN_ANZAHL_POSTEINGANG = 10;

	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function accessRules() {
		return array (
			array('allow',
					'actions'=>array('index','create','archiv','edit','liste', 'senden',
									'delete','alarm','alarmSenden','antworten','searchPosteingangUser','deleteArchiv'),
					'roles'=>array('Freigeschaltet', 'Freigeschaltet ohne Forum', 'Freigeschaltet ohne Korrektur'),
			),
			array('allow',
					'actions'=>array('index','archiv','edit','liste', 'delete','deleteArchiv','searchPosteingangUser'),
					'roles'=>array('Gesperrte User'),
			),
			array('allow',
					'actions'=>array('alarmierteNachrichten'),
					'roles'=>array('Superadmin', 'Administrator'),
			),
		 	
			/*
			array('allow',
				'actions'=>array('index','nachrichtVerfassen','archiv','statusAendern','liste', 'nachrichtSenden',
					'loeschen','alarm','alarmSenden','antwortSenden','searchPosteingangUser'),
				'roles'=>array('Superadmin', 'Administrator', 'Pate'),
			),
			array('allow',
				'actions'=>array('index','archiv','statusAendern','liste', 'loeschen','searchPosteingangUser'),
				'roles'=>array('Superadmin', 'Administrator', 'Pate'),
			),
			array('allow',
				'actions'=>array('alarmierteNachrichten'),
				'roles'=>array('Superadmin', 'Administrator'),
			),
			 */
			array('deny',  // deny all users
					'users'=>array('*'),
			),
		);
	}

	/**
	 * Posteingang
	 */
	public function actionIndex($id = null, $alarm = false) {
		$status 	= Yii::app()->request->getParam('status');
		$quote_id 	= Yii::app()->request->getParam('quote');
		$pn_id 		= (isset($id)) ? (int)$id : Yii::app()->request->getParam('id');

		if (!is_null($this->getFilterAbsenderId('PNEingang'))) {	// Nachrichtenfilter nach Absender
			$PNEingang = new PNEingang();
			$dataProvider = $PNEingang->findByAbsenderId($this->filter_absender_id);
		} else {
			$dataProvider = $this->getDefaultDataProvider('PNEingang');
		}
		//$statusModel = null;
		if (!isset($pn_id) && $dataProvider->getTotalItemCount()) {
			$data = $dataProvider->getData();
			$pn_id = $data[0]->pn_id;
			/*
			 * 
			$statusModel = new PNStatusForm();
			if (isset($_POST['PNStatusForm'])) {
				$statusModel->attributes = $_POST['PNStatusForm'];
				if($statusModel->validate()) {
					// Status Ã¤ndern
					muh('status Ã¤ndern');
					muh($_POST['PNStatusForm']);
					exit;
				} else {
					// Fehler
					muh('fehler');
					exit;
				}
			}
			*/
		}		
		$pn = new PN(Yii::app()->user->getId(), 'posteingang', $pn_id);
		$pn->alsGelesenMarkieren();
		$this->offene_pn_id = $pn->pn_id;
		
		$this->setStatusMessage($status);

		$this->registerPostfachScriptFiles();
		$this->render('index', array(
			'dataProvider' 	=> $dataProvider,
			'pn' 			=> $pn,
			'alarm' 		=> $alarm,
			'postfach' 		=> 'index',
			'absender_id' 	=> $this->filter_absender_id,
			'zitat' 		=> $this->getZitat($quote_id),
			//'statusModel' 	=> $statusModel,
		));
	}

	/**
	 * Archiv
	 */
	public function actionArchiv($id = null) {
		$status = Yii::app()->request->getParam('status');
		$pn_id 	= (isset($id)) ? (int)$id : Yii::app()->request->getParam('id');


		if (!is_null($this->getFilterAbsenderId('PNArchiv'))) {	// Nachrichtenfilter nach Absender
			$PNArchiv = new PNArchiv();
			$dataProvider = $PNArchiv->findByAbsenderId($this->filter_absender_id);
		} else {
			$dataProvider = $this->getDefaultDataProvider('PNArchiv');
		}
		
		if (!isset($pn_id) && $dataProvider->getTotalItemCount()) {
			$data 	= $dataProvider->getData();
			$pn_id 	= $data[0]->pn_id;
		}		
		$pn = new PN(Yii::app()->user->getId(), 'archiv', $pn_id);
		$pn->alsGelesenMarkieren();
		$this->offene_pn_id = $pn->pn_id;

		$this->registerPostfachScriptFiles();

		$this->render('index', array(
			'dataProvider' 	=> $dataProvider,
			'pn' 			=> $pn,
			'alarm' 		=> false,
			'postfach' 		=> 'archiv',
			'absender_id' 	=> $this->filter_absender_id,
			'zitat' 		=> null,
		));
	}
	


	private function registerPostfachScriptFiles() {
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.autosize.min.js');
		Yii::app()->clientScript->registerScript('searchByUsername', "
			$('#searchUser').submit(function(){
				$.fn.yiiGridView.update('postfach', {
			        data: $(this).serialize()
			    });
			});
		");
	}
	
	public function actionSearchByUsername() {
		$absender_id = Yii::app()->request->getParam('absender_id');
		
		$criteria=new CDbCriteria;
	    $criteria->condition = 'empfaenger_id='.Yii::app()->user->getId();
	    $criteria->compare('absender_id',$this->absender_id,true);
	    $criteria->order = 'update_datum DESC';
	
	    return new CActiveDataProvider($this, array(
	        'criteria'=>$criteria,
	        'pagination'=>array(
	            'pageSize'=>15,
	        ),
	    ));
	}
	
	public function actionAlarm() {
		$pn_id = Yii::app()->request->getParam('id');
		$this->actionIndex($pn_id, true);
	}
	
	public function actionAlarmSenden() {
		// Neuer Alarm
		$datum = date('Y-m-d H:i:s');
		$id = (int)$_POST['alarmierteId'];
		$user_id = Yii::app()->user->getId();

		// hole PNEingang des GesprÃ¤chspartners
		$pnEingangUser= PNEingang::model()->find('pn_id=:nachrichtID AND (empfaenger_id=:user_id)', 
				array(':nachrichtID' => $id, ':user_id' => $user_id));

		if ($pnEingangUser->hasSystemAbsender()) {
			throw new CException('Systemnachrichten kÃ¶nnen nicht alarmiert werden!');
		}
		
		$alarm = new PNAlarm();
		$alarm->alarm_tld = 'de';
		$alarm->user_id = $user_id;
		$alarm->meldung = $_POST['msg'];
		$alarm->alarm_datum = $datum;
		$alarm->save();

		$pnEingangUser->gelesen = 0;
		$pnEingangUser->update_datum = $datum;
		$pnEingangUser->alarm_id = $alarm->alarm_id;
		$pnEingangUser->save();

		$this->redirect(array('pn/index', 'id' => $id, 'status' => 6));
	}
	
	public function actionSenden() {
		$empfaenger_gruppen 		= Yii::app()->request->getParam('empfaenger_gruppen');
		$pn_copy 					= Yii::app()->request->getParam('pn_copy');
		#$sanktion_typ = Yii::app()->request->getParam('sanktionen', UserBestrafung::KEINE_BESTRAFUNG);
		$status = false;

		// Neue Nachricht
		
		$model = new PNForm();
		
		if(isset($_POST['PNForm'])) {

			/*
			if (isset($_POST['absender']) && $_POST['absender'] == 'administrator' && Yii::app()->user->checkAccess('AlsAdminSchreiben')) {
				$absender_id = 693;		// Administrator
			} else {
				$absender_id = Yii::app()->user->getId();
			}
			*/
				
			$error = array();
			
			$model->absender_id 	= Yii::app()->user->getId();
			$model->empfaenger_id 	= $_POST['PNForm']['empfaenger_id'];
			$model->betreff 		= $_POST['PNForm']['betreff'];
			$model->nachricht 		= $_POST['PNForm']['nachricht'];
			
			if(isset($_POST['gruppen'])) {
				$model->empfaenger_id = 1;
				$empfaenger_gruppen = $_POST['gruppen'];
			}
			
			if($model->validate()) {
			
				$pn = new PN($model->absender_id);
				$empfaenger_ids = $model->empfaenger_id;
				if (count($empfaenger_gruppen) > 0) {	// PN an alle User einer oder mehrerer Rollen
	
					$pn->nachrichtInPnQueueSpeichern($model->nachricht, $model->betreff, $model->absender_id, $empfaenger_gruppen);

					Yii::app()->user->setFlash('pnStatusMsg',Yii::t('pn', 'nachrichten_zum_verschicken_vorbereitet'));
					
					$array = array('status'=>'success','url'=>Yii::app()->createUrl('pn/index'));
					echo CJSON::encode($array);
					Yii::app()->end();
				} else {
					//$mehrere_empfaenger_erlaubt = ($sanktion_typ == UserBestrafung::KEINE_BESTRAFUNG) ? true : false;
					$mehrere_empfaenger_erlaubt = true;
					$pn->setAttributesForNewPn($model->nachricht, $model->betreff, $model->absender_id, $empfaenger_ids, $mehrere_empfaenger_erlaubt);
		
					// Sanktionen durchfÃ¼hren
					/*
					if ($sanktion_typ != UserBestrafung::KEINE_BESTRAFUNG) {
						$sanktion = new Sanktion($pn->empfaenger_ids[0], $_POST['sanktionen']);
						if ($sanktion->saveSanktion($_POST['titel'], $_POST['msg'])) {
							$success = true;
							$status = 7;
						} else {
							$status = 10;
							$success = false;
						}
					} else {	// keine Sanktionen -> normale PN
						$success = true;
					}*/
					$success = true;
					if ($success) {
						$pn->save($pn_copy);
						
						Yii::app()->user->setFlash('pnStatusMsg',Yii::t('pn', 'nachricht_erfolgreich_verschickt'));

						$array = array('status'=>'success','url'=>Yii::app()->createUrl('pn/index'));
						echo CJSON::encode($array);
						Yii::app()->end();						
					}
				}
				$this->redirect(array('pn/index', 'status' => $status));
			} else {
				$error = CActiveForm::validate($model);
				if($error!='[]') {
					echo $error;
				}
				Yii::app()->end();				
			}
		}
		
	}
	
	public function actionAntworten() {
		
		// hole PNEingang Absender-Eintrag
		$pnEingangUser = PNEingang::model()->find('nachricht_id=:nachrichtID AND (empfaenger_id=:user_id)', 
				array(':nachrichtID' => (int)$_POST['ersteNachrichtId'], ':user_id' => Yii::app()->user->getId()));

//		$pnEingangUser->saveAntwort(Yii::app()->user->getId(), $_POST['msg']);

		if ($pnEingangUser->hasSystemAbsender()) {
			throw new CException('pn','systemmeldungen_keineantwort');
		}

		$pnNachricht = new PNNachricht();
		$pnNachricht->nachricht = $_POST['PNNachricht']['nachricht'];
		$pnNachricht->pn_datum = new CDbExpression('NOW()');
		$pnNachricht->erste_nachricht_id = $pnEingangUser->nachricht_id;
		$pnNachricht->absender_id = Yii::app()->user->getId();
		$pnNachricht->save();
		
 		$pnEingangUser->update_datum = new CDbExpression('NOW()');
 		$pnEingangUser->update_user_id = Yii::app()->user->getId();
 		$pnEingangUser->gelesen_datum = new CDbExpression('NOW()');
 		$pnEingangUser->gelesen = 1;
 		$pnEingangUser->save();
		

		// hole PNEingang EmpfÃ¤nger-Eintrag
		$pnEingangEmpfaenger = PNEingang::model()->find('nachricht_id=:nachrichtID AND (empfaenger_id!=:user_id)', 
				array(':nachrichtID' => (int)$_POST['ersteNachrichtId'], ':user_id' => Yii::app()->user->getId()));
		if($pnEingangEmpfaenger!=null) {
			$pnEingangEmpfaenger->gelesen = 0;
			$pnEingangEmpfaenger->update_datum = new CDbExpression('NOW()');
			$pnEingangEmpfaenger->save();
		} else {
			$newPNEingang = new PNEingang();
			$newPNEingang->nachricht_id 	= $pnEingangUser->nachricht_id;
			$newPNEingang->titel 			= $pnEingangUser->titel;
			$newPNEingang->absender_id 		= $pnEingangUser->empfaenger_id;
			$newPNEingang->empfaenger_id	= $pnEingangUser->absender_id;
			$newPNEingang->pn_datum 		= $pnEingangUser->pn_datum;
 			$newPNEingang->update_user_id 	= Yii::app()->user->getId();
			$newPNEingang->update_datum		= new CDbExpression('NOW()');
			$newPNEingang->gelesen 			= 0;
			$newPNEingang->save();
		}		

		// TODO: checke zugehörigkeit
		Yii::app()->user->setFlash('pnStatusMsg',Yii::t('pn', 'nachricht_erfolgreich_verschickt'));
		$this->redirect($this->createUrl('pn/index'));
	}

	public function actionEdit() {
		// Nachrichten Satus Ã¤ndern
		if (isset($_POST['aktion']) && $this->isMethodValid($_POST['aktion'])) {
			if (!isset($_POST['auswahl']) || count($_POST['auswahl']) < 1) {
				throw new CHttpException(404, Yii::t('error', 'Es wurde keine Nachricht ausgewÃ¤hlt!'));
			} else {
				$method = 'checked'.ucfirst($_POST['aktion']);
				$status = $this->$method($_POST['auswahl']);
			}
		} 
		Yii::app()->end();
		/*
		if (in_array($_POST['aktion'], $this->archivMethoden)) {
			$this->redirect(array('pn/archiv', 'status' => $status));
		} else {
			$this->redirect(array('pn/index', 'status' => $status));
		}
		*/
	}

	/*
	public function actionNachrichtAnzeigen() {
		$pn_id = Yii::app()->request->getParam('id');
		$pn = new PrivateNachricht(Yii::app()->user->getId(), 'posteingang', $pn_id);
		$pn->alsGelesenMarkieren();
		
		$this->render('nachrichtAnzeigen', array(
			'pn' => $pn,
		));
	}
	 */

	public function actionCreate() {
		// ist eine ID vorhanden, wird der name im EmpfÃ¤ngerfeld eingetragen 
		$user_id 		= Yii::app()->request->getParam('id');
		$quote_id 		= Yii::app()->request->getParam('quote', null);
		$squad_id 		= Yii::app()->request->getParam('squad');
		$role_id 		= Yii::app()->request->getParam('role');
		
		$prePopulate 		= false;
		$pnCopy 			= true;
		
		if (isset($user_id)) {
			$user = User::model()->cache(CACHETIME_S)->findByPk($user_id);
			if($user != null) {
				$prePopulate[] = array('id' => $user->user_id, 'name' => $user->user_nick);
			}
		} elseif(isset($squad_id) || isset($role_id)) { 
			/*
			 * man hat die MÃ¶glichkeit an eine Tipprunde oder Managerliga zu schreiben, sofern man Mitglied dieser ist.
			 * Hinweis: funktioniert nur, wenn keine ID Ã¼bergeben wird!
			 */
			$prePopulate = $this->getGruppenUserIds($squad_id, $role_id);
			if ($prePopulate) {
				$pnCopy = false;
			}
		}

		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.autosize.min.js');
		Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.validate.min.js');
		
		$model = new PNForm();
		
		$this->render('create', array(
			'userModel' 			=> Yii::app()->user,
			'prePopulate' 			=> $prePopulate,
			'pnCopy' 				=> $pnCopy,
			'zitat' 				=> $this->getZitat($quote_id),
			'model'					=> $model,	
		));
	}

	public function actionAlarmierteNachrichten() {
		$alarm_id = Yii::app()->request->getParam('alarm_id', false);
		$pn_id = Yii::app()->request->getParam('id');
		if (is_numeric($alarm_id)) {
			$erledigt = PNEingang::alarmAlsErledigtMarkieren($alarm_id);
			if ($erledigt) {
				Yii::app()->user->setFlash('pnStatusMeldung',Yii::t('privateNachrichten', 'alarm_erledigt'));
			} else {
				Yii::app()->user->setFlash('pnFehler',Yii::t('privateNachrichten', 'alarm_erledigt_fehler'));
			}
		}

		$dataProvider = $this->getAlarmDataProvider();

		if (!isset($pn_id) && $dataProvider->getTotalItemCount()) {
			$data = $dataProvider->getData();
			$pn_id = $data[0]->pn_id;
		}
			$pn = new PrivateNachricht(Yii::app()->user->getId(), 'alarm', $pn_id);
			$this->offene_pn_id = $pn->pn_id;
		
		$this->registerPostfachScriptFiles();
		$this->render('alarmierteNachrichten', array(
			'dataProvider' 	=> $dataProvider,
			'pn' 			=> $pn,
			'alarm' 		=> false,
			'postfach' 		=> 'alarm_admin',
		));
	}
	
	private function getZitat($nachricht_id) {
		$zitat = null;
		if (is_numeric($nachricht_id)) {
			// pn_nachricht laden
			$nachricht = PNNachricht::model()->findByPk($nachricht_id);
			$pn_eingang = $nachricht->getPNEingang();
			
			$user_id = Yii::app()->user->getId();
			// Der User darf nur Nachrichten zitieren/weiterleiten, die an ihn geschickt wurden, oder er selbst geschrieben hat!
			if ($pn_eingang instanceof PNEingang && ($pn_eingang->absender_id == $user_id || $pn_eingang->empfaenger_id == $user_id)) {
				$user = User::model()->findByPk($nachricht->absender_id);
				$absender = ($user instanceof User) ? $user->user_nick : Yii::t('global', 'unbekannt');
				$zitat = '[zitat='.$absender.']'.$nachricht->nachricht.'[/zitat]';
			} 
		}
		return $zitat;
	}

	/**
	 * @param string $postfach table_name (PNEingang oder PNArchiv)
	 */
	private function getDefaultDataProvider($postfach) {
		return new CActiveDataProvider($postfach, array(
		    'criteria'=>array(
		        'condition'=>'empfaenger_id='.Yii::app()->user->getId(),
		        'order'=>'update_datum DESC, pn_datum DESC',
		    ),
		    'pagination'=>array(
		        'pageSize'=>self::PN_ANZAHL_POSTEINGANG,
		    ),
		));
	}

	private function getAlarmDataProvider() {
		return new CActiveDataProvider('PNEingang', array(
		    'criteria'=>array(
		        'condition'=>'alarm_id > 0 AND alarm_erledigt = 0',
		        'order'=>'update_datum DESC, pn_datum DESC',
		    ),
		    'pagination'=>array(
		        'pageSize'=>self::PN_ANZAHL_POSTEINGANG,
		    ),
		));
	}
	
	/**
	 * @param string $postfach table_name (PNEingang oder PNArchiv)
	 */
	private function getFilterAbsenderId($postfach) {
		if (isset($_GET[$postfach])) {
			$this->filter_absender_id = (int)$_GET[$postfach]['absender_id'];
		} else {
			$absender_id = Yii::app()->request->getParam('absender_id', null);
			
			if (isset($absender_id) && is_numeric($absender_id)) {
				$this->filter_absender_id = (int)$absender_id;	
			}
		} 
		return $this->filter_absender_id;
	}

	private function getGruppenUserIds($tipprunde_id, $managerliga_id) {
		$array = array();
		$userIstTeilDerGruppe = false;
		if (isset($tipprunde_id)) {
			$sql = 'SELECT u2t.user_id,u.user_nick FROM tr_user_private_liga u2t LEFT JOIN v4_user u ON u2t.user_id = u.user_id WHERE u2t.liga_id = :liga_id';
			$command = Yii::app()->db->createCommand($sql);
			$command->bindValue(":liga_id", $tipprunde_id, PDO::PARAM_INT);
		} elseif (isset($managerliga_id)) {
			$sql = 'SELECT u2l.user_id,u.user_nick FROM ma_user2liga u2l LEFT JOIN v4_user u ON u2l.user_id = u.user_id WHERE u2l.liga_id = :liga_id';
			$command = Yii::app()->db->createCommand($sql);
			$command->bindValue(":liga_id", $managerliga_id, PDO::PARAM_INT);
		}
		$teilnehmer = $command->queryAll();
		foreach ($teilnehmer AS $user) {
			$array[] = array('id' => $user['user_id'], 'name' => $user['user_nick']);
			if ($user['user_id'] == Yii::app()->user->getId()) {
				$userIstTeilDerGruppe = true;
			}
		}
		if ($userIstTeilDerGruppe) {
			return $array;
		} else {
			return false;
		}
	}

	public function actionSearchPosteingangUser($q) {
		$this->searchPostfachUser($q, 'pn_eingang');
	}

	public function actionSearchArchivUser($q) {
		$this->searchPostfachUser($q, 'pn_archiv');
	}

	public function actionDelete() {
		$pn_id = Yii::app()->request->getParam('id');
		$status = $this->checkedDelete(array($pn_id));
		
		Yii::app()->user->setFlash('pnStatusMsg', Yii::t('pn','diskussion_erfolgreich_geloescht'));
		#$this->redirect(array('pn/index'));		
		
		$data['status'] 	= 'success';
			
		header('Content-type: application/json');
		echo CJSON::encode($data);
		Yii::app()->end();
	}

	public function actionDeleteArchiv() {
		$pn_id = Yii::app()->request->getParam('id');
		$status = $this->checkedDeleteArchiv(array($pn_id));
		Yii::app()->user->setFlash('pnStatusMsg', Yii::t('pn','diskussion_erfolgreich_geloescht_archiv'));
		#$this->redirect(array('pn/index'));		
		
		$data['status'] 	= 'success';
			
		header('Content-type: application/json');
		echo CJSON::encode($data);
		Yii::app()->end();
	}
	
	public function actionListe() {
    $pns = PNEingang::model()->findAll('empfaenger_id=:user_id ORDER BY update_datum DESC, pn_datum DESC LIMIT 5', array(':user_id' => Yii::app()->user->getId()));
    $this->layout = '_ajax';
		$this->render('_liste', array('pns' => $pns));
	}
	
	private function searchPostfachUser($q, $table) {
	    $term = trim($q);
	    $result = array();
	    if(!empty($term)) {
			$criteria = new CDbCriteria;
			$criteria->select = array('u.user_nick', 'u.user_id');
			$criteria->alias = 'u';
			$criteria->condition = 'empfaenger_id = :user_id AND user_nick like :user_nick';
			$criteria->params = array(':user_nick' => '%'.$term.'%', ':user_id' => Yii::app()->user->getId());
			$criteria->join = 'LEFT JOIN '.$table.' AS pn ON pn.absender_id = u.user_id';
			$criteria->group = 'u.user_nick ASC';
			$criteria->order = 'u.user_nick ASC';
			$cursor = User::model()->cache(CACHETIME_S)->findAll($criteria);

	        if(!empty($cursor)) {
	            foreach ($cursor as $id => $value) {
	                $result[] = array('id' => $value->user_id, 'name' => $value->user_nick);
	            }
	        }
	    }
	    header('Content-type: application/json');
	    echo CJSON::encode($result);
	    Yii::app()->end();
	}

	private function isMethodValid($method) {
		return in_array($method, array_merge($this->indexMethoden, $this->archivMethoden));
	}
	
	private function checkedDelete(array $auswahl) {
		foreach ($auswahl AS $pn_id) {
			$pn_id = (int)$pn_id;
			// PNEingang laden (empfaenger)
			$pnEingangUser = PNEingang::model()->find('pn_id=:pn_id AND (empfaenger_id=:user_id)', 
					array(':pn_id' => $pn_id, ':user_id' => Yii::app()->user->getId()));

			// nachricht kann nur gelÃ¶scht werden, wenn keine Alarmierung vorliegt.
			if ($pnEingangUser instanceof PNEingang && !$pnEingangUser->aktiveAlarmmeldung()) {
				// count = PNEingang zÃ¤hlen (absender)
				$sql1 = 'SELECT COUNT(pn_id) FROM pn_eingang WHERE nachricht_id = '.$pnEingangUser->nachricht_id.' AND absender_id = '.Yii::app()->user->getId();
				$absenderNachrichtEingang = (boolean)Yii::app()->db->createCommand($sql1)->queryScalar();

				// count = PNArchiv zÃ¤hlen (absender)
				$sql2 = 'SELECT COUNT(pn_id) FROM pn_archiv WHERE nachricht_id = '.$pnEingangUser->nachricht_id.' AND absender_id = '.Yii::app()->user->getId();
				$absenderNachrichtArchiv = (boolean)Yii::app()->db->createCommand($sql2)->queryScalar();

				// sollte der GesprÃ¤chspartner bereits seine Nachricht im Eingang/Archiv gelÃ¶scht haben, werden nun alle dazugehÃ¶rigen "pn_nachricht"en gelÃ¶scht
				if (!$absenderNachrichtEingang && !$absenderNachrichtArchiv) {
					$command = Yii::app()->db->createCommand();
					$command->delete('pn_nachricht', 'nachricht_id=:nachricht_id OR erste_nachricht_id=:nachricht_id', array(':nachricht_id' => $pnEingangUser->nachricht_id));
				}

				// PNEingang (empfaenger) lÃ¶schen
				$pnEingangUser->delete();
				$status = 2;	// siehe ->setStatusMessage()
			} else {
				$status = 4;	// siehe ->setStatusMessage()
			}
		}
		return $status;
	}
	
	private function checkedDeleteArchiv(array $auswahl) {
		foreach ($auswahl AS $pn_id) {
			$pn_id = (int)$pn_id;
			// PNArchiv laden (empfaenger)
			$pnArchivUser = PNArchiv::model()->find('pn_id=:pn_id AND (empfaenger_id=:user_id)', 
					array(':pn_id' => $pn_id, ':user_id' => Yii::app()->user->getId()));

			// count = PNEingang zÃ¤hlen (absender)
			$sql1 = 'SELECT COUNT(pn_id) FROM pn_eingang WHERE nachricht_id = '.$pnArchivUser->nachricht_id.' AND absender_id = '.Yii::app()->user->getId();
			$absenderNachrichtEingang = (boolean)Yii::app()->db->createCommand($sql1)->queryScalar();

			// count = PNEingang zÃ¤hlen (absender)
			$sql2 = 'SELECT COUNT(pn_id) FROM pn_archiv WHERE nachricht_id = '.$pnArchivUser->nachricht_id.' AND absender_id = '.Yii::app()->user->getId();
			$absenderNachrichtArchiv = (boolean)Yii::app()->db->createCommand($sql2)->queryScalar();

			// sollte der GesprÃ¤chspartner bereits seine Nachricht im Eingang/Archiv gelÃ¶scht haben, werden nun alle dazugehÃ¶rigen "pn_nachricht"en gelÃ¶scht
			if (!$absenderNachrichtEingang && !$absenderNachrichtArchiv) {
				$command = Yii::app()->db->createCommand();
				$command->delete('pn_nachricht', 'nachricht_id=:nachricht_id OR erste_nachricht_id=:nachricht_id', array(':nachricht_id' => $pnArchivUser->nachricht_id));
			}

			// PNArchiv (empfaenger) lÃ¶schen
			$pnArchivUser->delete();
		}
		return 2;	// siehe ->setStatusMessage()
	}

	private function checkedRead(array $auswahl) {
		$command = Yii::app()->db->createCommand();
		$command->update('pn_eingang', array(
		    'gelesen'=> 1,
		), array('AND', array('IN', 'pn_id', $auswahl), 'empfaenger_id=:user_id'), array(':user_id' => Yii::app()->user->getId()));
	}

	private function checkedUnRead(array $auswahl) {
		
		$command = Yii::app()->db->createCommand();
		$command->update('pn_eingang', array(
		    'gelesen'=> 0,
		), array('AND', array('IN', 'pn_id', $auswahl), 'empfaenger_id=:user_id'), array(':user_id' => Yii::app()->user->getId()));	
	}

	private function checkedArchive(array $auswahl) {
		foreach ($auswahl AS $pn_id) {
			$pn_id = (int)$pn_id;
			// PNEingang laden (empfaenger)
			$pnEingangUser = PNEingang::model()->find('pn_id=:pn_id AND (empfaenger_id=:user_id)', 
					array(':pn_id' => $pn_id, ':user_id' => Yii::app()->user->getId()));

			// nachricht kann nur archiviert werden, wenn keine Alarmierung vorliegt.
			if (is_null($pnEingangUser->alarm_id)) {
				// PNEingang-Kopie in PNArchiv speichern
				$pnArchiv = new PNArchiv();
				$pnArchiv->titel = $pnEingangUser->titel;
				$pnArchiv->nachricht_id = $pnEingangUser->nachricht_id;
				$pnArchiv->pn_datum = $pnEingangUser->pn_datum;
				$pnArchiv->absender_id = $pnEingangUser->absender_id;
				$pnArchiv->empfaenger_id = $pnEingangUser->empfaenger_id;
				$pnArchiv->weitergeleitet_flag = $pnEingangUser->weitergeleitet_flag;
				$pnArchiv->update_datum = date('Y-m-d H:i:s');
				$pnArchiv->update_user_id = Yii::app()->user->getId();
				$pnArchiv->save();

				// PNEingang (empfaenger) lÃ¶schen
				$pnEingangUser->delete();
			} else {
				// TODO: User darauf hinweisen, das eine alarmierung vorliegt, und die PN deshalb nicht archiviert werden kann.
			}
		}		
	}
	
	private function checkedInPosteingang(array $auswahl) {
		foreach ($auswahl AS $pn_id) {
			$pn_id = (int)$pn_id;
			// PNArchiv laden (empfaenger)
			$pnArchivUser = PNArchiv::model()->find('pn_id=:pn_id AND (empfaenger_id=:user_id)', 
					array(':pn_id' => $pn_id, ':user_id' => Yii::app()->user->getId()));

			// PNArchiv-Kopie in PNEingang speichern
			$pnEingang = new PNEingang();
			$pnEingang->titel = $pnArchivUser->titel;
			$pnEingang->nachricht_id = $pnArchivUser->nachricht_id;
			$pnEingang->pn_datum = $pnArchivUser->pn_datum;
			$pnEingang->absender_id = $pnArchivUser->absender_id;
			$pnEingang->empfaenger_id = $pnArchivUser->empfaenger_id;
			$pnEingang->weitergeleitet_flag = $pnArchivUser->weitergeleitet_flag;
			$pnEingang->update_datum = date('Y-m-d H:i:s');
			$pnEingang->gelesen_datum = date('Y-m-d H:i:s');
			$pnEingang->gelesen = 1;
			$pnEingang->update_user_id = Yii::app()->user->getId();
			$pnEingang->save();

			// PNArchiv lÃ¶schen (empfaenger)
			$pnArchivUser->delete();
		}		
	}
	
	private function setStatusMessage($status)	{
		switch ($status) {
			case 1:
				$statusMeldung = 'ihre_auswahl_wurde_archiviert';
				$statusType = 'pnStatusMeldung';
				break;
			case 2:
				$statusMeldung = 'ihre_auswahl_wurde_geloescht';
				$statusType = 'pnStatusMeldung';
				break;
			case 3:
				$statusMeldung = 'die_nachricht_wurde_geloescht';
				$statusType = 'pnStatusMeldung';
				break;
			case 4:
				$statusMeldung = 'fehler_auswahl_nicht_geloescht_alarm';
				$statusType = 'pnFehler';
				break;
			case 5:
				$statusMeldung = 'fehler_nachricht_nicht_geloescht';
				$statusType = 'pnFehler';
				break;
			case 6:
				$statusMeldung = 'die_nachricht_wurde_alarmiert';
				$statusType = 'pnStatusMeldung';
				break;
			case 7:
				$statusMeldung = 'sanktion_wurde_durchgefuehrt';
				$statusType = 'pnStatusMeldung';
				break;
			case 8:
				$statusMeldung = 'sanktion_wurde_nicht_durchgefuehrt';
				$statusType = 'pnFehler';
				break;
			case 9:
				$statusMeldung = 'PN an mehrere Usergruppen wurde in der Queue gespeichert und wird in den nÃ¤chsten Minuten verschickt.';
				$statusType = 'pnStatusMeldung';
				break;
			case 10:
				$statusMeldung = 'Die ausgewÃ¤hlte Sanktion konnte nicht durchgefÃ¼hrt werden, da bereits eine Ã¤hnliche Sanktion aktiv ist.';
				$statusType = 'pnFehler';
				break;
			default:
				return false;
				break;
		}		
		Yii::app()->user->setFlash($statusType,Yii::t('privateNachrichten', $statusMeldung));
	}
		

	/**
	 * Folgende Funktion wird im (Grid)View verwendet
	 */
	protected function defineCSS($row, $data) {
		
		$cssKlassen = $data['pn_id'].' ';
		if ($this->offene_pn_id == $data->pn_id) {
			$cssKlassen .= "active";
		} else {
			$cssKlassen .= '';//"pn_zeile";
		}
		if (isset($data->gelesen) && $data->gelesen == 0) {
			$cssKlassen .= '';//" pn_ungelesen";
		}
		
		if($row%2==0) {
			$cssKlassen.=' odd';
		} else {
			$cssKlassen.=' even';
		}
		
		return $cssKlassen;
	}

	/**
	 * Folgende Funktion wird im (Grid)View verwendet
	 * 
	 * data = PNEingang oder PNArchiv
	 */
	protected function pnAbsenderBetreffAnzeige($data, $row) {
		$page = array();
		$page_key = get_class($data).'_page';
		$page_value = Yii::app()->request->getParam($page_key, null);
		if ($page_value != null && is_numeric($page_value)) {
			$page = array($page_key => $page_value);
		}
		
		$user_id = $this->getGespraechspartnerId($data);
		
		$user = User::model()->findByPk($user_id);
		
		$userlink = '<div class="s10">';
		if ($user_id == 0) {	// Systemnachricht
			$userlink .= 'System';
		} else {
			$userlink .= $user->getHeadline();
		}
		
		
	
		$title = Yii::t('pn','unbeantwortet');
		$icon = TbHtml::tag('i',array('class'=>'icon-check-empty grey s14 s14 mt5 mr5','title'=>$title),'&nbsp;');
		
		
		
		if ($data->update_user_id == Yii::app()->user->getId()) {
			$title = Yii::t('pn','beantwortet');
			$icon = TbHtml::tag('i',array('class'=>'icon-check green s14 s14 mt5 mr5','title'=>$title),'&nbsp;');
		} 
		if (isset($data->gelesen) && $data->gelesen == 0) {
			$title = Yii::t('pn','ungelesen');
			$icon = TbHtml::tag('i',array('class'=>'icon-eye-close orange s14 mt5 mr5','title'=>$title),'&nbsp;');
		}
		if ($data->weitergeleitet_flag == 1) {
			$title = Yii::t('pn','weitergeleitet');
			$icon.= TbHtml::tag('i',array('class'=>'icon-mail-forward green ml5 s14 mt5 mr5','title'=>$title),'&nbsp;');
		}		
		
		#GFunctions::pre($data);
		
		/*
		
		if (isset($data->gelesen) && $data->gelesen == 0) {
			$icon = TbHtml::tag('i',array('class'=>'icon-eye-close orange'),'&nbsp;');
		} else { 
			$icon = TbHtml::tag('i',array('class'=>'icon-eye-close orange'),'&nbsp;');
		}
		
		
		if($data->update_user_id == Yii::app()->user->getId()) {
			$icon = TbHtml::tag('i',array('class'=>'icon-check green'),'&nbsp;');
		} elseif ($data->weitergeleitet_flag == 1 && $data->gelesen == 1) {
			$icon = TbHtml::tag('i',array('class'=>'icon-mail-forward green'),'&nbsp;');
		}		
		*/
		$userlink.= TbHtml::tag('span',array('class'=>'fr'),$icon);
		
		$userlink .= "</div>";

		$betreff = '<div class="s10">'.$data->getGekuerztenTitel().'</div>';
		$betreff .= TbHtml::tag('span',array('class'=>'pn_target dn'),$data->getUrl($this->filter_absender_id, $page));
		return $userlink . $betreff;
	}
	
	/**
	 * Folgende Funktion wird im (Grid)View verwendet
	 */
	protected function pnAlarmBetreffAnzeige($data, $row) {
		$absenderNick = User::getUserNick($data['absender_id']);
		$empfaengerNick = User::getUserNick($data['empfaenger_id']);
		$userlink = '<div>';
		$userlink .= "<b>".User::getStaticHtmlLink(array("user_id" => $data['absender_id'], "user_nick" => $absenderNick))."</b>";
		$userlink .= ", ";
		$userlink .= "<b>".User::getStaticHtmlLink(array("user_id" => $data['empfaenger_id'], "user_nick" => $empfaengerNick))."</b>";
		$userlink .= "</div><br/>";
		$betreff = '<div>'.$data->getAlarmHtmlLink(false).'</div>';
		return $userlink . $betreff;
	}
	
	/**
	 * Folgende Funktion wird im (Grid)View verwendet
	 */
	protected function pnUserFoto($data, $row) {
		return true;
		$user2foto = User2Foto::model()->cache(CACHETIME_XL)->findByAttributes(array('user_id'=>$this->getGespraechspartnerId($data)));
		if ($user2foto instanceof User2Foto) {
			$foto = $user2foto->getFoto();
		} else {
			$foto = 'default.jpg';
		}

		return CHtml::link(Yii::app()->controller->widget("ext.SAImageDisplayer", array(
				"image" => $foto,
				"defaultImage" => "default.jpg",
				"size" => "medium",
				"group" => "user",
				"title" => "",
				"alt" => "",
				"class" => "pn-user-bild"
		),true));
	}

	/**
	 * Folgende Funktion wird im (Grid)View verwendet
	 */
	protected function pnDatumAnzeigen($data, $row) {
		$statusCSSClass = 'icon-pn-unbeantwortet';
		if ($data->update_user_id == Yii::app()->user->getId()) {
			$statusCSSClass = 'icon-check green';
		} elseif (isset($data->gelesen) && $data->gelesen == 0) {
			$statusCSSClass = 'icon-pn-ungelesen';
		}
		if ($data->weitergeleitet_flag == 1) {
			$statusCSSClass .= ' icon-pn-weitergeleitet';
		}
		$statusicon = '<div class="pn_sprite '.$statusCSSClass.' pn-icon-grid-view"></div>';
		
		if ($data->update_datum > 0) {
			return '<div class="s10">'.Yii::app()->dateFormatter->formatDateTimeAnzeige($data->update_datum,'short','short',', ',false) .'</div>';
		} else {
			return '<div class="s10">'.Yii::app()->dateFormatter->formatDateTimeAnzeige($data->pn_datum, 'short','short',', ',false) .'</div>';
		}
	}
	
	/**
	 * @param mixed $data (PNEingang oder PNArchiv)
	 */
	private function getGespraechspartnerId($data) {
		return (Yii::app()->user->getId() == $data->absender_id) ? $data->empfaenger_id : $data->absender_id;
	}
}