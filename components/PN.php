<?php
class PN {
	protected $_new = true;
	protected $nachricht_id = null;
	protected $pn_id = null;
	protected $pn_datum;
	protected $gelesen_datum;
	protected $update_datum;
	protected $update_user_id;
	
	protected $nachrichten_counter = 0;

	// beim erstellen einer neuen Nachricht müssen folgende sechs Eigenschaften gesetzt werden: ->setAttributesForNewPn()
	protected $nachricht;
	protected $titel;
	protected $absender_id;
	protected $empfaenger_id = null;
	protected $empfaenger_ids = array();
	// --------------------------------------------
	protected $voraussetzungen_erfuellt = false; // um eine neue PN zu speichern
	protected $pn_voraussetzungen_erfuellt = false; // um eine Nachricht in die PNQueue einzutragen
	protected $mehrere_empfaenger_erlaubt = true; // wird z.B. bei Sanktionen auf false gesetzt!
	
	protected $weitergeleitet_flag = 0;
	protected $gelesen = 0;
	protected $alarm_id = null;
	protected $pn_alarm = null;

	protected $table;

	// array PNNachrichten
	protected $pnAntwortNachrichten = array();

	/**
	 * @param integer $user_id 
	 * @param string $status (neu|posteingang|archiv|admin) "admin" dient für die Ansicht der alarmierten PNs und ist nur für Admins gedacht.
	 * @param integer $pn_id
	 */
	public function __construct($user_id, $status = 'neu', $pn_id = null) {
		if ($status != 'neu' && !empty($pn_id)) {
			$this->pn_id = $pn_id;
			
			switch ($status) {
				case 'posteingang':
					$this->nachrichtAusPosteingangLaden($user_id);
					break;
					
				case 'archiv':
					$this->nachrichtAusArchivLaden($user_id);
					break;
					
				case 'alarm':
					$this->alarmierteNachrichtLaden();
					break;
			}
		}
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
	/**
	 * @param text $msg
	 * @param string $titel
	 * @param integer $absender_id
	 * @param mixed $empfaenger_ids
	 * @param bool $mehrere_empfaenger_erlaubt | default: true
	 */
	public function setAttributesForNewPn($msg, $titel, $absender_id, $empfaenger_ids, $mehrere_empfaenger_erlaubt = true) {
		$this->nachricht = $msg;
		$this->titel = $titel;
		$this->absender_id = $absender_id;
		$this->empfaenger_ids = self::empfaengerStringToArray($empfaenger_ids);
		$this->voraussetzungen_erfuellt = true;
		$this->mehrere_empfaenger_erlaubt = $mehrere_empfaenger_erlaubt;
		$this->checkEmpfaengerIds();
	}
	
	public function nachrichtInPnQueueSpeichern($msg, $titel, $absender_id, $empfaenger_gruppen) {
		$queue = new PNQueue;
		$queue->msg = $msg;
		$queue->titel = $titel;
		$queue->datum = date('Y-m-d H:i:s');
		$queue->empfaenger_rollen = serialize($empfaenger_gruppen);
		$queue->last_empfaenger_id = null;
		$queue->absender_id = $absender_id;
		$queue->user_id = Yii::app()->user->getId();
		$queue->save();
	}
	
	public function convertRollenToEmpfaengerIdArray(array $empfaenger_rollen) {
		$inClause = "'".implode("','",$empfaenger_rollen)."'";
		$sql = "SELECT DISTINCT userid FROM authassignment WHERE itemname IN(".$inClause.")";
		return Yii::app()->db->createCommand($sql)->queryColumn();
	}
	
	public static function empfaengerStringToArray($empfaenger_ids) {
		if (!is_array($empfaenger_ids)) {
			$empfaenger_ids = explode(',', $empfaenger_ids);
			$empfaenger_ids = array_map('intval', $empfaenger_ids);
		}
		return $empfaenger_ids;
	}
	
	private function checkEmpfaengerIds() {
		if(count($this->empfaenger_ids) > 1 && !$this->mehrere_empfaenger_erlaubt) {
			throw new CHttpException(404, 'Nur ein Empfänger erlaubt!');
		}
		$sql = "SELECT COUNT(user_id) 
				FROM user
				WHERE user_id = :user_id";
		foreach ($this->empfaenger_ids as $k => $v) {
			$command = Yii::app()->db->cache(CACHETIME_M)->createCommand($sql);
			$command->bindValue(":user_id", $v, PDO::PARAM_INT);
			$count = $command->queryScalar();
			if ($count < 1) {
				// Existiert der User nicht in der DB, wird der User aus der Empfänger-Liste entfernt.
				unset($this->empfaenger_ids[$k]);
			}
		}
		if (count($this->empfaenger_ids) < 1) {
			if (empty($this->empfaenger_id)) {
				throw new CHttpException(404, 'Empfänger fehlt!');
			} else {
				$this->empfaenger_ids[] = (int)$this->empfaenger_id;
			}
		}
	}

	public function save($noCopy = null) {
		$this->checkVoraussetzungen();
		$this->pn_datum = date('Y-m-d H:i:s');
		
		
		foreach ($this->empfaenger_ids as $empfaenger_id) {
			// nachricht speichern
			$pnNachricht = new PNNachricht();
			$pnNachricht->nachricht = $this->nachricht;
			$pnNachricht->pn_datum = $this->pn_datum;
			$pnNachricht->absender_id = $this->absender_id;
			$pnNachricht->save();
			
			// Nachricht für den Empfänger
			$pnEingang = new PNEingang();
			$pnEingang->nachricht_id = $pnNachricht->nachricht_id;
			$pnEingang->titel = $this->titel;
			$pnEingang->absender_id = $this->absender_id;
			$pnEingang->empfaenger_id = $empfaenger_id;
			$pnEingang->update_datum = $this->pn_datum;
			$pnEingang->pn_datum = $this->pn_datum;
			$pnEingang->save();

			/*
			 * - Absender 0(Systemnachrichten) werden nur einseitig gespeichert
			 * - Nachrichten bei denen $keinePosteingangsKopie gesetzt wurde, werden ebenfalls nicht gespeichert(kann im Formular angehakt werden)
			 */ 
			if ($this->absender_id > 0 && !is_null($noCopy)) {	
				unset($pnEingang);
				// Nachricht für für den Absender
				$pnEingang = new PNEingang();
				$pnEingang->nachricht_id = $pnNachricht->nachricht_id;
				$pnEingang->titel = $this->titel;
				$pnEingang->absender_id = $empfaenger_id;
				$pnEingang->empfaenger_id = $this->absender_id;
				$pnEingang->pn_datum = $this->pn_datum;
				$pnEingang->update_datum = $this->pn_datum;
				$pnEingang->gelesen_datum = $this->pn_datum;
				$pnEingang->gelesen = 1;
				$pnEingang->save();
			}
		}
	}
	
	/*
	public function antworten($ersteNachrichtId) {

	}
	 * 
	 */
	
	public function alsGelesenMarkieren() {
		if ($this->gelesen == 0 && $this->table == 'pn_eingang') {
			$this->gelesen = 1;
			$this->gelesen_datum = date('Y-m-d H:i:s');
			$this->saveGelesenStatus();
		}
	}
	
	public function nachrichtAnzeigen($msg = null, $parse_flag = 0) {
		$nachricht = new StringParser();
		if(isset($msg)) {
			$nachricht->string = $msg;
		} else {
			$nachricht->string = $this->nachricht;
		}
		$nachricht->parse_flag = $parse_flag;
		$nachricht->parseString();
		
		echo $nachricht->string;	
	}
	
	public function getAlarmMeldenUrl() {
		return Yii::app()->createUrl('pn/alarm', array('id' => $this->pn_id, 'seo' => GFunctions::normalisiereString('Private pn')));
	}

	public function getLoeschenUrl() {
		$action = ($this->table == 'pn_eingang') ? 'delete' : 'deleteArchiv'; 
		return Yii::app()->createUrl('pn/'.$action, array('id' => $this->pn_id, 'seo' => GFunctions::normalisiereString('pn')));
	}
	
	/**
	 * Gibt ein Array mit allen Rollen zurück, an die man eine Gruppen-PN verschicken darf/kann
	 */
	public static function getGueltigeRechteAlsGruppenempfaenger() {
		$gruppen = array('Superadmin' => Yii::t('global', 'Clanleader'),
						'SquadLeader' => Yii::t('global', 'SquadLeader'),
						'Clan-Member' => Yii::t('global', 'Clan-Member'),);
						
		if (Yii::app()->user->checkAccess('Superadmin')) {
			$gruppen['Freigeschaltet'] = Yii::t('pn', 'Freigeschaltet');
		}
		
		
		$squads = array();
		if (Yii::app()->user->checkAccess('Superadmin')) {
			$criteria = new CDbCriteria();
			$criteria->condition = 'st_flag = 1';
			$res = Squad::model()->findAll($criteria);
			foreach($res as $key => $v) {
				$squads[$v->squad_id] = $v->squad_tag;
			}
		} else {
			$criteria = new CDbCriteria();
			$criteria->condition = 'user_id = '.Yii::app()->user->getId().' AND (leader_flag = 1 OR orga_flag = 1)';
			if (Yii::app()->user->checkAccess('SquadLeader')) {
				$squadzuweisung = User2Squad::model()->findAll($criteria);
				
				foreach($squadzuweisung as $key => $v) {
					$squads[$v->squad->squad_id] = Yii::t('global','squad').': '.$v->squad->squad_tag;
				}
				
			}
		}

		foreach($squads as $k => $v) {
			$gruppen[$k] = $v;
		}
		
		$output = array();
		
		foreach($gruppen as $k => $v) {
			$output[] = array('id'=> $k, 'name'=>$v);
		}
		
		return $gruppen;
	}
	
	private function saveGelesenStatus() {
		//$command = Yii::app()->dbMaster->createCommand();
		$command = Yii::app()->db->createCommand();
		$command->update('pn_eingang', array(
		    'gelesen'=> $this->gelesen, 
		    'gelesen_datum'=> $this->gelesen_datum, 
		), 'pn_id=:id', array(':id'=>$this->pn_id));
	}
	
	private function nachrichtAusPosteingangLaden($user_id, $nachricht = null) {
		$sql = "SELECT pn_e.titel, pn_n.nachricht, pn_e.nachricht_id, pn_e.pn_datum, pn_e.absender_id, pn_e.empfaenger_id, pn_e.alarm_id, pn_e.weitergeleitet_flag, pn_e.gelesen 
			FROM pn_eingang AS pn_e 
			INNER JOIN pn_nachricht pn_n ON pn_n.nachricht_id = pn_e.nachricht_id 
			WHERE pn_e.pn_id = ".$this->pn_id." AND pn_e.empfaenger_id =".$user_id; 
    	$nachricht = Yii::app()->db->createCommand($sql)->queryRow();
    	if (!$nachricht) {
    		throw new CHttpException(404, 'Keine Berechtigung!');
    	}
		$this->setPnAttributsFromArray($nachricht);
		$this->table = 'pn_eingang';
		$this->_new = false;
		$this->pnAntwortNachrichten = $this->getAlleAntworten($nachricht['nachricht_id']);
		$this->nachrichten_counter = count($this->pnAntwortNachrichten); 
	}

	private function nachrichtAusArchivLaden($user_id, $nachricht = null) {
		$sql = "SELECT pn_a.titel, pn_n.nachricht, pn_a.nachricht_id, pn_a.pn_datum, pn_a.absender_id, pn_a.empfaenger_id, pn_a.weitergeleitet_flag 
			FROM pn_archiv AS pn_a 
			INNER JOIN pn_nachricht pn_n ON pn_n.nachricht_id = pn_a.nachricht_id 
			WHERE pn_a.pn_id = ".$this->pn_id." AND pn_a.empfaenger_id =".$user_id; 
    	$nachricht = Yii::app()->db->createCommand($sql)->queryRow();
    	if (!$nachricht) {
    		throw new CHttpException(404, 'Keine Berechtigung!!');
    	}
		$this->setPnAttributsFromArray($nachricht);
		$this->table = 'pn_archiv';
		$this->_new = false;
		$this->pnAntwortNachrichten = $this->getAlleAntworten($nachricht['nachricht_id']);
		$this->nachrichten_counter = count($this->pnAntwortNachrichten); 
	}
	
	private function alarmierteNachrichtLaden() {
		$sql = "SELECT pn_e.titel, pn_n.nachricht, pn_e.nachricht_id, pn_e.pn_datum, pn_e.absender_id, pn_e.empfaenger_id, pn_e.alarm_id, pn_e.weitergeleitet_flag, pn_e.gelesen 
			FROM pn_eingang AS pn_e 
			INNER JOIN pn_nachricht pn_n ON pn_n.nachricht_id = pn_e.nachricht_id 
			WHERE pn_e.pn_id = ".$this->pn_id; 
    	$nachricht = Yii::app()->db->createCommand($sql)->queryRow();
    	
   		$this->setPnAttributsFromArray($nachricht);
   		$this->pn_alarm = PNAlarm::model()->findByPk($nachricht['alarm_id']);
		$this->table = 'pn_eingang';
		$this->_new = false;
		$this->pnAntwortNachrichten = $this->getAlleAntworten($nachricht['nachricht_id']);
		$this->nachrichten_counter = count($this->pnAntwortNachrichten); 
	}
	
	private function getAlleAntworten($erste_nachricht_id) {
		$criteria = new CDbCriteria;
		$criteria->condition = 'nachricht_id = :nachrichtID OR erste_nachricht_id = :nachrichtID';
		$criteria->params = array(':nachrichtID' => $erste_nachricht_id);
		$criteria->order = 'pn_datum ASC';
		return PNNachricht::model()->findAll($criteria);
	}
	
	private function setPnAttributsFromArray(array $array) {
		foreach ($array as $key => $value) {
			$this->$key = $value;
		}
	}
	
	private function checkNew() {
		if (!$this->_new) {
			throw new CHttpException(404, 'Eine bereits existierende Nachricht kann nicht als "Neue Nachricht" gespeichert werden.');
		}
		return true;
	}

	private function checkVoraussetzungen() {
		if ($this->voraussetzungen_erfuellt && $this->checkNew()) {
			return true;
		} else {
			throw new CHttpException(404, 'Voraussetzungen für eine neue PN sind nicht erfüllt.');
		}
	}
}
