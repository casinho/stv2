<?php

/**
 * This is the model class for table "person".
 *
 * The followings are the available columns in table 'person':
 * @property string $person_id
 * @property string $vorname
 * @property string $nachname
 * @property string $spitzname
 * @property string $anzeige_name
 * @property string $voller_name
 * @property string $geschlecht
 * @property string $foto
 * @property string $tags
 * @property integer $land_id
 * @property string $geburtsdatum
 * @property string $geburtsjahr
 * @property integer $geburtsmonat
 * @property integer $geburtstag
 * @property string $todesdatum
 * @property integer $verstorben_flag
 * @property integer $age
 * @property integer $produzent_flag
 * @property integer $musiker_flag
 * @property integer $artwork_flag
 * @property string $user_id
 * @property string $erstellt_datum
 * @property string $bearbeitet_datum
 * @property string $bearbeitet_user_id
 */
class Person extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Person the static model class
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
		return 'person';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('anzeige_name', 'required'),
			array('land_id, geburtsmonat, geburtstag, verstorben_flag, age, produzent_flag, musiker_flag, artwork_flag', 'numerical', 'integerOnly'=>true),
			array('vorname, foto', 'length', 'max'=>120),
			array('nachname, spitzname', 'length', 'max'=>160),
			array('anzeige_name, voller_name, tags', 'length', 'max'=>255),
			array('geschlecht', 'length', 'max'=>1),
			array('geburtsjahr', 'length', 'max'=>4),
			array('user_id, bearbeitet_user_id', 'length', 'max'=>10),
			array('geburtsdatum, todesdatum, erstellt_datum, bearbeitet_datum', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('person_id, vorname, nachname, spitzname, anzeige_name, voller_name, geschlecht, foto, tags, land_id, geburtsdatum, geburtsjahr, geburtsmonat, geburtstag, todesdatum, verstorben_flag, age, produzent_flag, musiker_flag, artwork_flag, user_id, erstellt_datum, bearbeitet_datum, bearbeitet_user_id', 'safe', 'on'=>'search'),
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
			'person_id' => 'Person',
			'vorname' => 'Vorname',
			'nachname' => 'Nachname',
			'spitzname' => 'Spitzname',
			'anzeige_name' => 'Anzeige Name',
			'voller_name' => 'Voller Name',
			'geschlecht' => 'Geschlecht',
			'foto' => 'Foto',
			'tags' => 'Tags',
			'land_id' => 'Land',
			'geburtsdatum' => 'Geburtsdatum',
			'geburtsjahr' => 'Geburtsjahr',
			'geburtsmonat' => 'Geburtsmonat',
			'geburtstag' => 'Geburtstag',
			'todesdatum' => 'Todesdatum',
			'verstorben_flag' => 'Verstorben Flag',
			'age' => 'Age',
			'produzent_flag' => 'Produzent Flag',
			'musiker_flag' => 'Musiker Flag',
			'artwork_flag' => 'Artwork Flag',
			'user_id' => 'User',
			'erstellt_datum' => 'Erstellt Datum',
			'bearbeitet_datum' => 'Bearbeitet Datum',
			'bearbeitet_user_id' => 'Bearbeitet User',
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

		$criteria->compare('person_id',$this->person_id,true);
		$criteria->compare('vorname',$this->vorname,true);
		$criteria->compare('nachname',$this->nachname,true);
		$criteria->compare('spitzname',$this->spitzname,true);
		$criteria->compare('anzeige_name',$this->anzeige_name,true);
		$criteria->compare('voller_name',$this->voller_name,true);
		$criteria->compare('geschlecht',$this->geschlecht,true);
		$criteria->compare('foto',$this->foto,true);
		$criteria->compare('tags',$this->tags,true);
		$criteria->compare('land_id',$this->land_id);
		$criteria->compare('geburtsdatum',$this->geburtsdatum,true);
		$criteria->compare('geburtsjahr',$this->geburtsjahr,true);
		$criteria->compare('geburtsmonat',$this->geburtsmonat);
		$criteria->compare('geburtstag',$this->geburtstag);
		$criteria->compare('todesdatum',$this->todesdatum,true);
		$criteria->compare('verstorben_flag',$this->verstorben_flag);
		$criteria->compare('age',$this->age);
		$criteria->compare('produzent_flag',$this->produzent_flag);
		$criteria->compare('musiker_flag',$this->musiker_flag);
		$criteria->compare('artwork_flag',$this->artwork_flag);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('erstellt_datum',$this->erstellt_datum,true);
		$criteria->compare('bearbeitet_datum',$this->bearbeitet_datum,true);
		$criteria->compare('bearbeitet_user_id',$this->bearbeitet_user_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}