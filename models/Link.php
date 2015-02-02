<?php

/**
 * This is the model class for table "link".
 *
 * The followings are the available columns in table 'link':
 * @property integer $id
 * @property string $link
 * @property string $text
 * @property string $beschreibung
 * @property string $bild
 * @property string $typ
 * @property string $channel
 * @property string $tag
 * @property string $land_id
 * @property integer $hits
 * @property string $joker
 * @property string $poster_id
 * @property string $date
 */
class Link extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Link the static model class
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
		return 'link';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('text, beschreibung', 'required', 'on'=>'insert, update'),
			array('text, tag', 'required', 'on'=>'form'),				
			array('hits', 'numerical', 'integerOnly'=>true),
			array('link, bild', 'length', 'max'=>150),
			array('text', 'length', 'max'=>100),
			array('typ, channel, tag, date', 'length', 'max'=>20),
			array('land_id', 'length', 'max'=>10),
			array('joker', 'length', 'max'=>255),
			array('poster_id', 'length', 'max'=>4),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, link, text, beschreibung, bild, typ, channel, tag, land_id, hits, joker, poster_id, date', 'safe', 'on'=>'search'),
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
			'link' => 'Link',
			'text' => 'Text',
			'beschreibung' => 'Beschreibung',
			'bild' => 'Bild',
			'typ' => 'Typ',
			'channel' => 'Channel',
			'tag' => 'Tag',
			'land_id' => 'Land',
			'hits' => 'Hits',
			'joker' => 'Joker',
			'poster_id' => 'Poster',
			'date' => 'Date',
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
		$criteria->compare('link',$this->link,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('beschreibung',$this->beschreibung,true);
		$criteria->compare('bild',$this->bild,true);
		$criteria->compare('typ',$this->typ,true);
		$criteria->compare('channel',$this->channel,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('land_id',$this->land_id,true);
		$criteria->compare('hits',$this->hits);
		$criteria->compare('joker',$this->joker,true);
		$criteria->compare('poster_id',$this->poster_id,true);
		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getHeadline() {
		return CHtml::link($this->text,$this->link);
	}
	
	public function getLink() {
		return $this->link;
	}	
	
	public function getImage() {
		return false;
	}
	
	public function getName() {
		return $this->text;
	}	
}