<?php

/**
 * This is the model class for table "track".
 *
 * The followings are the available columns in table 'track':
 * @property string $track_id
 * @property string $album_id
 * @property integer $disc
 * @property string $titel
 * @property string $lyrics
 * @property integer $reihenfolge
 * @property string $band_id
 * @property integer $intro_flag
 * @property integer $outro_flag
 * @property integer $live_flag
 * @property integer $instrumental_flag
 * @property string $laenge
 * @property integer $sekunden
 */
class Track extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Track the static model class
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
		return 'track';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('disc, laenge, sekunden', 'required'),
			array('disc, reihenfolge, intro_flag, outro_flag, live_flag, instrumental_flag, sekunden', 'numerical', 'integerOnly'=>true),
			array('album_id, band_id', 'length', 'max'=>10),
			array('titel', 'length', 'max'=>255),
			array('laenge', 'length', 'max'=>8),
			array('lyrics', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('track_id, album_id, disc, titel, lyrics, reihenfolge, band_id, intro_flag, outro_flag, live_flag, instrumental_flag, laenge, sekunden', 'safe', 'on'=>'search'),
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
			'track_id' => 'Track',
			'album_id' => 'Album',
			'disc' => 'Disc',
			'titel' => 'Titel',
			'lyrics' => 'Lyrics',
			'reihenfolge' => 'Reihenfolge',
			'band_id' => 'Band',
			'intro_flag' => 'Intro Flag',
			'outro_flag' => 'Outro Flag',
			'live_flag' => 'Live Flag',
			'instrumental_flag' => 'Instrumental Flag',
			'laenge' => 'Laenge',
			'sekunden' => 'Sekunden',
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

		$criteria->compare('track_id',$this->track_id,true);
		$criteria->compare('album_id',$this->album_id,true);
		$criteria->compare('disc',$this->disc);
		$criteria->compare('titel',$this->titel,true);
		$criteria->compare('lyrics',$this->lyrics,true);
		$criteria->compare('reihenfolge',$this->reihenfolge);
		$criteria->compare('band_id',$this->band_id,true);
		$criteria->compare('intro_flag',$this->intro_flag);
		$criteria->compare('outro_flag',$this->outro_flag);
		$criteria->compare('live_flag',$this->live_flag);
		$criteria->compare('instrumental_flag',$this->instrumental_flag);
		$criteria->compare('laenge',$this->laenge,true);
		$criteria->compare('sekunden',$this->sekunden);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}