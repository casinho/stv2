<?php

/**
 * This is the model class for table "artikel".
 *
 * The followings are the available columns in table 'artikel':
 * @property integer $id
 * @property string $titel
 * @property string $text
 * @property string $name
 * @property string $email
 * @property string $date
 * @property integer $poster_id
 * @property string $links_01
 * @property string $link_text_01
 * @property string $links_02
 * @property string $link_text_02
 * @property string $kategorie_id
 */
class Artikel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Artikel the static model class
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
		return 'artikel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('text', 'required'),
			array('poster_id', 'numerical', 'integerOnly'=>true),
			array('titel', 'length', 'max'=>150),
			array('name, email, date', 'length', 'max'=>100),
			array('links_01, link_text_01, links_02, link_text_02', 'length', 'max'=>255),
			array('kategorie_id', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, titel, text, name, email, date, poster_id, links_01, link_text_01, links_02, link_text_02, kategorie_id', 'safe', 'on'=>'search'),
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
			'titel' => 'Titel',
			'text' => 'Text',
			'name' => 'Name',
			'email' => 'Email',
			'date' => 'Date',
			'poster_id' => 'Poster',
			'links_01' => 'Links 01',
			'link_text_01' => 'Link Text 01',
			'links_02' => 'Links 02',
			'link_text_02' => 'Link Text 02',
			'kategorie_id' => 'Kategorie',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('titel',$this->titel,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('poster_id',$this->poster_id);
		$criteria->compare('links_01',$this->links_01,true);
		$criteria->compare('link_text_01',$this->link_text_01,true);
		$criteria->compare('links_02',$this->links_02,true);
		$criteria->compare('link_text_02',$this->link_text_02,true);
		$criteria->compare('kategorie_id',$this->kategorie_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}