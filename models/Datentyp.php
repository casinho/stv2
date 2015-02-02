<?php

/**
 * This is the model class for table "v4_datentyp".
 *
 * The followings are the available columns in table 'v4_datentyp':
 * @property integer $datentyp_id
 * @property string $datentyp
 * @property string $type
 * @property integer $required_flag
 * @property string $anzeigebereich
 * @property integer $sortierung
 */
class Datentyp extends CActiveRecord
{
	
	public $daten_anzeige;
	public $daten_wert;
	public $daten;
	public $readonly_flag;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Datentyp the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'datentyp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('datentyp, type, required_flag, anzeigebereich, sortierung', 'required'),
			array('required_flag, sortierung, readonly_flag', 'numerical', 'integerOnly'=>true),
			array('datentyp', 'length', 'max'=>255),
			array('type', 'length', 'max'=>10),
			array('anzeigebereich', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('datentyp_id, datentyp, type, required_flag, anzeigebereich, sortierung', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user2daten'=>array(self::HAS_ONE, 'User2Daten', 'datentyp_id'),
			'datenwerte' => array(
					self::HAS_ONE,	'User2Daten', array('datentyp_id' => 'datentyp_id'), 'joinType' => 'LEFT JOIN'
			),				
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'datentyp_id' 	=> Yii::t('profil','datentyp_id'),
			'datentyp' 		=> Yii::t('profil','datentyp'),
			'type' 			=> Yii::t('profil','type'),
			'required_flag' => Yii::t('profil','required_flag'),
			'anzeigebereich'=> Yii::t('profil','anzeigebereich'),
			'sortierung' 	=> Yii::t('profil','sortierung'),
			'readonly_flag'	=> Yii::t('profil','readonly'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('datentyp_id',$this->datentyp_id);
		$criteria->compare('datentyp',$this->datentyp,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('required_flag',$this->required_flag);
		$criteria->compare('readonly_flag',$this->readonly_flag);
		$criteria->compare('anzeigebereich',$this->anzeigebereich,true);
		$criteria->compare('sortierung',$this->sortierung);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function holeUserFormDaten($user_id,$anzeigebereich = false) {
		if($anzeigebereich !== false) {
			$bedingung =  ' AND '.$anzeigebereich.' IN (d.anzeigebereich)';
		} else {
			$bedingung = '';
		}
		if(1==1) {
			$daten = Yii::app()->db->createCommand()
				->select('u2d.datenwert,d.datentyp,d.type,d.required_flag,d.datentyp_id,daten_anzeige')
				->from('v4_user2daten AS u2d')
				->leftJoin('v4_datentyp AS d', 'd.datentyp_id = u2d.datentyp_id')
				->where('u2d.user_id = '.$user_id.$bedingung.'')
				->order('d.sortierung DESC')
				->limit('5')
				->queryAll();
			
		} else {	
			
			$criteria = new CDbCriteria;
			$criteria->with 		= array('datenwerte');
			$criteria->select 		= array('datentyp_id', 'type', 'required_flag', 'datenwerte.*');
			if($anzeigebereich !== false) {
				$criteria->condition 	= 'datenwerte.user_id = :user_id AND :anzeigebereich IN (anzeigebereich)';
				$criteria->params 		= array(':user_id' => $user_id, ':anzeigebereich' => $anzeigebereich);
			} else {
				$criteria->condition 	= 'datenwerte.user_id = :user_id ';
				$criteria->params 		= array(':user_id' => $user_id);			
			}
			//$criteria->order 		= 'erstelltzeit DESC';
			
			$daten = $this->findAll($criteria);
		}
		
		$output = array();
		
		foreach ($daten as $k => $v) {
			$output[$v['datentyp']] = $v;//[$v['datentyp'].'_cb'] = $v['daten_anzeige'];
			$output[$v['datentyp']][$v['datentyp'].'_cb'] = $v['daten_anzeige'];
		}
		/*
		echo "<pre>";
		print_r($output);
		echo "<pre/>";
		die();*/
		return $output;
	}

	
	
}