<?php

/**
 * This is the model class for table "person2album_vorschlag".
 *
 * The followings are the available columns in table 'person2album_vorschlag':
 * @property string $auto_id
 * @property string $ma_person_id
 * @property string $ma_album_id
 * @property integer $albumaufgabe_id
 * @property string $beschreibung
 */
class Person2AlbumVorschlag extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Person2AlbumVorschlag the static model class
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
		return 'person2album_vorschlag';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ma_person_id, ma_album_id', 'required'),
			array('albumaufgabe_id', 'numerical', 'integerOnly'=>true),
			array('ma_person_id, ma_album_id', 'length', 'max'=>10),
			array('beschreibung', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('auto_id, ma_person_id, ma_album_id, albumaufgabe_id, beschreibung', 'safe', 'on'=>'search'),
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
			'ma_person_id' => 'Ma Person',
			'ma_album_id' => 'Ma Album',
			'albumaufgabe_id' => 'Albumaufgabe',
			'beschreibung' => 'Beschreibung',
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
		$criteria->compare('ma_person_id',$this->ma_person_id,true);
		$criteria->compare('ma_album_id',$this->ma_album_id,true);
		$criteria->compare('albumaufgabe_id',$this->albumaufgabe_id);
		$criteria->compare('beschreibung',$this->beschreibung,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}