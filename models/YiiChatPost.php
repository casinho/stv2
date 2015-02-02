<?php

/**
 * This is the model class for table "yiichat_post".
 *
 * The followings are the available columns in table 'yiichat_post':
 * @property string $id
 * @property string $chat_id
 * @property string $post_identity
 * @property string $owner
 * @property string $created
 * @property string $text
 * @property string $data
 */
class YiiChatPost extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'yiichat_post';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chat_id, post_identity, owner, text', 'required'),
			array('owner', 'length', 'max'=>20),
			array('created', 'length', 'max'=>30),
			array('text, data', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, chat_id, post_identity, owner, created, text, data', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'chat_id' => 'Chat',
			'post_identity' => 'Post Identity',
			'owner' => 'Owner',
			'created' => 'Created',
			'text' => 'Text',
			'data' => 'Data',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('chat_id',$this->chat_id,true);
		$criteria->compare('post_identity',$this->post_identity,true);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('data',$this->data,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return YiiChatPost the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
