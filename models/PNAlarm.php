<?php

/**
 * This is the model class for table "pn_alarm".
 *
 * The followings are the available columns in table 'pn_alarm':
 * @property string $alarm_id
 * @property integer $user_id
 * @property string $alarm_tld
 * @property string $meldung
 * @property datetime $alarm_datum
 */
class PNAlarm extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PNAlarm the static model class
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
		return 'pn_alarm';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('alarm_tld, user_id, meldung, alarm_datum', 'required'),
			array('alarm_tld', 'length', 'max'=>5),
			array('user_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('alarm_id, user_id, alarm_tld, meldung, alarm_datum', 'safe', 'on'=>'search'),
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
			'alarm_id' => 'Alarm',
			'user_id' => 'User-Id',
			'alarm_tld' => 'Alarm Tld',
			'meldung' => 'Meldung',
			'alarm_datum' => 'Alarm Datum',
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

		$criteria->compare('alarm_id',$this->alarm_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('alarm_tld',$this->alarm_tld,true);
		$criteria->compare('meldung',$this->meldung,true);
		$criteria->compare('alarm_datum',$this->alarm_datum,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}