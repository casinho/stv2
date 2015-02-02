<?php

/**
 * This is the model class for table "AuthAssignmentLog".
 *
 * The followings are the available columns in table 'AuthAssignmentLog':
 * @property integer $auto_id
 * @property string $itemname
 * @property string $userid
 * @property string $bizrule
 * @property string $data
 * @property string $user_id_bearbeiter
 * @property string $datum
 */
class AuthAssignmentLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuthAssignmentLog the static model class
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
		return 'AuthAssignmentLog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('itemname, userid, user_id_bearbeiter, datum', 'required'),
			array('itemname, userid', 'length', 'max'=>100),
			array('user_id_bearbeiter', 'length', 'max'=>10),
			array('bizrule, data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('auto_id, itemname, userid, bizrule, data, user_id_bearbeiter, datum', 'safe', 'on'=>'search'),
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
			'itemname' => 'Itemname',
			'userid' => 'Userid',
			'bizrule' => 'Bizrule',
			'data' => 'Data',
			'user_id_bearbeiter' => 'User Id Bearbeiter',
			'datum' => 'Datum',
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

		$criteria->compare('auto_id',$this->auto_id);
		$criteria->compare('itemname',$this->itemname,true);
		$criteria->compare('userid',$this->userid,true);
		$criteria->compare('bizrule',$this->bizrule,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('user_id_bearbeiter',$this->user_id_bearbeiter,true);
		$criteria->compare('datum',$this->datum,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}