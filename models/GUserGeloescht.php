<?php

/**
 * This is the model class for table "tm_user_geloescht".
 *
 * The followings are the available columns in table 'tm_user_geloescht':
 * @property integer $user_id
 * @property string $user_nick
 * @property string $user_mail
 * @property string $datum
 * @property string $user_ip
 */
class GUserGeloescht extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TmUserGeloescht the static model class
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
		return 'tm_user_geloescht';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, user_nick, user_mail, user_ip', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('user_nick', 'length', 'max'=>100),
			array('user_mail', 'length', 'max'=>200),
			array('user_ip', 'length', 'max'=>50),
			array('datum','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'insert'),				
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, user_nick, user_mail, datum, user_ip', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'user_nick' => 'User Nick',
			'user_mail' => 'User Mail',
			'datum' => 'Datum',
			'user_ip' => 'User Ip',
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
		$criteria->compare('user_nick',$this->user_nick,true);
		$criteria->compare('user_mail',$this->user_mail,true);
		$criteria->compare('datum',$this->datum,true);
		$criteria->compare('user_ip',$this->user_ip,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}