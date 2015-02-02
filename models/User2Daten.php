<?php

/**
 * This is the model class for table "v4_user2daten".
 *
 * The followings are the available columns in table 'v4_user2daten':
 * @property string $user_id
 * @property string $datentyp_id
 * @property string $datenwert
 * @property integer $daten_anzeige
 */
class User2Daten extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User2Daten the static model class
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
		return 'user2daten';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, datentyp_id', 'required'),
			array('daten_anzeige', 'numerical', 'integerOnly'=>true),
			array('user_id, datentyp_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, datentyp_id, datenwert, daten_anzeige', 'safe', 'on'=>'search'),
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
			//'user2daten'=>array(self::BELONGS_TO, 'Datentyp', 'datentyp_id'),
			'Datentyp'=>array(self::HAS_MANY, 'Datentyp', 'datentyp_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' 		=> Yii::t('profil','user_id'),
			'datentyp_id' 	=> Yii::t('profil','datentyp_id'),
			'datenwert' 	=> Yii::t('profil','datenwert'),
			'daten_anzeige' => Yii::t('profil','daten_anzeige'),
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

		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('datentyp_id',$this->datentyp_id,true);
		$criteria->compare('datenwert',$this->datenwert,true);
		$criteria->compare('daten_anzeige',$this->daten_anzeige);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}