<?php

/**
 * This is the model class for table "beitrag".
 *
 * The followings are the available columns in table 'beitrag':
 * @property string $beitrag_id
 * @property integer $beitrag_id_alt
 * @property string $user_id
 * @property string $thema_id
 * @property string $titel
 * @property string $nachricht
 * @property string $erstellzeit
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Thema $thema
 */
class Beitrag extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Beitrag the static model class
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
		return 'beitrag';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, thema_id', 'required'),
			array('beitrag_id_alt', 'numerical', 'integerOnly'=>true),
			array('user_id, thema_id', 'length', 'max'=>10),
			array('titel', 'length', 'max'=>255),
			array('nachricht, erstellzeit', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('beitrag_id, beitrag_id_alt, user_id, thema_id, titel, nachricht, erstellzeit', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'thema' => array(self::BELONGS_TO, 'Thema', 'thema_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'beitrag_id' => 'Beitrag',
			'beitrag_id_alt' => 'Beitrag Id Alt',
			'user_id' => 'User',
			'thema_id' => 'Thema',
			'titel' => 'Titel',
			'nachricht' => 'Nachricht',
			'erstellzeit' => 'Erstellzeit',
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

		$criteria->compare('beitrag_id',$this->beitrag_id,true);
		$criteria->compare('beitrag_id_alt',$this->beitrag_id_alt);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('thema_id',$this->thema_id,true);
		$criteria->compare('titel',$this->titel,true);
		$criteria->compare('nachricht',$this->nachricht,true);
		$criteria->compare('erstellzeit',$this->erstellzeit,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}