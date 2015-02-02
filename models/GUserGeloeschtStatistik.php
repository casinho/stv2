<?php

/**
 * This is the model class for table "tm_user_geloescht_statistik".
 *
 * The followings are the available columns in table 'tm_user_geloescht_statistik':
 * @property string $datum
 * @property string $zeit
 * @property string $grund
 * @property integer $grund_id
 */
class GUserGeloeschtStatistik extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TmUserGeloeschtStatistik the static model class
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
		return 'tm_user_geloescht_statistik';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('grund, grund_id', 'required'),
			array('grund_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('datum, zeit','default','value'=>new CDbExpression('NOW()'),'setOnEmpty'=>false,'on'=>'insert'),
			array('datum, zeit, grund, grund_id', 'safe', 'on'=>'search'),
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
			'datum' => 'Datum',
			'zeit' => 'Zeit',
			'grund' => 'Grund',
			'grund_id' => 'Grund',
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

		$criteria->compare('datum',$this->datum,true);
		$criteria->compare('zeit',$this->zeit,true);
		$criteria->compare('grund',$this->grund,true);
		$criteria->compare('grund_id',$this->grund_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}