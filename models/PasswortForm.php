<?php

/**
 * PasswortForm class.
 * PasswortForm zum Anfordern neuer Passwörter
 */
class PasswortForm extends CFormModel
{
	public $user_nick;
	public $verifyCode;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
		return array(
			array('user_nick, verifyCode', 'required'),
			array('user_nick', 'userExists'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'verifyCode'=>'Verifizierungscode',
		);
	}
	
	public function userExists() {
	    $attributes['user_nick'] = $this->user_nick;
	    $user = User::model()->findByAttributes($attributes);
	    if(empty($user)) {
	       $this->addError('user_nick','Der Nutzer <b>'.$this->user_nick.'</b> existiert nicht.'); 
	    }
	}	
}
