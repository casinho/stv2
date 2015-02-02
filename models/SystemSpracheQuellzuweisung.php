<?php

/**
 * This is the model class for table "system_sprache_quellzuweisung".
 *
 * The followings are the available columns in table 'system_sprache_quellzuweisung':
 * @property string $quelle_id
 * @property integer $quelldatei_id
 */
class SystemSpracheQuellzuweisung extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'system_sprache_quellzuweisung';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quelle_id, quelldatei_id', 'required'),
			array('quelldatei_id', 'numerical', 'integerOnly'=>true),
			array('quelle_id', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('quelle_id, quelldatei_id', 'safe', 'on'=>'search'),
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
			'quelle_id' => 'Quelle',
			'quelldatei_id' => 'Quelldatei',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('quelle_id',$this->quelle_id,true);
		$criteria->compare('quelldatei_id',$this->quelldatei_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SystemSpracheQuellzuweisung the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
