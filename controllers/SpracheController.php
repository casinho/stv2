<?php
class SpracheController extends Controller {
	public $hauptsprache = 'de';
	public $messagePath = '';
	public $disableICheck = true;
	public $subNavi = array();

	protected function beforeAction($action) {
		set_time_limit(0);
		parent::beforeAction($action);
		if(isset(Yii::app()->log)) {
			$noLogging = array('exporttoxml', 'importvonsourcefiles', 'integritaet', 'importfromxml');
			if(in_array(strtolower($action->getId()), $noLogging)) {
				foreach (Yii::app()->log->routes as $route) {
					if ($route instanceof CWebLogRoute) {
						$route->enabled = false;
					}
				}
			}
		}
		return true;
	}

	public function createSubMenu() {
		$this->subNavi = array(
			'Übersicht' => array('index')
		);

		if(Yii::app()->user->checkAccess('Superadmin')) {
			$this->subNavi['Import/Export'] = array(
				'#',
				array(
					'Import' => array(
						Yii::t('admin', 'Import in DB aus Sources') => 'ImportVonSourceFiles',
						Yii::t('admin', 'Import in DB aus Language-Files') => 'ImportVonMessageFiles',
					),
					'Export' => array(
						Yii::t('admin', 'Export in Language-Files') => 'Export',
						Yii::t('admin', 'Export in XML-Datei') => 'ExportToXML',
					),
					'Merge' => array(
						Yii::t('admin', 'Übersetzungen zusammenführen') => 'Merge',
					)
				),
			);
		};
	}

