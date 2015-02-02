<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class RegisterForm extends CFormModel
{
	public $user_nick;
    public $nachname;
	public $vorname;
	public $email;
	public $email2;
	public $passwort1;
	public $passwort2;
	public $verifyCode;
	public $datenschutzerklaerung_flag;
	public $nutzungsbedingungen_flag;
	
	public $systeminfo_flag;
	public $presseportal_flag;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	
	public function rules() {
		return array(
			//array('user_nick, passwort1, verifyCode, email, email2, datenschutzerklaerung_flag, nutzungsbedingungen_flag', 'required'),
			array('user_nick, passwort1, verifyCode, email, email2, nutzungsbedingungen_flag', 'required'),
			array('verifyCode', 'captcha', 'captchaAction'=>'user/captcha','skipOnError'=>true),
			//array('datenschutzerklaerung_flag, nutzungsbedingungen_flag', 'mustCheck'),
			array('email', 'email'),
			array('email', 'unique', 'className' => 'User', 'attributeName'=>'email', 'message' => Yii::t('user','email_bereits_verwendet')),
			//array('user_nick', 'userExists'),
			array('user_nick', 'unique', 'className' => 'User', 'attributeName'=>'user_nick', 'message' => Yii::t('user','nick_vergeben')),
			
			array('email2', 'compare', 'compareAttribute'=>'email', 'message' => Yii::t('user','emails_sind_ungleich')),
			array('passwort2', 'compare', 'compareAttribute'=>'passwort1', 'message' => Yii::t('user','passwoerter_sind_ungleich')),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'verifyCode' => Yii::t('user','verifizierungscode'),
		    'passwort1'	=> Yii::t('user','passwort'),
		    'passwort2'	=> Yii::t('user','passwort_wiederholen'),
		    'email'	=> Yii::t('user','email'),
		    'email2' => Yii::t('user','email_wiederholen'),
			'nutzungsbedingungen_flag'	=> Yii::t('user','stimme_anb_zu'),
			'datenschutzerklaerung_flag'=> Yii::t('user','stimme_ade_zu'),
			'systeminfo_flag' => Yii::t('user','info_vom_system'),
		);
	}
	
	public function mustCheck($attribute, $params) {
		if($this->anb_flag != 0) {
			return true;
		} else {
			$this->addError('anb_flag','Du musst die Allgemeinen Nutzungsbedingungen akzeptieren');
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
