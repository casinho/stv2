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
	public $passwort1;
	public $passwort2;
	public $str;
	public $plz;
	public $ort;
	public $verifyCode;
	public $verein;
	public $anb_flag;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
		return array(
			array('user_nick, nachname, passwort1, verifyCode, email, vorname, anb_flag', 'required'),
			array('anb_flag', 'mustCheck'),
			array('email', 'email'),
			array('user_nick', 'userExists'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
			array('passwort2', 'compare', 'compareAttribute'=>'passwort1', 'message' => 'Die Passwörter stimmen nicht überein'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'verifyCode'=>'Verifizierungscode',
		    'passwort1'=>'Passwort',
		    'passwort2'=>'Passwort wiederholen',
		    'plz'=>'Postleitzahl',
		    'str'=>'Straße',
			'anb_flag'=>'Ich akzeptiere die <a href="/service/nutzungsbedingungen" target="_blank">Allgemeinen Nutzungsbedingungen</a>',
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
	    $user = User::model()->findByAttributes($attributes);
	    if(!empty($user)) {
	       $this->addError('user_nick','Es existiert bereits ein Account mit dem Namen <b>'.$this->user_nick.'</b>.'); 
	    }
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
