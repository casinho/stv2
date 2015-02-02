<?php

/**
 * This is the model class for table "note".
 *
 * The followings are the available columns in table 'note':
 * @property string $note_id
 * @property string $note
 * @property string $spiel2spieler_spiel_id
 * @property string $spiel2spieler_spieler_id
 * @property string $user_id
 *
 * The followings are the available model relations:
 * @property Spiel2spieler $spiel2spielerSpiel
 * @property Spiel2spieler $spiel2spielerSpieler
 * @property User $user
 */
class Note extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Note the static model class
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
		return 'note';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('spiel2spieler_spiel_id, spiel2spieler_spieler_id, user_id', 'required'),
			array('note, spiel2spieler_spiel_id, spiel2spieler_spieler_id, user_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('note_id, note, spiel2spieler_spiel_id, spiel2spieler_spieler_id, user_id', 'safe', 'on'=>'search'),
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
			'spiel2spielerSpiel' => array(self::BELONGS_TO, 'Spiel2spieler', 'spiel2spieler_spiel_id'),
			'spiel2spielerSpieler' => array(self::BELONGS_TO, 'Spiel2spieler', 'spiel2spieler_spieler_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'note_id' => 'Note',
			'note' => 'Note',
			'spiel2spieler_spiel_id' => 'Spiel2spieler Spiel',
			'spiel2spieler_spieler_id' => 'Spiel2spieler Spieler',
			'user_id' => 'User',
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

		$criteria->compare('note_id',$this->note_id,true);
		$criteria->compare('note',$this->note,true);
		$criteria->compare('spiel2spieler_spiel_id',$this->spiel2spieler_spiel_id,true);
		$criteria->compare('spiel2spieler_spieler_id',$this->spiel2spieler_spieler_id,true);
		$criteria->compare('user_id',$this->user_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}