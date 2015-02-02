<?php

/**
 * This is the model class for table "pn_ausgang".
 *
 * The followings are the available columns in table 'pn_ausgang':
 * @property string $pn_id
 * @property string $titel
 * @property integer $nachricht_id
 * @property string $pn_datum
 * @property string $absender_id
 * @property string $empfaenger_id
 * @property string $empfaenger_multi
 * @property integer $anz_empfaenger
 * @property integer $gelesen
 */
class PNAusgang extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PNAusgang the static model class
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
		return 'pn_ausgang';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('titel, nachricht_id, pn_datum, absender_id, empfaenger_id', 'required'),
			array('nachricht_id, anz_empfaenger, gelesen', 'numerical', 'integerOnly'=>true),
			array('titel', 'length', 'max'=>250),
			array('absender_id, empfaenger_id', 'length', 'max'=>6),
			array('empfaenger_multi', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('pn_id, titel, nachricht_id, pn_datum, absender_id, empfaenger_id, empfaenger_multi, anz_empfaenger, gelesen', 'safe', 'on'=>'search'),
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
			'pn_id' => 'Pn',
			'titel' => 'Titel',
			'nachricht_id' => 'Nachricht',
			'pn_datum' => 'Pn Datum',
			'absender_id' => 'Absender',
			'empfaenger_id' => 'Empfaenger',
			'empfaenger_multi' => 'Empfaenger Multi',
			'anz_empfaenger' => 'Anz Empfaenger',
			'gelesen' => 'Gelesen',
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

		$criteria->compare('pn_id',$this->pn_id,true);
		$criteria->compare('titel',$this->titel,true);
		$criteria->compare('nachricht_id',$this->nachricht_id);
		$criteria->compare('pn_datum',$this->pn_datum,true);
		$criteria->compare('absender_id',$this->absender_id,true);
		$criteria->compare('empfaenger_id',$this->empfaenger_id,true);
		$criteria->compare('empfaenger_multi',$this->empfaenger_multi,true);
		$criteria->compare('anz_empfaenger',$this->anz_empfaenger);
		$criteria->compare('gelesen',$this->gelesen);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * @param integer $user_id
	 * @return integer
	 */
	public function countPostausgang($user_id = 0) {
		$sql = "SELECT COUNT(pn_id) FROM ".$this->tableName()." WHERE absender_id = ".$user_id;
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}
}