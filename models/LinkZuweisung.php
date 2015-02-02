<?php

/**
 * This is the model class for table "linkZuweisung".
 *
 * The followings are the available columns in table 'linkZuweisung':
 * @property string $link_id
 * @property string $fremd_id
 * @property string $link
 * @property string $link_text
 * @property string $zuweisung
 */
class LinkZuweisung extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return LinkZuweisung the static model class
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
		return 'linkzuweisung';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fremd_id, link, link_text, zuweisung', 'required'),
			array('fremd_id', 'length', 'max'=>11),
			array('link, link_text', 'length', 'max'=>255),
			array('zuweisung', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('link_id, fremd_id, link, link_text, zuweisung', 'safe', 'on'=>'search'),
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
			'link_id' => 'Link',
			'fremd_id' => 'Fremd',
			'link' => 'Link',
			'link_text' => 'Link Text',
			'zuweisung' => 'Zuweisung',
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

		$criteria->compare('link_id',$this->link_id,true);
		$criteria->compare('fremd_id',$this->fremd_id,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('link_text',$this->link_text,true);
		$criteria->compare('zuweisung',$this->zuweisung,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}