	public function init() {
		// message-Pfad
		// Per default immer davon ausgehen, dass die Deutschen dateien vorhanden sind
		$this->messagePath = Yii::app()->basePath.DIRECTORY_SEPARATOR.'messages'.DIRECTORY_SEPARATOR;
		$this->createSubMenu();
		parent::init();
	}

	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules() {
		return array(
			array('allow',
				'roles'=>array('Superadmin'),
				'actions' => array('admin', 'adminPartial', 'index', 'translate'),
			),
			array('allow',
				'roles' => array('Superadmin'),
				'actions' => array('dubletten', 'export', 'exporttoxml', 'importfromxml', 'importvonsourcefiles', 'importvonmessagefiles', 'integritaet', 'merge', 'mergevorschau', 'createMissingTranslations'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex() {
		$sprachen = SystemSpracheUebersetzt::model()->getSprachen();
		foreach($sprachen as $sprache) {
			$fehlendeUebersetzungen[$sprache->sprache] = SystemSpracheQuelle::getAnzahlFehlendeUebersetzungen($sprache->sprache);
//			$fehlendeUebersetzungen[$sprache->sprache] = count(SystemSpracheQuelle::getAllFehlendeUebersetzungen($sprache->sprache));
		}
		
		$this->render('index', compact('sprachen', 'fehlendeUebersetzungen'));
	}

	/**
	 * @var sprache string Die zu Übersetzende Sprache
	 * @var modus integer 0: Nur neue (DEFAULT), 1: Nur vorhandene, 2: Alle
	 */
	public function actionAdmin($sprache, $modus = 0) {
		$dbDateTime = date('Y-m-d H:i:s');
		if(isset($_POST['SystemSpracheQuelle'])) {
			foreach($_POST['SystemSpracheQuelle'] as $quelle_id => $postValues) {
				$aktiveAttributes['sprache'] = $sprache;
				$aktiveAttributes['aktiv'] = 1;
				$aktiveAttributes['sprache_quelle_id'] = $quelle_id;
				$aktiveUebersetzung = SystemSpracheUebersetzt::model()->findByAttributes($aktiveAttributes);

				if(strcmp($aktiveUebersetzung->value, $postValues['value']) != 0) {
					$aktiveUebersetzung->aktiv = 0;
					$alteVersion = $aktiveUebersetzung->version;
					if($aktiveUebersetzung->save()) {
						$neueUebersetzung = new SystemSpracheUebersetzt;
						$attributes['version'] = $alteVersion+1; 
						$attributes['aktiv'] = 1;
						$attributes['sprache'] = $sprache;
						$attributes['sprache_quelle_id'] = $quelle_id;
						$attributes['value'] = $postValues['value'];
						$attributes['create_time'] = $dbDateTime;
						$attributes['last_modified_time'] = $dbDateTime;
						$attributes['last_modified_user_id'] = 0;
						$neueUebersetzung->attributes = $attributes;
						if(!$neueUebersetzung->save()) {
							$aktiveUebersetzung->aktiv = 1;
							$aktiveUebersetzung->save();
						}
					}
				}
			}
            Yii::app()->user->setFlash('gespeichert', Yii::t('admin', 'Übersetzungen wurden gespeichert und werden demnächst veröffentlicht.'));
		}

		$criteria = new CDbCriteria;

		$criteria->select = '*, aktiveUebersetzung.value AS value';
		$criteria->condition = 'kategorie != "oldLang"';
		if($modus == 0) {
			$criteria->with = array(
				'aktiveUebersetzung' => array(
					'scopes' => array(
						'leereUebersetzung',
						'sprache' => $sprache,
					),
				),
			);
		} elseif ($modus == 1) {
			$criteria->with = array(
				'aktiveUebersetzung' => array(
					'scopes' => array(
						'vorhandeneUebersetzung',
						'sprache' => $sprache,
					),
				),
			);
		} elseif ($modus == 2) {
			$criteria->with = array(
				'aktiveUebersetzung' => array(
					'scopes' => array(
						'sprache' => $sprache,
					),
				),
			);
		}

		$fehlendeUebersetzungen = new CActiveDataProvider(new SystemSpracheQuelle, array(
			'criteria' => $criteria,
    		'pagination'=>array(
        		'pageSize'=>20,
    		),
		));

		$this->render('admin', compact('fehlendeUebersetzungen'));
	}

	public function actionAdminPartial() {
		$sprache = Yii::app()->request->getParam('sprache');
		$quell_ids = Yii::app()->request->getParam('quell_ids');
		
		$dbDateTime = date('Y-m-d H:i:s');
		if(isset($_POST['SystemSpracheQuelle'])) {
            $quell_ids = array();
			foreach($_POST['SystemSpracheQuelle'] as $quelle_id => $postValues) {
                $quell_ids[] = $quelle_id;
				$aktiveAttributes['sprache'] = $sprache;
				$aktiveAttributes['aktiv'] = 1;
				$aktiveAttributes['sprache_quelle_id'] = $quelle_id;
				$aktiveUebersetzung = SystemSpracheUebersetzt::model()->findByAttributes($aktiveAttributes);

				if(strcmp($aktiveUebersetzung->value, $postValues['value']) != 0) {
					$aktiveUebersetzung->aktiv = 0;
					$alteVersion = $aktiveUebersetzung->version;
					if($aktiveUebersetzung->save()) {
						$neueUebersetzung = new SystemSpracheUebersetzt;
						$attributes['version'] = $alteVersion+1; 
						$attributes['aktiv'] = 1;
						$attributes['sprache'] = $sprache;
						$attributes['sprache_quelle_id'] = $quelle_id;
						$attributes['value'] = $postValues['value'];
						$attributes['create_time'] = $dbDateTime;
						$attributes['last_modified_time'] = $dbDateTime;
						$attributes['last_modified_user_id'] = 0;
						$neueUebersetzung->attributes = $attributes;
						if(!$neueUebersetzung->save()) {
							$aktiveUebersetzung->aktiv = 1;
							$aktiveUebersetzung->save();
						}
					}
				}
			}
            Yii::app()->user->setFlash('gespeichert', Yii::t('admin', 'Übersetzungen wurden gespeichert und werden demnächst veröffentlicht.'));
		}

		$criteria = new CDbCriteria;
		$criteria->select = '*, aktiveUebersetzung.value AS value';
		$criteria->with = array(
			'aktiveUebersetzung' => array(
				'scopes' => array(
					'aktiv',
					'sprache' => $sprache,
				),
			),
		);
		$criteria->addInCondition('t.id', $quell_ids);
		
		$fehlendeUebersetzungen = new CActiveDataProvider(new SystemSpracheQuelle, array(
			'criteria' => $criteria,
    		'pagination'=>array(
        		'pageSize'=>500,
    		),
		));

		$this->render('admin', compact('fehlendeUebersetzungen'));
	}



	/*
	public function actionImportAlteSprachen($sprachkuerzel) {
		$sql = 'SELECT COUNT(id) FROM system_sprache_uebersetzt WHERE sprache = :sprache';
		$command = Yii::app()->db->createCommand($sql);

	}
	*/

	public function actionMergeVorschau() {
		set_time_limit(0);
		$languages = array('de', 'en', 'it', 'fr', 'pt', 'tr', 'pl', 'es', 'nl');
		/*
		 * sucht für jede Sprache, ob es zu noch nicht vorhandenen Übersetzungen schon einen identischen key gibt, der schon übersetzt ist, und trägt diese Übersetzung ein
		 */
		foreach($languages as $sprache) {
			$sql = 'SELECT sprache_quelle_id, system_sprache_quelle.key, system_sprache_uebersetzt.id 
					FROM system_sprache_uebersetzt 
					INNER JOIN system_sprache_quelle ON system_sprache_uebersetzt.sprache_quelle_id = system_sprache_quelle.id 
					WHERE sprache = :sprache AND aktiv = 1 AND (system_sprache_uebersetzt.value IS NULL OR system_sprache_uebersetzt.value = "")';
			$command = Yii::app()->db->createCommand($sql);
			$command->bindParam(':sprache', $sprache, PDO::PARAM_STR);
			$reader = $command->query();
			while(($row = $reader->read()) !== false) {
				$sql = 'SELECT count(*) FROM system_sprache_quelle WHERE system_sprache_quelle.key = :key';
				$command = Yii::app()->db->createCommand($sql);
				$command->bindParam(':key', $row['key'], PDO::PARAM_STR);
				$n = $command->queryScalar();
				if($n > 1) {
					$sql = 'SELECT value 
							FROM system_sprache_uebersetzt 
							JOIN system_sprache_quelle ON system_sprache_quelle.id = sprache_quelle_id AND system_sprache_quelle.key = :key 
							WHERE value != "" AND value IS NOT NULL AND sprache = :sprache LIMIT 1';
					$command = Yii::app()->db->createCommand($sql);
					$command->bindParam(':key', $row['key'], PDO::PARAM_STR);
					$command->bindParam(':sprache', $sprache, PDO::PARAM_STR);
					$reader2 = $command->query();
					while(($result = $reader2->read()) !== false) {
						$uebersetzung = SystemSpracheUebersetzt::model()->findByPK($row['id']);
						$uebersetzung->last_modified_time = date('Y-m-d H:i:s');
						$uebersetzung->value = $result['value'];
						$uebersetzung->version++;
						$uebersetzung->save();
					}
				}
			}
		}
		/*
		 * sucht neue Übersetzungen, deren Value dem einer alten Übersetzung entspricht (auf deutsch), und trägt für die anderen Sprachen vorhandene Übersetzungen nach
		 */
/*
		$sql = 'SELECT ssu.sprache_quelle_id AS sqOld, ssu2.sprache_quelle_id AS sqNew FROM system_sprache_uebersetzt AS ssu INNER JOIN system_sprache_uebersetzt AS ssu2 ON ssu.value = ssu2.value AND ssu.sprache_quelle_id != ssu2.sprache_quelle_id INNER JOIN system_sprache_quelle AS ssq ON ssq.id = ssu2.sprache_quelle_id WHERE ssu.sprache = "de" AND ssu2.sprache = "de" AND ssu.value IS NOT NULL AND ssu.value != "" AND ssq.kategorie != "oldLang"';
		$command = Yii::app()->db->createCommand($sql);
		$reader = $command->query();
		while(($row = $reader->read()) !== false) {
			$sql = "SELECT id, sprache_quelle_id, sprache FROM system_sprache_uebersetzt WHERE sprache_quelle_id = :newId AND aktiv = 1 AND (value IS NULL OR value = '')";
			$command2 = Yii::app()->db->createCommand($sql);
			$command2->bindParam(':newId', $row['sqNew'], PDO::PARAM_STR);
			$reader2 = $command2->query();
			while(($result = $reader2->read()) !== false) {
				$sql = "SELECT value FROM system_sprache_uebersetzt WHERE sprache_quelle_id = :oldId AND sprache = :sprache AND aktiv = 1 AND (value IS NOT NULL OR value != '')";
				$command3 = Yii::app()->db->createCommand($sql);
				$command3->bindParam(':oldId', $row['sqOld'], PDO::PARAM_STR);
				$command3->bindParam(':sprache', $result['sprache'], PDO::PARAM_STR);
				$uebersetzt = $command3->queryScalar();
				if($uebersetzt) {
					$uebersetzung = SystemSpracheUebersetzt::model()->findByPK($result['id']);
					$uebersetzung->last_modified_time = date('Y-m-d H:i:s');
					$uebersetzung->value = $uebersetzt;
					$uebersetzung->version++;
					$uebersetzung->save();
				}
			}
		}
*/

		/*
		 * sucht neue Übersetzungen, deren Key dem Value einer alten Übersetzung entspricht (auf deutsch), und trägt für die anderen Sprachen vorhandene Übersetzungen nach
		 */
/*
		$sql = 'SELECT ssu.sprache_quelle_id AS sqOld, ssq.id AS sqNew FROM system_sprache_uebersetzt AS ssu INNER JOIN system_sprache_quelle AS ssq ON ssq.key = ssu.value INNER JOIN system_sprache_uebersetzt AS ssu2 ON ssq.id = ssu2.sprache_quelle_id AND (ssu2.value IS NULL OR ssu2.value="") AND ssu2.sprache="de" AND ssu2.aktiv = 1 WHERE ssu.sprache = "de"';
		$command = Yii::app()->db->createCommand($sql);
		$reader = $command->query();
		while(($row = $reader->read()) !== false) {
			$sql = "SELECT id, sprache_quelle_id, sprache FROM system_sprache_uebersetzt WHERE sprache_quelle_id = :newId AND aktiv = 1 AND (value IS NULL OR value = '')";
			$command2 = Yii::app()->db->createCommand($sql);
			$command2->bindParam(':newId', $row['sqNew'], PDO::PARAM_STR);
			$reader2 = $command2->query();
			while(($result = $reader2->read()) !== false) {
				$sql = "SELECT value FROM system_sprache_uebersetzt WHERE sprache_quelle_id = :oldId AND sprache = :sprache AND aktiv = 1 AND (value IS NOT NULL OR value != '')";
				$command3 = Yii::app()->db->createCommand($sql);
				$command3->bindParam(':oldId', $row['sqOld'], PDO::PARAM_STR);
				$command3->bindParam(':sprache', $result['sprache'], PDO::PARAM_STR);
				$uebersetzt = $command3->queryScalar();
				if($uebersetzt) {
					$uebersetzung = SystemSpracheUebersetzt::model()->findByPK($result['id']);
					$uebersetzung->last_modified_time = date('Y-m-d H:i:s');
					$uebersetzung->value = $uebersetzt;
					$uebersetzung->version++;
					$uebersetzung->save();
				}
			}
		}*/
	}

	public function actionMerge() {
		$params['uebersetzung_id'] = Yii::app()->request->getParam('uebersetzung_id', 0);
		$params['wiedergefunden_id'] = Yii::app()->request->getParam('wiedergefunden_id', 0);
		// print_r($params);
	}

    /*
	public function actionTranslate() {
//		debug($_POST);
//		exit;

		$quellen = $_POST['tmTrans-missing'];
		$sprache = 'de';

		if(!empty($quellen)) {
			foreach($quellen as $kategorie => $messages) {
				foreach ($messages as $message => $nix) {
debug($kategorie);
debug($message);
					$quelle = SystemSpracheQuelle::model()->useMaster()->findByAttributes(array('kategorie' => $kategorie, 'key' => $message));
debug($quelle);
debug($quelle->id);
					$test = SystemSpracheUebersetzt::model()->useMaster()->findByAttributes(array('sprache_quelle_id' => $quelle->id, 'sprache' => 'de'));
//					$models[$kategorie] = SystemSpracheQuelle::model()->with(array('aktiveUebersetzung' => array('scopes' => array('sprache' => $sprache))))->findByAttributes(array('kategorie' => $kategorie, 'key' => $message));

debug($test);
exit;
					/*
					if($sprache == 'de') {
						$models[$kategorie]['value_de'] = '---';
					} else {
						$datensatz_de = SystemSpracheQuelle::model()->with(array('aktiveUebersetzung' => array('scopes' => array('sprache' => 'de'))))->findByAttributes(array('id' => $quelle['sprache_quelle_id']));
						$models[$kategorie]['value_de'] = $datensatz_de->aktiveUebersetzung->value;
					}
					 */

    /*
				}
			}
		}
		$this->render('translate', compact('models', 'versionForm'));
		
	}
*/
	public function actionTranslate() {
		
		if(isset($_POST['SystemSpracheQuelle']) && isset($_POST['SystemSpracheUebersetzt'])) {
			$success = true;
			foreach($_POST['SystemSpracheUebersetzt'] as $quelle_id => $arr) {
				$sprache = $arr['sprache'];
				$updateAttributes = array('sprache' => $arr['sprache'], 'sprache_quelle_id' => $quelle_id, 'newValue' => $arr['value']);
				$success = SystemSpracheUebersetzt::updateUebersetzung($updateAttributes) && $success;
			}
			if(!$success) {
				echo "nichts ist gut";
				die();
			}
			$this->redirect(array('admin', 'sprache' => $sprache));
		}

		if(isset($_GET['quelle_id']) && isset($_GET['sprache'])) {
			// Ändern einer einzelnen Übersetzung
			$versionierungFormModel = new adminSpracheDetailUpdateVersionForm;
			$versionForm = new testform('application.views.adminsprache._Form_detailUpdate_Versionierung', $versionierungFormModel);

			if($versionForm->submitted('speichern')) {
				// Ändern der aktuellen Version
				// print_r($versionierungFormModel->attributes);
				$updateAttributes['sprache_quelle_id'] = $_GET['quelle_id'];
				$updateAttributes['sprache'] = $_GET['sprache'];
				$updateAttributes['version'] = $versionierungFormModel['version'];
				if(SystemSpracheUebersetzt::changeAktiveUebersetzung($updateAttributes)) {
					$this->redirect(array('admin', 'sprache' => $_GET['sprache']));
				}
			}

			$quellen[$_GET['quelle_id']] = array('sprache' => $_GET['sprache'], 'sprache_quelle_id' => $_GET['quelle_id']);
		} elseif(isset($_POST['tmTrans-missing']) && is_array($_POST['tmTrans-missing'])) {
			// Ändern von mehreren Übersetzungen
			$versionForm = null;
			$quellen = $_POST['tmTrans-missing'];
		}

		if(!empty($quellen)) {
			foreach($quellen as $id => $quelle) {
				$models[$id] = SystemSpracheQuelle::model()->with(array('aktiveUebersetzung' => array('scopes' => array('sprache' => $quelle['sprache']))))->findByAttributes(array('id' => $quelle['sprache_quelle_id']));
				if($quelle['sprache'] == 'de') {
					$models[$id]['value_de'] = '---';
				} else {
					$datensatz_de = SystemSpracheQuelle::model()->with(array('aktiveUebersetzung' => array('scopes' => array('sprache' => 'de'))))->findByAttributes(array('id' => $quelle['sprache_quelle_id']));
					$models[$id]['value_de'] = $datensatz_de->aktiveUebersetzung->value;
				}
			}
		}
		$this->render('translate', compact('models', 'versionForm'));
	}

	/**
	 * Diese Methode arbeitet Ähnlich wie die run() Methode der MessageCommand.php aus den Yii-Sources
	 */
	public function actionImportVonSourceFiles() {
		set_time_limit(0);
		ini_set('memory_limit', '2048M');
		if(!is_file($this->messagePath.'config.php')) {
			throw new CHttpException('Konfigurationsdatei fehlt!');
		}
		$config = require_once($this->messagePath.'config.php');
		$translator='Yii::t';
		extract($config);	// MAGIC = 
		

		if(!isset($sourcePath,$messagePath,$languages)) {
			$this->usageError('The configuration file must specify "sourcePath", "messagePath" and "languages".');
		}
		if(!is_dir($sourcePath)) {
			$this->usageError("The source path $sourcePath is not a valid directory.");
		}
		if(!is_dir($messagePath)) {
			$this->usageError("The message path $messagePath is not a valid directory.");
		}
		if(empty($languages)) {
			$this->usageError("Languages cannot be empty.");
		}

		if(!isset($overwrite)) {
			$overwrite = false;
		}

		if(!isset($removeOld)) {
			$removeOld = false;
		}

		if(!isset($sort)) {
			$sort = false;
		}

		$options=array();
		if(isset($fileTypes)) {
			$options['fileTypes']=$fileTypes;
		}

		if(isset($exclude)) {
			$options['exclude']=$exclude;
		}
		$files=CFileHelper::findFiles(realpath($sourcePath),$options);

		$messages=array();
		foreach($files as $file) {
//			if ($file == 'C:\xampp\htdocs\workspace\tmv4\protected\views\site\index.php') {
				$messages=array_merge_recursive($messages, $this->extractMessages($file, $translator));
//			}
		}

// debug($messages);
 //exit;

		//$db = Yii::app()->dbMaster;
		
	//	SystemSpracheQuellzuweisung::model()->deleteAll();

		foreach($messages as $kategorie => $msgs) {
			foreach($msgs as $msg) {
				unset($systemSpracheQuelle);
				$schluessel = $msg['str'];
				$quelldatei = $msg['quelldatei'];
				
				$schluesselHash = md5($kategorie.$schluessel);
				$neueQuelle = 0;

	/*
				// Übersetzungsstring in system_sprache_quelle speichern, sofern noch nicht vorhanden.
				if(!isset($quellen_ids[$schluesselHash])) {
					$command = $db->createCommand('SELECT id FROM system_sprache_quelle WHERE `kategorie` = :kategorie AND `key` = :schluessel')->bindParam(':kategorie', $kategorie)->bindParam(':schluessel', $schluessel);
					$quellen_ids[$schluesselHash] = $command->queryScalar();
					if(!isset($quellen_ids[$schluesselHash]) || $quellen_ids[$schluesselHash] == false) {
						$quellen_ids[$schluesselHash] = $this->erstelleSpracheQuelle($schluessel, $kategorie);
					}
				}
*/

				// Quelle auslesen, sofern vorhanden
				$systemSpracheQuelle = SystemSpracheQuelle::model()->findByAttributes(array('kategorie' => $kategorie, 'key' => $schluessel));

				if(!is_object($systemSpracheQuelle)) {
					$systemSpracheQuelle = $this->erstelleSpracheQuelle($schluessel, $kategorie);
					$neueQuelle = 1;
				}

				$quellen_ids[$schluesselHash] = $systemSpracheQuelle->id;


/*
				// gibt es bereits einen Datensatz für die Quelldatei?
				$systemSpracheQuelldatei = SystemSpracheQuelldatei::model()->useMaster()->findByAttributes(array('quelldatei' => $quelldatei));
				if(!is_object($systemSpracheQuelldatei)) {
					$systemSpracheQuelldatei = new SystemSpracheQuelldatei;
					$systemSpracheQuelldatei->quelldatei = $quelldatei;
					$systemSpracheQuelldatei->save();
				}
				// Quelldatei-Zuweisung speichern
				$zuweisung = new SystemSpracheQuellzuweisung;
				$zuweisung->quelle_id = $systemSpracheQuelle->id;
				$zuweisung->quelldatei_id = $systemSpracheQuelldatei->id;
				$zuweisung->save();
 */

				// Übersetzungsstring in system_sprache_quelle_neu speichern, sofern noch nicht vorhanden.
				if(!isset($quellen_ids_neu[$schluesselHash])) {
					$command = Yii::app()->db->createCommand('SELECT id FROM system_sprache_quelle_neu WHERE `kategorie` = :kategorie AND `key` = :schluessel')->bindParam(':kategorie', $kategorie)->bindParam(':schluessel', $schluessel);
					$quellen_ids_neu[$schluesselHash] = $command->queryScalar();
					if(!isset($quellen_ids_neu[$schluesselHash]) || $quellen_ids_neu[$schluesselHash] == false) {
						$quellen_ids_neu[$schluesselHash] = $this->erstelleSpracheQuelleNeu($schluessel, $kategorie);
					}
				}

				foreach($languages as $sprache) {
					/*
					$sucheUebersetzung = array(
						'aktiv' => 1,
						'sprache' => $sprache,
						'sprache_quelle_id' => $quellen_ids[$schluesselHash],
					);
					 */

					if(!isset($uebersetzungen[$sprache.'-'.$quellen_ids[$schluesselHash]]) || empty($uebersetzungen[$sprache.'-'.$quellen_ids[$schluesselHash]])) {
						$uebersetzungen[$sprache.'-'.$quellen_ids[$schluesselHash]] = ($neueQuelle == 0 ? $this->existiertUebersetzungProzedual($sprache, $quellen_ids[$schluesselHash], 1) : 0);
					}

					/* Die Einträge die hier geschrieben werden, können nur leer sein.
					 * Aus dem grund können wir das erstellen überspringen, sollte es schon eine
					 * Übersetzung geben (z.B. durch die Migration)
					 */
					if($uebersetzungen[$sprache.'-'.$quellen_ids[$schluesselHash]] !== false) {
						continue;
					}

					$uebersetzungenZumAnlegen[] = array(
						'sprache' => $sprache,
						'quelle_id' => $quellen_ids[$schluesselHash],
					);
					// $uebersetzungen[$sprache.'-'.$quellen_ids[$schluesselHash]] = -1;
				}
				if(isset($uebersetzungenZumAnlegen) && count($uebersetzungenZumAnlegen) > 0) {
					$this->erstelleUebersetzungenMultiple($uebersetzungenZumAnlegen);
					unset($uebersetzungenZumAnlegen);
				}
			}
		}
		// hole alle Datensätze aus system_sprache_quelle
		$sprache_quellen = SystemSpracheQuelle::model()->findAll();

		foreach ($sprache_quellen as $k => $sprache_quelle) {
			// existiert der Datensatz nicht in SystemSpracheQuelleNeu, wird er in SystemSpracheQuelle gelöscht. Außerdem werden alle dazugehörigen Übersetzungen gelöscht.
			$kategorie = $sprache_quelle->kategorie;
			$key = $sprache_quelle->key;
			$command = Yii::app()->db->createCommand("SELECT count(id) FROM system_sprache_quelle_neu WHERE kategorie = :kategorie AND `key` = :schluessel")->bindValue(':kategorie', $kategorie)->bindValue(':schluessel', $key);
			
			if($command->queryScalar() < 1) {
				echo $sprache_quelle->id .'<br/>';
				/*
				SystemSpracheUebersetzt::model()->deleteAll('sprache_quelle_id = :quelle_id ', array(':quelle_id' => $sprache_quelle->id));
				$sprache_quelle->delete();
				 */
			}
			
			
		}

		// system_sprache_quelle_neu leeren
		Yii::app()->db->createCommand()->truncateTable('system_sprache_quelle_neu');
		echo 'DONE!!';
	}

	private function existiertUebersetzungProzedual($sprache, $quelle_id, $aktiv) {
		$db = Yii::app()->db;
		$result = $db->createCommand('SELECT id FROM system_sprache_uebersetzt WHERE aktiv = :aktiv AND sprache = :sprache AND sprache_quelle_id = :quelle_id')
			->bindParam(':aktiv', $aktiv)
			->bindParam(':sprache', $sprache)
			->bindParam(':quelle_id', $quelle_id)
			->queryScalar();
		return $result;
	}
	
	public function actionCreateMissingTranslations() {
		if(!is_file($this->messagePath.'config.php')) {
			throw new CHttpException('Konfigurationsdatei fehlt!');
		}
		$config = require_once($this->messagePath.'config.php');
		extract($config);


		// hole alle Datensätze aus system_sprache_quelle
		$sprache_quellen = SystemSpracheQuelle::model()->findAll();

		foreach ($sprache_quellen as $k => $sprache_quelle) {
			// existiert der Datensatz nicht in SystemSpracheQuelleNeu, wird er in SystemSpracheQuelle gelöscht. Außerdem werden alle dazugehörigen Übersetzungen gelöscht.
			$command = Yii::app()->db->createCommand("SELECT count(id) FROM system_sprache_uebersetzt WHERE sprache_quelle_id = :quelle_id")->bindValue(':quelle_id', $sprache_quelle->id);
			
			if($command->queryScalar() < 1) {
				echo $sprache_quelle->id .'<br/>';
				
				foreach($languages as $sprache) {
					$uebersetzungenZumAnlegen[] = array(
						'sprache' => $sprache,
						'quelle_id' => $sprache_quelle->id,
					);
				}
			}
		}
		if(isset($uebersetzungenZumAnlegen) && count($uebersetzungenZumAnlegen) > 0) {
			$this->erstelleUebersetzungenMultiple($uebersetzungenZumAnlegen);
			unset($uebersetzungenZumAnlegen);
		}
		echo 'DONE!!';
	}

	public function actionImportVonMessageFiles() {
		// Zeitlimit loeschen;
		set_time_limit(0);
		$dbDateTime = date('Y-m-d H:i:s', time());
		$languages = array();

		if(!is_file($this->messagePath.'config.php')) {
			throw new CHttpException('Konfigurationsdatei fehlt!');
		}
		$config = require_once($this->messagePath.'config.php');
		$translator='Yii::t';
		extract($config);

		foreach($languages as $sprache) {
			$messagesOrdner = $this->messagePath.$sprache.DIRECTORY_SEPARATOR;
			if(!is_dir($messagesOrdner)) continue;

			// Wir gehen davon aus, dass wir alle Translation-Dateien in einer ebene haben.
			// Sonst muss ein RecursiveDirectoryIterator benutzt werden.
			// Und es muss abgespeichert werden wo diese Dateien überall liegen
			foreach(new DirectoryIterator($messagesOrdner) as $fileInfo) {
				if($fileInfo->isDot()) continue;
				if($fileInfo->isDir()) continue;

				$messages = include($fileInfo->getPathname());
				$kategorie = substr($fileInfo->getFilename(), 0, -4);

				if($kategorie == 'oldLang') continue;

				foreach($messages as $schluessel => $value) {
					$schluesselHash = md5($schluessel);

					$quellen_ids[$schluesselHash] = $this->holeSpracheQuelle($schluessel, $kategorie);
					if(!isset($quellen_ids[$schluesselHash]) || $quellen_ids[$schluesselHash] == null) {
						$quellen_ids[$schluesselHash] = $this->erstelleSpracheQuelle($schluessel, $kategorie);
					}

					$uebersetztAttributes['aktiv'] = '1';
					$uebersetztAttributes['sprache'] = $sprache;
					$uebersetztAttributes['sprache_quelle_id'] = $quellen_ids[$schluesselHash];

					
					
					$sprache_uebersetzt = $this->existiertUebersetzung($uebersetztAttributes);
					
					
					
					if($sprache_uebersetzt==null) {
						$this->erstelleUebersetzung($quellen_ids[$schluesselHash], $sprache, $value);
					} else {

						if(strcmp($sprache_uebersetzt->value, $value) != 0) {
							if(strlen($value) < 1 && strlen($sprache_uebersetzt->value) > 1) {
								continue;
							}
							if(empty($value)) continue;
							// Es gibt einen aktiven Datensatz, die übersetzungen unterscheiden sich aber

							// Aktuellen Eintrag deaktivieren und speichern
							$sprache_uebersetzt->aktiv = 0;
							$sprache_uebersetzt->last_modified_time = $dbDateTime;
							$sprache_uebersetzt->save();

							// aktuelle Version der Übersetzung speichern
							$aktuelleVersion = $sprache_uebersetzt->version;

							// Prüfen ob es bereits einen Eintrag mit der neuen übersetzung gibt, dann einfach wieder aktivieren.
							$uebersetztAttributes['aktiv'] = 0;
							$uebersetztAttributes['value'] = $value;

							$sprache_uebersetzt = null;
							$sprache_uebersetzt = SystemSpracheUebersetzt::model()->findByAttributes($uebersetztAttributes);
							if(!is_object($sprache_uebersetzt)) {
								// Gibt keinen solchen Datensatz
								// Neue Version wird gespeichert
								$sprache_uebersetzt = new SystemSpracheUebersetzt;
								$sprache_uebersetzt->attributes = $uebersetztAttributes;
								$sprache_uebersetzt->version = $aktuelleVersion + 1;
								$sprache_uebersetzt->aktiv = 1;
								$sprache_uebersetzt->create_time = $dbDateTime;
								$sprache_uebersetzt->last_modified_time = $dbDateTime;
								$sprache_uebersetzt->last_modified_user_id = 0;
								$sprache_uebersetzt->save();
							} else {
								// Alte Version wird wiederhergestellt
								$sprache_uebersetzt->aktiv = 1;
								$sprache_uebersetzt->last_modified_time = $dbDateTime;
								$sprache_uebersetzt->save();
							}
						}
					}
					unset($sprache_uebersetzt, $uebersetztAttributes);
				}
			}
		}
	}

	public function actionIntegritaet() {
		$languages = array('de', 'en', 'it', 'fr', 'pt', 'tr', 'pl', 'es', 'nl');
		$anzahlSprachen = count($languages);

		$sql = "SELECT count(id) as anzahl, sprache_quelle_id FROM system_sprache_uebersetzt where aktiv = 1 group by sprache_quelle_id having anzahl < {$anzahlSprachen}";
		$command = Yii::app()->db->createCommand($sql);
		$fehlendeSprachen = $command->queryAll();

		if(!empty($fehlendeSprachen)) {
			set_time_limit(0);
			foreach($fehlendeSprachen as $ergebnis) {
				foreach($languages as $sprache) {
					$sql = "SELECT sprache FROM system_sprache_uebersetzt WHERE aktiv = 1 AND sprache = '{$sprache}' AND sprache_quelle_id = {$ergebnis['sprache_quelle_id']}";
					$command = Yii::app()->db->createCommand($sql);
					if(!$command->queryScalar())
						$this->erstelleUebersetzung($ergebnis['sprache_quelle_id'], $sprache);
				}
			}
		}
	}
	
	public function actionDubletten() {
		$sql = 'SELECT sprache_quelle_id, sprache, COUNT( * ) AS anzahl FROM  `system_sprache_uebersetzt` GROUP BY  `sprache` ,  `sprache_quelle_id` ,  `value` ,  `version` ,  `aktiv` HAVING anzahl >1';
		$command = Yii::app()->db->createCommand($sql);
		$dubletten = $command->queryAll();
		foreach($dubletten as $row) {
			$sql = 'SELECT id FROM system_sprache_uebersetzt WHERE sprache_quelle_id = :ssq_id AND sprache = :sprache';
			$command = Yii::app()->db->createCommand($sql);
			$command->bindParam(':ssq_id', $row['sprache_quelle_id'], PDO::PARAM_INT);
			$command->bindParam(':sprache', $row['sprache'], PDO::PARAM_STR);
			$reader = $command->query();
			for($i = 1; $i < count($reader); $i++) {
				$row = $reader->read();
				$sql = 'DELETE FROM system_sprache_uebersetzt WHERE id = :id';
				$command = Yii::app()->db->createCommand($sql);
				$command->bindParam(':id', $row['id'], PDO::PARAM_INT);
				$command->execute();
			}
		}
	}

	public function actionImportFromXML() {
		$model = new UploadForm;

		if(isset($_POST['UploadForm']) && !empty($_POST['sprache'])) {
			set_time_limit(0);
			$uploadDir = dirname(Yii::app()->request->scriptFile).DIRECTORY_SEPARATOR.'uploads';
			$model->uploaded_file = CUploadedFile::getInstance($model,'uploaded_file');
			$pathToFile = $uploadDir.DIRECTORY_SEPARATOR.$model->uploaded_file->getName();
			if($model->uploaded_file->saveAs($pathToFile)) {
				$document = $this->loadXML($pathToFile);
				$rows = $document->getElementsByTagName('row');
				foreach($rows as $index => $row) {
					$kategorie = $row->getElementsByTagName('kategorie')->item(0)->nodeValue;
					$schluessel = $row->getElementsByTagName('schluessel')->item(0)->nodeValue;
					$element = $row->getElementsByTagName($_POST['sprache']);
					if(is_object($element)) {
						$item = $element->item(0);
						if(is_object($item)) {
							$importZeile = $item->nodeValue;
						} else {
							continue;
						}
					} else {
						continue;
					}
					// $importZeile = $row->getElementsByTagName($_POST['sprache'])->item(0)->nodeValue;

					$this->schreibeNeueUebersetzung($_POST['sprache'], $schluessel, $kategorie, $importZeile);
				}
			}
		}

		$this->render('importFromXml', compact('model'));
	}

	/**
	 * Methode erstellt ein DOMDocument Objekt und liesst die angegebene Datei ein
	 *
	 * @param string $file Pfad zur XML-Datei
	 * @return object DOMDocument-Objekt
	 */
	private function loadXML($file) {
		$document = new DOMDocument();
		$document->load($file);
		return $document;
	}

	private function schreibeNeueUebersetzung($sprache, $schluessel, $kategorie, $value) {
		$schluesselHash = md5($schluessel);

		$quellen_ids[$schluesselHash] = $this->holeSpracheQuelle($schluessel, $kategorie);
		if(!isset($quellen_ids[$schluesselHash]) || $quellen_ids[$schluesselHash] == null) {
			$quellen_ids[$schluesselHash] = $this->erstelleSpracheQuelle($schluessel, $kategorie);
		}

		$uebersetztAttributes['aktiv'] = '1';
		$uebersetztAttributes['sprache'] = $sprache;
		$uebersetztAttributes['sprache_quelle_id'] = $quellen_ids[$schluesselHash];

		$sprache_uebersetzt = $this->existiertUebersetzung($uebersetztAttributes);
		if(!is_object($sprache_uebersetzt)) {
			$this->erstelleUebersetzung($quellen_ids[$schluesselHash], $sprache, $value);
		} else {
			if(strcmp($sprache_uebersetzt->value, $value) != 0) {
				// Es gibt einen aktiven Datensatz, die übersetzungen unterscheiden sich aber

				// Aktuellen Eintrag deaktivieren und speichern
				$sprache_uebersetzt->aktiv = 0;
				$sprache_uebersetzt->last_modified_time = date('Y-m-d H:i:s', time());;
				$sprache_uebersetzt->save();

				// aktuelle Version der Übersetzung speichern
				$aktuelleVersion = $sprache_uebersetzt->version;

				// Prüfen ob es bereits einen Eintrag mit der neuen übersetzung gibt, dann einfach wieder aktivieren.
				$uebersetztAttributes['aktiv'] = 0;
				$uebersetztAttributes['value'] = $value;

				$sprache_uebersetzt = null;
				$sprache_uebersetzt = SystemSpracheUebersetzt::model()->findByAttributes($uebersetztAttributes);
				if(!is_object($sprache_uebersetzt)) {
					// Gibt keinen solchen Datensatz
					// Neue Version wird gespeichert
					$sprache_uebersetzt = new SystemSpracheUebersetzt;
					$sprache_uebersetzt->attributes = $uebersetztAttributes;
					$sprache_uebersetzt->version = $aktuelleVersion + 1;
					$sprache_uebersetzt->aktiv = 1;
					$sprache_uebersetzt->create_time = date('Y-m-d H:i:s', time());;
					$sprache_uebersetzt->last_modified_time = date('Y-m-d H:i:s', time());;
					$sprache_uebersetzt->last_modified_user_id = 0;
					$sprache_uebersetzt->save();
				} else {
					// Alte Version wird wiederhergestellt
					$sprache_uebersetzt->aktiv = 1;
					$sprache_uebersetzt->last_modified_time = date('Y-m-d H:i:s', time());;
					$sprache_uebersetzt->save();
				}
			}
		}
		unset($sprache_uebersetzt, $uebersetztAttributes);
	}

	public function actionExportToXML($sprache = null) {
		set_time_limit(0);
		if($sprache) {
			$sprachenModel = SystemSpracheUebersetzt::getSprachen('de');
			$deutsch = $sprachenModel[0];
		}

		$sprachenModel = SystemSpracheUebersetzt::getSprachen($sprache);
		if($sprache) {
			$sprachen = $sprachenModel[0];
			$sprachenModel = array();
			$sprachenModel[0] = $deutsch;
			$sprachenModel[1] = $sprachen;
		}

		$kategorienModel = SystemSpracheQuelle::getKategorien();
		foreach($sprachenModel as $spracheModel) {
			foreach($kategorienModel as $kategorieModel) {
				$uebersetzungen = SystemSpracheQuelle::getAktiveUebersetzungen($kategorieModel->kategorie, $spracheModel->sprache);
				foreach($uebersetzungen as $uebersetzung) {
					$outputArray[$uebersetzung->id]['kategorie'] = $kategorieModel->kategorie;
					$outputArray[$uebersetzung->id]['schluessel'] = $uebersetzung->key;
					$outputArray[$uebersetzung->id][$spracheModel->sprache] = (!empty($uebersetzung->aktiveUebersetzung->value) ? $uebersetzung->aktiveUebersetzung->value : '');
				}
			}
		}

		if($sprache) {
			header("Content-Disposition: attachment; filename=translation_{$sprache}.xml");
		} else {
			header("Content-Disposition: attachment; filename=translation.xml");
		}
		//header('Content-Type: text/xml');
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");
		//header("Content-Length: " . filesize($file));

		echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>".PHP_EOL;
		echo "<xml xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">".PHP_EOL;
		foreach($outputArray as $id => $werte) {
			echo "<row>".PHP_EOL;
			echo "<tabelle>languageConfig</tabelle>".PHP_EOL;
			echo "<id>{$id}</id>".PHP_EOL;
			foreach($werte as $spalte => $wert) {
				echo "<{$spalte}><![CDATA[{$wert}]]></{$spalte}>".PHP_EOL;
				//echo "<german1><![CDATA[".html_entity_decode($werte['de'])."]]></german1>".PHP_EOL;
				//echo "<english1><![CDATA[".(isset($werte['en']) ? $werte['en'] : '')."]]></english1>".PHP_EOL;
			}
			echo "</row>".PHP_EOL;
		}
		echo "</xml>".PHP_EOL;
		Yii::app()->end();
	}

	public function actionExport($sprache = NULL) {
		set_time_limit(0);
		$sprachenModel = SystemSpracheUebersetzt::getSprachen($sprache);
		$kategorienModel = SystemSpracheQuelle::getKategorien();

		// Wenn mit unterordnern gearbeitet wird, muss hier noch einiges an Logik rein!
		foreach($sprachenModel as $spracheModel) {
			foreach($kategorienModel as $kategorieModel) {
				if($kategorieModel->kategorie == 'oldLang')
					continue;

				$files[$kategorieModel->kategorie] = $this->messagePath.$spracheModel->sprache.DIRECTORY_SEPARATOR.$kategorieModel->kategorie.'.php';
				$uebersetzungen = SystemSpracheQuelle::getAktiveUebersetzungen($kategorieModel->kategorie, $spracheModel->sprache);
				foreach($uebersetzungen as $uebersetzung) {
					$outputArray[$kategorieModel->kategorie][$uebersetzung->key] =$uebersetzung->aktiveUebersetzung->value;
				}
			}

			foreach($files as $eigenekategorie => $datei) {
				$array = array();
				foreach($outputArray as $kategorie => $uebersetzungen) {
					if($eigenekategorie == $kategorie) {
						$eigeneUebersetzungen = $uebersetzungen;
						continue;
					}
					$array = array_merge($array, $uebersetzungen);
				}
				$array = array_merge($array, $eigeneUebersetzungen);

				$exportarray=str_replace("\r",'',var_export($array, true));
				$content=<<<EOD
<?php
/**
 * Message translations.
 *
 * This file is automatically generated by 'yiic message' command.
 * It contains the localizable messages extracted from source code.
 * You may modify this file by translating the extracted messages.
 *
 * Each array element represents the translation (value) of a message (key).
 * If the value is empty, the message is considered as not translated.
 * Messages that no longer need translation will have their translations
 * enclosed between a pair of '@@' marks.
 *
 * Message string can be used with plural forms format. Check i18n section
 * of the guide for details.
 *
 * NOTE, this file must be saved in UTF-8 encoding.
 */

return $exportarray;

EOD;
				file_put_contents($datei, $content);
			}
		}
		$this->redirect('index');
/*
 			foreach($kategorienModel as $kategorieModel) {
				$messageFile = $this->messagePath.$spracheModel->sprache.DIRECTORY_SEPARATOR.$kategorieModel->kategorie.'.php';
				if(!is_dir(dirname($messageFile))) {
					mkdir(dirname($messageFile), 0755, true);
				}



				// Kopiert aus Yii-Source/cli/commands/MessageCommand.php
				$array=str_replace("\r",'',var_export($outputArray, true));
				$content=<<<EOD
<?php
/**
 * Message translations.
 *
 * This file is automatically generated by 'yiic message' command.
 * It contains the localizable messages extracted from source code.
 * You may modify this file by translating the extracted messages.
 *
 * Each array element represents the translation (value) of a message (key).
 * If the value is empty, the message is considered as not translated.
 * Messages that no longer need translation will have their translations
 * enclosed between a pair of '@@' marks.
 *
 * Message string can be used with plural forms format. Check i18n section
 * of the guide for details.
 *
 * NOTE, this file must be saved in UTF-8 encoding.
 */
		/*
return $array;

EOD;
				file_put_contents($messageFile, $content);

				$outputArray = array();
			}
		}
		$this->redirect('index');
		 */
	}

	/**
	 * Folgende Funktion wird im (Grid)View verwendet
	 */
	protected function zeigeButton($data, $row) {
		$value = '"'.$data['key'].'"';
		
		return "<input type='button' name='click' value='x' onClick=setInputValue(".$data['id'].",".$value.");>";
	}

	/**
	 * Methode kommt aus Yii-Source/cli/commands/MessagesCommand.php
	 * @var fileName string vollständiger Pfad zu einer Datei die zu übersetzende Strings enthält
	 * @var translator mixed nach zu suchendem Funktionsaufruf
	 * @return array Key-Value-Paare
	 */
	private function extractMessages($fileName,$translator) {
		//echo "Extracting messages from $fileName...\n";
		$subject=file_get_contents($fileName);
		$messages=array();
		if(!is_array($translator))
			$translator=array($translator);

		foreach ($translator as $currentTranslator)
		{
			$n=preg_match_all('/\b'.$currentTranslator.'\s*\(\s*(\'[\w.]*?(?<!\.)\'|"[\w.]*?(?<!\.)")\s*,\s*(\'.*?(?<!\\\\)\'|".*?(?<!\\\\)")\s*[,\)]/s',$subject,$matches,PREG_SET_ORDER);

			for($i=0;$i<$n;++$i)
			{
				if(($pos=strpos($matches[$i][1],'.'))!==false)
					$category=substr($matches[$i][1],$pos+1,-1);
				else
					$category=substr($matches[$i][1],1,-1);
				$message=$matches[$i][2];
//				$messages[$category][]=array('str' => eval("return $message;"), 'quelldatei' => preg_replace('/^.+[\\\\\\/]/', '', $fileName));  // use eval to eliminate quote escape
				$messages[$category][]=array('str' => eval("return $message;"), 'quelldatei' => str_replace('/usr/home/www/tmv4', '', $fileName));  // use eval to eliminate quote escape
			}
		}
		return $messages;
	}

	/**
	 * Gibt ein passendes Objekt anhand eines Schluessels und der Kategorie zurück
	 *
	 * @param string $schluessel Der zu suchende Schluessel
	 * @param string $kategorie Die Kategorie zu der der Schluessel gehört
	 * @return mixed SystemSpracheQuelle-Objekt oder null
	 */
	private function holeSpracheQuelle($schluessel, $kategorie) {
		$quelle = SystemSpracheQuelle::model()->findByAttributes(array('kategorie' => $kategorie, 'key' => $schluessel));
		return (is_object($quelle) ? $quelle->id : null);
	}

	private function erstelleSpracheQuelle($schluessel, $kategorie) {
		$quelle = new SystemSpracheQuelle;
		$quelle->vorkommen = 1;
		$quelle->kategorie = $kategorie;
		$quelle->key = $schluessel;
		if ($quelle->save()) {
			$quelle_id = $quelle->id;
		}
		return $quelle;
//		return (isset($quelle_id) ? $quelle_id : false);
	}

	private function erstelleSpracheQuelleNeu($schluessel, $kategorie) {
		$quelle = new SystemSpracheQuelleNeu;
		$quelle->vorkommen = 1;
		$quelle->kategorie = $kategorie;
		$quelle->key = $schluessel;
		if($quelle->save()) {
			$quelle_id = $quelle->id;
		}

		return (isset($quelle_id) ? $quelle_id : false);
	}

	private function existiertUebersetzung($parameters) {
		$uebersetzung = SystemSpracheUebersetzt::model()->findByAttributes($parameters);

		if($uebersetzung != null) {
			return $uebersetzung;
		} else {
			return null;
		}
	}

	private function erstelleUebersetzung($quelle_id, $sprache, $wert = null, $version = null) {
		$uebersetzung = new SystemSpracheUebersetzt;
		$uebersetzung->sprache = $sprache;
		$uebersetzung->sprache_quelle_id = $quelle_id;
		if(!empty($wert)) {
			$uebersetzung->value = $wert;
		}
		$uebersetzung->version = ($version == null ? 1 : $version);
		$uebersetzung->aktiv = 1;
		$uebersetzung->create_time = date('Y-m-d H:i:s', time());
		$uebersetzung->last_modified_time = date('Y-m-d H:i:s', time());
		$uebersetzung->last_modified_user_id = 0;
		if($uebersetzung->save()) {
			return true;
		}
		return false;
	}

	/**
	 * @param $array array Multidimensionales Array. enthält die zu erstellenden Übersetzungen. Folgende Felder müssen vorhanden sein
	 * 1. Ebene: Key -> zeile, Value -> Array
	 * 2. Ebene:
	 * sprache: string Das Sprachkuerzel (z.B. de)
	 * quelle_id: integer Die ID des Schluessels
	 * wert: mixed Der Übersetzte String oder NULL
	 * version: mixed Die aktuelle Version oder NULL (wird dann Version 1)
	 */
	private function erstelleUebersetzungenMultiple(&$array) {
		$db = Yii::app()->db;
		$aktuelleZeit = date('Y-m-d H:i:s', time());
		foreach($array as $idx => $unvollstaendigeUebersetzung) {
			$array[$idx]['sprache'] = $db->quoteValue($array[$idx]['sprache']);
			if(!isset($array[$idx]['wert'])) {
				$array[$idx]['value'] = new CDbExpression('NULL');
			} else {
				$array[$idx]['value'] = $db->quoteValue($array[$idx]['wert']);
			}
			if(!isset($array[$idx]['version'])) {
				$array[$idx]['version'] = 1;
			}
			$array[$idx]['aktiv'] = 1;
			$array[$idx]['create_time'] = $aktuelleZeit;
			$array[$idx]['last_modified_time'] = $aktuelleZeit;
			$array[$idx]['last_modified_user_id'] = 0;
		}
		foreach($array as $unvollstaendigeUebersetzung) {
			$inputArray[] = "({$unvollstaendigeUebersetzung['sprache']}, {$unvollstaendigeUebersetzung['quelle_id']}, {$unvollstaendigeUebersetzung['value']}, {$unvollstaendigeUebersetzung['version']}, {$unvollstaendigeUebersetzung['aktiv']}, '{$unvollstaendigeUebersetzung['create_time']}', '{$unvollstaendigeUebersetzung['last_modified_time']}', {$unvollstaendigeUebersetzung['last_modified_user_id']})";
		}

		$db->createCommand('INSERT INTO system_sprache_uebersetzt (sprache, sprache_quelle_id, value, version, aktiv, create_time, last_modified_time, last_modified_user_id)  VALUES '.implode(', ', $inputArray))->query();
	}

	private function deaktiviereUebersetzung($uebersetzung_id) {
		$uebersetzung = SystemSpracheUebersetzt::model()->findByPK($uebersetzung_id);
		$uebersetzung->aktiv = 0;
		$uebersetzung->last_modified_time = date('Y-m-d H:i:s');
		if($uebersetzung->save()) {
			return $uebersetzung->version;
		}
		return false;
	}

	private function writeDatabaseRecords($messages, $language, $category) {
		$neueEintraege = 0;

		SystemSpracheQuelle::model()->updateAll(array('vorkommen' => '0'), 'kategorie = :kategorie', array('kategorie' => $category));
		foreach($messages as $message) {
			$quelle = SystemSpracheQuelle::model()->findByAttributes(array('key' => $message, 'kategorie' => $category));
			if(is_object($quelle)) {
				$quelle->vorkommen++;
				$quelle->save();
			} else {
				$quelle = new SystemSpracheQuelle;
				$quelle->vorkommen = 1;
				$quelle->kategorie = $category;
				$quelle->key = $message;
				$quelle->save();

				$quelle_id = $quelle->id;

				$uebersetzung = new SystemSpracheUebersetzt;
				$uebersetzung->sprache = $language;
				$uebersetzung->sprache_quelle_id = $quelle_id;
				$uebersetzung->version = 1;
				$uebersetzung->aktiv = 1;
				$uebersetzung->create_time = date('Y-m-d H:i:s', time());
				$uebersetzung->last_modified_time = date('Y-m-d H:i:s', time());
				$uebersetzung->last_modified_user_id = 0;
				$uebersetzung->save();

				$neueEintraege++;
			}
		}

		return $neueEintraege;
	}
}
