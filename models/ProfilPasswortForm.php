<?php

class ProfilPasswortForm extends CFormModel {
	public $passwort1;
	public $passwort2;
	public $oldpasswort;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
		return array(
			array('oldpasswort, passwort1', 'required', 'on' => 'insert, update'),
			array('passwort1', 'required', 'on' => 'admin'),	
			array('passwort1', 'length', 'min'=> 5),
			array('oldpasswort', 'checkOldPasswort', 'on'=> 'insert,update'),
			array('passwort2', 'compare', 'compareAttribute'=>'passwort1', 'message' => 'Die Passwörter stimmen nicht überein'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'oldpasswort'	=> Yii::t('member','altes_passwort'),
		    'passwort1'		=> Yii::t('member','neues_passwort'),
		    'passwort2'		=> Yii::t('member','neues_passwort_wiederholen'),
		);
	}
	
	/**
	 * Passwort ändern Formular:
	 * check ob das aktuelle passwort richtig eingegeben wurde
	 */
	public function checkOldPasswort($attribute, $params) {
		
		$user_id = Yii::app()->user->getId();
		$user = User::model()->findByPk($user_id);
		
		if($user->validatePassword($this->oldpasswort)) {
			return true;
		} else {
			$this->addError('oldpasswort',Yii::t('member','fehler_altes_passwort'));
		}
	}

	public function userExists() {
	    $attributes['user_nick'] = $this->user_nick;
	    $user1 = User::model()->findByAttributes($attributes);
	    if(!empty($user1)) {
	       $this->addError('user_nick','Es existiert bereits ein Account mit dem Namen <b>'.$this->user_nick.'</b>.'); 
	    }
		/*
	    $attributes['user_mail'] = $this->email;
	    $user2 = User::model()->findByAttributes($attributes);
	    if(!empty($user2)) {
	       $this->addError('email','Es existiert bereits ein Account mit der Email-Adresse <b>'.$this->email.'</b>.'); 
	    }
	    */	    
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params) {
		
		if(!$this->hasErrors()) {
			$this->_identity=new UserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate()) {
				$this->addError('password','Passwort und Username stimmen nicht überein.');
			}
		}
	}



	
}
