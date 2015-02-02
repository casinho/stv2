<?php

/**
 * This is the model class for table "tm_polls".
 *
 * The followings are the available columns in table 'tm_polls':
 * @property string $option_id
 * @property integer $forum_id
 * @property string $thread_id
 * @property string $option
 * @property string $count_votes
 * @property integer $sort
 */
class Polls extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Polls the static model class
	 */
	
	public $gesamtstimmen;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'polls';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('forum_id, thread_id, sort, option', 'numerical', 'integerOnly'=>true),
			array('thread_id, count_votes', 'length', 'max'=>12),
			array('optionen', 'length', 'max'=>245),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('option_id, forum_id, thread_id, option, count_votes, sort', 'safe', 'on'=>'search'),
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
			'option_id' => 'OptionID',
			'forum_id' => 'Forum',
			'thread_id' => 'Thread',
			'option' => 'Option',
			'count_votes' => 'Count Votes',
			'sort' => 'Sort',
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

		$criteria->compare('option_id',$this->option_id,true);
		$criteria->compare('forum_id',$this->forum_id);
		$criteria->compare('thread_id',$this->thread_id,true);
		$criteria->compare('option',$this->option,true);
		$criteria->compare('count_votes',$this->count_votes,true);
		$criteria->compare('sort',$this->sort);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getProzent($gesamtstimmen = 0) {
		if($gesamtstimmen == 0) {
			return 0;
		} else {
			if($this->count_votes > 0) {
				return ($this->count_votes*100/$gesamtstimmen);
			} else { 
				return 0;
			}			
		}
	}
	
	
}