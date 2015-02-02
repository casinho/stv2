<?php

/**
 * This is the model class for table "pn_queue".
 *
 * The followings are the available columns in table 'pn_queue':
 * @property integer $queue_id
 * @property string $titel
 * @property string $msg
 * @property string $datum
 * @property string $empfaenger_rollen
 * @property integer $last_empfaenger_id
 * @property integer $user_id
 * @property integer $absender_id
 */
class PNQueue extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pn_queue';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('titel, msg, datum, empfaenger_rollen, user_id, absender_id', 'required'),
			array('last_empfaenger_id, user_id, absender_id', 'numerical', 'integerOnly'=>true),
			array('titel', 'length', 'max'=>100),
			array('empfaenger_rollen', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('queue_id, titel, msg, datum, empfaenger_rollen, last_empfaenger_id, user_id, absender_id', 'safe', 'on'=>'search'),
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
			'queue_id' => 'Queue',
			'titel' => 'Titel',
			'msg' => 'Msg',
			'datum' => 'Datum',
			'empfaenger_rollen' => 'Empfaenger Rollen',
			'last_empfaenger_id' => 'user_id der zuletzt verschickten PN',
			'user_id' => 'user_id des echten Absenders',
			'absender_id' => 'user_id, welche als Absender angezeigt wird.',
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

		$criteria->compare('queue_id',$this->queue_id);
		$criteria->compare('titel',$this->titel,true);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('datum',$this->datum,true);
		$criteria->compare('empfaenger_rollen',$this->empfaenger_rollen,true);
		$criteria->compare('last_empfaenger_id',$this->last_empfaenger_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('absender_id',$this->absender_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PnQueue the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
