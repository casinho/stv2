<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ShoutForm extends CFormModel
{
	public $chat_id;
	public $post_identity;
	public $owner;
	public $created;
	public $text;
	public $data;
	public $verifyCode;
	
	public $maxChars = 144;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('chat_id, post_identity, owner, created, text, data', 'required'),
			array('owner', 'length', 'min' => 1, 'max' => 30),
			array('text', 'length', 'min' => 3, 'max' => 144),
			array('verifyCode', 'captcha',  'captchaAction'=>'yiiChat/captcha','skipOnError'=>true,  'on' => 'captchaRequired'),
			array('owner', 'unique', 'className' => 'User', 'attributeName'=>'user_nick', 'message' => Yii::t('global','nick_vergeben_registriere_dich'),  'on' => 'captchaRequired'),				
			// rememberMe needs to be a boolean
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()	{
		return array(
			'owner'			=>Yii::t('global','dein_name'),
			'user_nick'		=>Yii::t('global','user_nick'),
			'text'			=>Yii::t('global','dein_shout'),
			'verifyCode'	=>Yii::t('global','verifyCode'),
		);
	}


}
