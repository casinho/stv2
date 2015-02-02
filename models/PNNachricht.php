<?php

/**
 * This is the model class for table "pn_nachricht".
 *
 * The followings are the available columns in table 'pn_nachricht':
 * @property integer $nachricht_id
 * @property integer $erste_nachricht_id
 * @property string $nachricht
 * @property integer $absender_id
 * @property string $pn_datum
 */
class PNNachricht extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PNNachricht the static model class
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
		return 'pn_nachricht';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nachricht', 'required'),
			array('nachricht', 'validateNachricht'),
			array('erste_nachricht_id, absender_id', 'numerical', 'integerOnly'=>true),
			array('pn_datum', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('nachricht_id, erste_nachricht_id, nachricht, absender_id, pn_datum', 'safe', 'on'=>'search'),
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
			'pn_eingang' => array (
					self::HAS_ONE,
					'PNEingang',
					array (
						'nachricht_id' => 'erste_nachricht_id'
					),
					'joinType' => 'INNER JOIN'
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'nachricht_id' => 'Nachricht',
			'erste_nachricht_id' => 'Erste Nachricht',
			'nachricht' => 'Nachricht',
			'absender_id' => 'Absender',
			'pn_datum' => 'Pn Datum',
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

		$criteria->compare('nachricht_id',$this->nachricht_id);
		$criteria->compare('erste_nachricht_id',$this->erste_nachricht_id);
		$criteria->compare('nachricht',$this->nachricht,true);
		$criteria->compare('absender_id',$this->absender_id);
		$criteria->compare('pn_datum',$this->pn_datum,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
    public function validateNachricht($attribute,$params)
    {
		// HTML entfernen
		$this->nachricht = strip_tags($this->nachricht);
    }
	
	public function getPNEingang() {
		$nachricht_id = $this->erste_nachricht_id == null ? $this->nachricht_id : $this->erste_nachricht_id;
		return PNEingang::model()->findByAttributes(array('nachricht_id' => $nachricht_id));
	}
}