<?php

/**
 * This is the model class for table "v4_user2info".
 *
 * The followings are the available columns in table 'v4_user2info':
 * @property integer $user_id
 * @property string $datum
 * @property integer $infotyp_id
 * @property integer $info_flag
 * @property integer $notification_flag
 */
class User2Info extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User2Info the static model class
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
		return 'user2info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, datum, infotyp_id, info_flag, notification_flag', 'required'),
			array('user_id, infotyp_id, info_flag, notification_flag', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, datum, infotyp_id, info_flag, notification_flag', 'safe', 'on'=>'search'),
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
			'user_id' 			=> 'User',
			'datum' 			=> 'Datum',
			'infotyp_id' 		=> 'Infotyp',
			'info_flag' 		=> 'Info Flag',
			'notification_flag' => 'Notification Flag',
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('datum',$this->datum,true);
		$criteria->compare('infotyp_id',$this->infotyp_id);
		$criteria->compare('info_flag',$this->info_flag);
		$criteria->compare('notification_flag',$this->notification_flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}