<?php

/**
 * This is the model class for table "useravatare".
 *
 * The followings are the available columns in table 'useravatare':
 * @property string $avatar_id
 * @property string $user_id
 * @property string $bildname
 * @property integer $aktiv
 */
class Useravatare extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Useravatare the static model class
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
		return 'useravatare';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, bildname, aktiv', 'required'),
			array('aktiv', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>10),
			array('bildname', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('avatar_id, user_id, bildname, aktiv', 'safe', 'on'=>'search'),
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
			'avatar_id' => 'Avatar',
			'user_id' => 'User',
			'bildname' => 'Bildname',
			'aktiv' => 'Aktiv',
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

		$criteria->compare('avatar_id',$this->avatar_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('bildname',$this->bildname,true);
		$criteria->compare('aktiv',$this->aktiv);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}