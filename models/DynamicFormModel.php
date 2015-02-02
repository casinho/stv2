<?php
/*
 * Ich habs mir mal wieder zu kompliziert ghemacht und brauch ein dynamisches FromModel (ct)
 */

class DynamicFormModel extends CFormModel {
	
	public $elements = array();
	public $models = array();
	public $arrData = array();
	
	public $rules;
	
	public $rulesArray;

	
	public function init() {
		$user_id 	= Yii::app()->user->getId();
	
		$this->models 	= $this->setModels();
		$this->elements = $this->setElements($user_id);
	
		$this->rules	= $this->setRules();
		
		//TMFunctions::pre($this->rules);
		
		$this->setUser2DatenObjecte($user_id);
	}

	public function setModels() {
		return Datentyp::model()->findAll(array('order'=>'sortierung', 'condition'=>'anzeigebereich=:ab', 'params'=>array(':ab'=>1)));
	}
	
	
	public function setElements($user_id) {
		return Datentyp::model()->holeUserFormDaten($user_id);
	}
	
	public function setUser2DatenObjecte($user_id) {
		$attr['user_id'] = $user_id;
		foreach($this->models as $k => $v) {
				
			$attr['datentyp_id'] = $v['datentyp_id'];
			$this->models[$k]['daten'] = User2Daten::model()->findByAttributes($attr);
			if($this->models[$k]['daten']==null) {
				$this->models[$k]['daten'] = new User2Daten;
				$this->{$v['datentyp']} = '';
			} else {
				$this->{$v['datentyp']} = $this->models[$k]['daten']['datenwert'];
			}
		}
		//TMFunctions::pre($this->models);
	}	
	
	// elemente definieren
	public function setRules() {
		$required 	= array();
		$intOnly 	= array();
		$eMail 		= array();
		$varChars	= array();
		
		$output 	= array();
		
		foreach($this->models as $k => $e) {
			if($e['required_flag']) {
				$required[] = $e['datentyp'];
			}
			if($e['type']=='integer') {
				$intOnly[] = $e['datentyp'];
			} 
			if($e['type']=='varchar') {
				$varChars[] = $e['datentyp'];
			}
			if($e['type']=='eMail') {
				$eMail[] = $e['datentyp'];
			}
			//print_r($e);
		}
		if(!empty($eMail)) {
			$output[] = array(implode(', ', $eMail), 'email', 'on' => implode(', ', $eMail), 'on' => implode(', ', $eMail));
		}
		if(!empty($varChars)) {
			$output[] = array(implode(', ', $varChars), 'length', 'max' => 200, 'on' => implode(', ', $varChars));
		}
		if(!empty($intOnly)) {
			$output[] = array(implode(', ', $intOnly), 'numerical', 'integerOnly'=>true, 'on' => implode(', ', $intOnly));
		}		
		
		if(!empty($required)) {	
			$output[] = array(implode(', ', $required), 'required', 'on' => implode(', ', $required));
		}
		
		$output[] = array('ort, plz', 'pruefeTeilnahme');
		
		return $output;
	}

	public function rules() {
		return array(
				array('str, plz', 'pruefeTeilnahme'),
		);
	}
	
	
	// elementnamen wiedergeben
	public function __get($name) {
		//if(isset(($name,)) {
			if(is_numeric($name)) {
				foreach($this->elements as $e) {
					if($e['datentyp'] == $name) {
						return $e['datenwert'].'_cb';
						//return $e['datentyp'];
					}
				} 
				return $this->$name;
			} else {
				foreach($this->models as $k => $v) {
					if($v->datentyp == $name) {
						return $v->daten->datenwert;
					} 
				}
				//return $this->$name;
			}
		//} 
	}

	public function setAttribute($name, $val) {
		$this->{$name} = $val;
	}	
	
	public function __set($name, $val) {
		$this->arrData = array($name => $val);
	}
	
	public function pruefeTeilnahme() {
	
		$attributes['daten_anzeige']= 1;
		$attributes['user_id'] 		= Yii::app()->user->getId();
		$attributes['datentyp_id'] 	= 24;
			
		$daten = User2Daten::model()->findByAttributes($attributes);
		
		if(!empty($daten)) {
			foreach($this->models as $k => $v) {
				
				//TMFunctions::pre($v->datentyp.' - '.$v->daten->datenwert.PHP_EOL);
				
				if($v->datentyp=='str'&& empty($v->daten->datenwert)) {
					$this->addError('plz',Yii::t('profil','tm_userdaten_fansuche_fehler_plz'));
				}
				if($v->datentyp=='ort' && empty($v->daten->datenwert)) {
					$this->addError('ort',Yii::t('profil','tm_userdaten_fansuche_fehler_ort'));
				}
			}						
		} else {
			return true;
		}
	}	

}
?>
