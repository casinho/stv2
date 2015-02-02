<?php

/**
 * This is the model class for table "person2band".
 *
 * The followings are the available columns in table 'person2band':
 * @property string $auto_id
 * @property string $person_id
 * @property string $band_id
 * @property integer $status_flag
 * @property integer $live_flag
 * @property string $jahr_von
 * @property string $jahr_bis
 * @property string $beschreibung
 * @property string $posten
 * @property integer $sortierung
 */
class Person2Band extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Person2Band the static model class
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
		return 'person2band';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('posten', 'required'),
			array('status_flag, live_flag, sortierung', 'numerical', 'integerOnly'=>true),
			array('person_id, band_id', 'length', 'max'=>10),
			array('jahr_von, jahr_bis', 'length', 'max'=>4),
			array('beschreibung, posten', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('auto_id, person_id, band_id, status_flag, live_flag, jahr_von, jahr_bis, beschreibung, posten, sortierung', 'safe', 'on'=>'search'),
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
			'person_id' => 'Person',
			'band_id' => 'Band',
			'status_flag' => 'Status Flag',
			'live_flag' => 'Live Flag',
			'jahr_von' => 'Jahr Von',
			'jahr_bis' => 'Jahr Bis',
			'beschreibung' => 'Beschreibung',
			'posten' => 'Posten',
			'sortierung' => 'Sortierung',
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
		$criteria->compare('person_id',$this->person_id,true);
		$criteria->compare('band_id',$this->band_id,true);
		$criteria->compare('status_flag',$this->status_flag);
		$criteria->compare('live_flag',$this->live_flag);
		$criteria->compare('jahr_von',$this->jahr_von,true);
		$criteria->compare('jahr_bis',$this->jahr_bis,true);
		$criteria->compare('beschreibung',$this->beschreibung,true);
		$criteria->compare('posten',$this->posten,true);
		$criteria->compare('sortierung',$this->sortierung);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}