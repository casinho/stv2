<?php

/**
 * This is the model class for table "cronjob".
 *
 * The followings are the available columns in table 'cronjob':
 * @property string $id
 * @property string $cronjob
 * @property string $start
 * @property string $ende
 * @property integer $status
 * @property string $info
 */
class Cronjob extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cronjob';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cronjob, start, ende, status, info', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('cronjob', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('cronjob_id, cronjob, start, ende, status, info', 'safe', 'on'=>'search'),
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
			'cronjonb_id' => 'ID',
			'cronjob' => 'Cronjob',
			'start' => 'Start',
			'ende' => 'Ende',
			'status' => 'Status',
			'info' => 'Info',
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

		$criteria->compare('cronjob_id',$this->cronjob_id,true);
		$criteria->compare('cronjob',$this->cronjob,true);
		$criteria->compare('start',$this->start,true);
		$criteria->compare('ende',$this->ende,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('info',$this->info,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cronjob the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function erstelleEintrag($cron = 'n.D', $start = '0000-00-00 00:00:00', $status = 0, $info = '-') {
		$cronjob = new Cronjob;
		$cronjob->cronjob 	= $cron;
		$cronjob->status	= $status;
		$cronjob->start		= $start;
		$cronjob->ende		= date('Y-m-d H:i:s');
		$cronjob->info		= $info;
		$cronjob->save();
	}
}
