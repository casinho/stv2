<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class PNForm extends CFormModel
{
	public $empfaenger_id;
    public $absender_id;
	public $betreff;
	public $nachricht;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	
	public function rules() {
		return array(
			array('empfaenger_id,betreff,nachricht', 'required'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'empfaenger_id' 	=> Yii::t('pn','empfaenger'),
		    'betreff'		=> Yii::t('pn','betreff'),
		    'nachrichr'		=> Yii::t('pn','nachricht'),
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




	
}
