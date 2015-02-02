<?php

/**
 * This is the model class for table "user2squad".
 *
 * The followings are the available columns in table 'user2squad':
 * @property string $auto_id
 * @property string $user_id
 * @property string $squad_id
 */
class User2Squad extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return User2Squad the static model class
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
		return 'user2squad';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, squad_id', 'required'),
			array('user_id, squad_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('auto_id, user_id, squad_id, leader_flag, orga_flag', 'safe', 'on'=>'search'),
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
				'squad' => array(
						self::HAS_ONE,
						'Squad',
						array('squad_id'=>'squad_id'),
						'condition' => 'st_flag = 1',
				),
				'user' => array(
						self::HAS_ONE,
						'User',
						array('user_id'=>'user_id'),
				),								
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
			'squad_id' => 'Squad',
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
		$criteria->compare('squad_id',$this->squad_id,true);
		$criteria->compare('leader_flag',$this->leader_flag,true);
		$criteria->compare('orga_flag',$this->orga_flag,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}