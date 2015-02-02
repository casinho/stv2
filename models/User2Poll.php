<?php

/**
 * This is the model class for table "user2polls".
 *
 * The followings are the available columns in table 'user2polls':
 * @property string $auto_id
 * @property string $forum_id
 * @property string $thread_id
 * @property string $user_id
 * @property string $option_id
 * @property string $datum
 */
class User2Poll extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user2poll';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('forum_id, thread_id, user_id, option_id, datum', 'required'),
			array('forum_id, thread_id, user_id, option_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('auto_id, forum_id, thread_id, user_id, option_id, datum', 'safe', 'on'=>'search'),
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
			'forum_id' => 'Forum',
			'thread_id' => 'Thread',
			'user_id' => 'User',
			'option_id' => 'Option',
			'datum' => 'Datum',
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

		$criteria->compare('auto_id',$this->auto_id,true);
		$criteria->compare('forum_id',$this->forum_id,true);
		$criteria->compare('thread_id',$this->thread_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('option_id',$this->option_id,true);
		$criteria->compare('datum',$this->datum,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User2polls the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
