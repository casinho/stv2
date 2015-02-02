<?php

/**
 * This is the model class for table "post_log".
 *
 * The followings are the available columns in table 'post_log':
 * @property string $auto_id
 * @property integer $user_id
 * @property integer $forum_id
 * @property integer $thread_id
 * @property integer $post_id
 * @property string $datum_zeit
 */
class PostLog extends CActiveRecord
{
	
	public $thread;
	public $post;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return V4PostLog the static model class
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
		return 'post_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, forum_id, thread_id, post_id, datum_zeit', 'required'),
			array('user_id, forum_id, thread_id, post_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('auto_id, user_id, forum_id, thread_id, post_id, datum_zeit', 'safe', 'on'=>'search'),
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
			'forum' => array(
					self::HAS_ONE,
					'Forum',
					array('forum_id'=>'forum_id'),
			),	
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'auto_id' => 'Auto',
			'user_id' => 'User',
			'forum_id' => 'Master',
			'thread_id' => 'Thread',
			'post_id' => 'Post',
			'datum_zeit' => 'Datum Zeit',
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

		$criteria->compare('auto_id',$this->auto_id,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('forum_id',$this->forum_id);
		$criteria->compare('thread_id',$this->thread_id);
		$criteria->compare('post_id',$this->post_id);
		$criteria->compare('datum_zeit',$this->datum_zeit,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}