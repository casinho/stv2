<?php

/**
 * This is the model class for table "user2forum".
 *
 * The followings are the available columns in table 'user2forum':
 * @property string $user_id
 * @property string $forum_id
 * @property string $haupt_flag
 * @property string $co_flag
 */
class User2Forum extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User2Forum the static model class
	 */
	
	public $user_nick;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user2forum';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, forum_id', 'required'),
			array('user_id', 'length', 'max'=>10),
			array('forum_id', 'length', 'max'=>3),
			array('haupt_flag, co_flag', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, forum_id, haupt_flag, co_flag', 'safe', 'on'=>'search'),
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
			'user' => array(
				self::HAS_ONE,
				'User',
				array(
					'user_id' => 'user_id'
				),
				'joinType' => 'INNER JOIN',
			)
		);				
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			#'auto_id' => 'ID',
			'user_id' => 'User',
			'forum_id' => 'Forum',
			'haupt_flag' => 'Haupt Flag',
			'co_flag' => 'Co Flag',
		);
	}
	
	public function getUsernick() {
		$user = User::model()->findByPk($this->user_id);
		if($user != null) {
			$this->user_nick = $user->user_nick;
		}
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

		#$criteria->compare('auto_id',$this->user_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('forum_id',$this->forum_id,true);
		$criteria->compare('haupt_flag',$this->haupt_flag,true);
		$criteria->compare('co_flag',$this->co_flag,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}