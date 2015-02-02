<?php

/**
 * This is the model class for table "v4_user2validierung".
 *
 * The followings are the available columns in table 'v4_user2validierung':
 * @property string $auto_id
 * @property string $user_id
 * @property string $user_mail
 * @property string $daten
 * @property string $datum_angefordert
 * @property integer $user_id_angefordert
 * @property string $user_ip_angefordert
 * @property string $validierungs_typ
 * @property string $validierungs_schluessel
 * @property string $validiert_datum
 * @property integer $validiert_flag 
 * @property string $datum_erinnert
 * @property integer $anzahl_erinnerungen
 */
class User2Validierung extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User2validierung the static model class
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
		return 'user2validierung';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, user_mail, datum_angefordert, user_id_angefordert, user_ip_angefordert, validierungs_typ, validierungs_schluessel', 'required'),
			array('user_id_angefordert, anzahl_erinnerungen', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>10),
			array('user_mail', 'length', 'max'=>100),
			array('daten', 'length', 'max'=>255),
			array('user_ip_angefordert', 'length', 'max'=>40),
			array('validierungs_typ, validierungs_schluessel', 'length', 'max'=>30),
			array('datum_erinnert', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('auto_id, user_id, user_mail, daten, datum_angefordert, user_id_angefordert, user_ip_angefordert, validierungs_typ, validierungs_schluessel, validiert_datum, validiert_flag, datum_erinnert, anzahl_erinnerungen', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'auto_id' => 'Auto',
			'user_id' => 'User',
			'user_mail' => 'User Mail',
			'daten' => 'Daten',
			'datum_angefordert' => 'Datum Angefordert',
			'user_id_angefordert' => 'User Id Angefordert',
			'user_ip_angefordert' => 'User Ip Angefordert',
			'validierungs_typ' => 'Validierungs Typ',
			'validierungs_schluessel' => 'Validierungs Schluessel',
			'validiert_datum' => 'Validierungsdatum',
			'validiert_flag' => 'ValidierungsFlag',				
			'datum_erinnert' => 'Datum Erinnert',
			'anzahl_erinnerungen' => 'Anzahl Erinnerungen',
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

		$criteria->compare('auto_id',$this->auto_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('user_mail',$this->user_mail,true);
		$criteria->compare('daten',$this->daten,true);
		$criteria->compare('datum_angefordert',$this->datum_angefordert,true);
		$criteria->compare('user_id_angefordert',$this->user_id_angefordert);
		$criteria->compare('user_ip_angefordert',$this->user_ip_angefordert,true);
		$criteria->compare('validierungs_typ',$this->validierungs_typ,true);
		$criteria->compare('validierungs_schluessel',$this->validierungs_schluessel,true);
		$criteria->compare('validiert_datum',$this->validiert_datum,true);
		$criteria->compare('validiert_flag',$this->validiert_flag,true);		
		$criteria->compare('datum_erinnert',$this->datum_erinnert,true);
		$criteria->compare('anzahl_erinnerungen',$this->anzahl_erinnerungen);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
?>