<?php

class ProfilDeleteForm extends CFormModel {
	
	public $delete;
	public $loeschgrund;
	public $grund_id;
	public $allowed;
	
	
	public function getLoeschgrunde() {
		
		//$output[1] = Yii::t('member','kann_nick_nicht_aendern');
		$output[1] = Yii::t('member','brauche_account_nicht_mehr');
		$output[2] = Yii::t('member','hatte_mehr_erhofft');
		$output[3] = Yii::t('member','werde_beleidigt');
		$output[4] = Yii::t('member','zeitverschwendung');
		$output[5] = Yii::t('member','sonstiges');
		
		return $output;
	}
	

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules() {
		return array(
			array('delete', 'required'),
			array('delete', 'pruefeUser'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'delete'	=> 'ja_account_loeschen',
		    'ja_account_loeschen' => Yii::t('member','ja_account_loeschen'),
		);
	}

	/*
	 * TODO: Bedingungen einbauen, ob der User überhaupt sein Profil löschen darf
	 */
	
	public function pruefeUser() {
		if(Yii::app()->user->checkAccess('admin')) {
			$this->addError('delete',Yii::t('member','account_loeschung_nicht_erlaubt'));
		} else {
			return true;
		}
		
	}	
	
	
	
	
}
