<?php

/**
 * This is the model class for table "system_sprache_log".
 *
 * The followings are the available columns in table 'system_sprache_log':
 * @property string $id
 * @property string $sprache_quelle_id
 * @property string $sprache_uebersetzt_id
 * @property integer $aktion
 * @property string $user_id
 * @property string $create_time
 */
class SystemSpracheLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SystemSpracheLog the static model class
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
		return 'system_sprache_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sprache_quelle_id, sprache_uebersetzt_id, aktion, user_id, create_time', 'required'),
			array('aktion', 'numerical', 'integerOnly'=>true),
			array('sprache_quelle_id, sprache_uebersetzt_id, user_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sprache_quelle_id, sprache_uebersetzt_id, aktion, user_id, create_time', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'sprache_quelle_id' => 'Sprache Quelle',
			'sprache_uebersetzt_id' => 'Sprache Uebersetzt',
			'aktion' => 'Aktion',
			'user_id' => 'User',
			'create_time' => 'Create Time',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('sprache_quelle_id',$this->sprache_quelle_id,true);
		$criteria->compare('sprache_uebersetzt_id',$this->sprache_uebersetzt_id,true);
		$criteria->compare('aktion',$this->aktion);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}