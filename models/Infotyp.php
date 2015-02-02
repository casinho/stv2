<?php

/**
 * This is the model class for table "v4_infotypen".
 *
 * The followings are the available columns in table 'v4_infotypen':
 * @property integer $infotyp_id
 * @property string $infotyp
 * @property integer $info_flag
 * @property string $bereich
 * @property integer $sortierung
 */
class Infotyp extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Infotyp the static model class
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
		return 'infotypen';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('infotyp, info_flag, bereich, sortierung', 'required'),
			array('info_flag, sortierung', 'numerical', 'integerOnly'=>true),
			array('infotyp', 'length', 'max'=>255),
			array('bereich', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('infotyp_id, infotyp, info_flag, bereich, sortierung', 'safe', 'on'=>'search'),
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
			'infotyp_id' => 'Infotyp',
			'infotyp' => 'Infotyp',
			'info_flag' => 'Info Flag',
			'bereich' => 'Bereich',
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

		$criteria->compare('infotyp_id',$this->infotyp_id);
		$criteria->compare('infotyp',$this->infotyp,true);
		$criteria->compare('info_flag',$this->info_flag);
		$criteria->compare('bereich',$this->bereich,true);
		$criteria->compare('sortierung',$this->sortierung);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}