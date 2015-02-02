<?php

class PasswortVergessenForm extends CFormModel
{
	public $email;
	public $verifyCode;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('email, verifyCode', 'required'),
			array('verifyCode', 'captcha', 'captchaAction'=>'user/captcha','skipOnError'=>true, 'on' => 'captchaRequired'),				
			array('email', 'email'),
			array('email', 'exist', 'className' => 'User', 'attributeName'=>'email'),				
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'verifyCode'	=> Yii::t('user','pruefcode'),
			'email'			=> Yii::t('user','email'),
		);
	}

	public function checkEmailGueltigkeit($attribute, $params) {
		
		if(!empty($this->email)) {
			$user = User::model()->find('user_mail=:user_mail',array(':user_mail'=>$this->email));
			
			if(!($user instanceof User)) {
				$this->addError('email',Yii::t('profil','E-Mail unbekannt!'));
			} else {
				return true;
			}
		}
	}

}
