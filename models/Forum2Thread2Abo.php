<?php

/**
 * This is the model class for table "forum2thread2abo".
 *
 * The followings are the available columns in table 'forum2thread2abo':
 * @property string $auto_id
 * @property string $forum_id
 * @property string $thread_id
 * @property string $post_id
 * @property string $user_id
 * @property string $datum
 */
class Forum2Thread2Abo extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'forum2thread2abo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('forum_id, thread_id, user_id', 'required'),
			array('forum_id, thread_id, post_id, user_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('auto_id, forum_id, thread_id, post_id, user_id, datum', 'safe', 'on'=>'search'),
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
			'post_id' => 'Post',
			'user_id' => 'User',
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
		$criteria->compare('post_id',$this->post_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('datum',$this->datum,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Forum2Thread2Abo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function holeUserAbos($user_id = false,$pageSize = 10,$forum_id = 0) {
	
		if($user_id === false) {
			$user_id = Yii::app()->user->getId();
		}
	
	
		if($forum_id > 0) {
			$sql_statement = "SELECT *, auto_id AS id FROM ".$this->tableName()." WHERE user_id = ".$user_id." AND master_id = ".$forum_id."";
		} else {
			$sql_statement = "SELECT *, auto_id AS id FROM ".$this->tableName()." WHERE user_id = ".$user_id."";
		}
	
		$userAbos = Yii::app()->db->createCommand($sql_statement)->queryAll();
	
		$forum = array();
	
		foreach($userAbos as $k => $v)  {
	
			$userAbos[$k]['forum'] 			= CHtml::tag('i',array(),Yii::t('forum','geloescht'));
			$userAbos[$k]['thread'] 		= CHtml::tag('i',array(),Yii::t('forum','geloescht'));
			$userAbos[$k]['letzte_antwort'] = null;
	
			if(!isset($forum[$v['forum_id']])) {
				$forum[$v['forum_id']] = Forum::model()->findByPk($v['forum_id']);
			}
	
			if(!empty($forum[$v['forum_id']])) {
				$userAbos[$k]['forum'] = CHtml::link($forum[$v['forum_id']]['forum_titel'],array('forum/detail', 'id' => $v['forum_id'], 'seo' => GFunctions::normalisiereString($forum[$v['forum_id']]['forum_titel'])));
				$thread	= ForumThread::model(null,$v['forum_id'])->findByPk($v['thread_id']);
				if(!empty($thread)) {
					// 					$userAbos[$k]['thread'] = CHtml::link($thread['thread_title'],array('thread/anzeige','id' => $v['master_id'], 'seo' => TMFunctions::normalisiereString($thread['thread_title']), 'thread_id'=>$thread['thread_id']));
					$userAbos[$k]['thread'] = CHtml::link($thread['thread_titel'],array('forum/thread','id' => $v['forum_id'], 'seo' => GFunctions::normalisiereString($thread['thread_titel']), 'thread_id'=>$thread['thread_id']));
					$userAbos[$k]['letzte_antwort'] = $thread['datum_antwort'];
				}
			}
	
		}
	
		$criteria = new CDbCriteria();
	
		if($forum_id > 0) {
			$criteria->condition = 'user_id = :user_id AND forum_id = :forum_id';
			$criteria->params = array (':user_id' => $user_id,':forum_id'=>$forum_id);
		} else {
			$criteria->condition = 'user_id = :user_id';
			$criteria->params = array (':user_id' => $user_id);
		}
	
		$item_count = Forum2Thread2Abo::model()->count($criteria);
	
		//--- original
	
	
		$pages = new CPagination($item_count);
		$pages->setPageSize($pageSize);
		$pages->applyLimit($criteria);
	
		$sort = new CSort();
		$sort->defaultOrder = 'letzte_antwort DESC';
		$sort->attributes = array(
				'user_nick' 	=> array('user_nick' => 'asc'),
				'nachname' 		=> array('default' => 'asc'),
				'vorname' 		=> array('default' => 'asc'),
				'datum' 		=> array(
						'asc'	=> 'letzte_antwort',
						'desc'	=> 'letzte_antwort DESC',
				),
				'thread' 		=> array(
						'asc'	=> 'thread',
						'desc'	=> 'thread DESC',
				),
				'forum' 		=> array(
						'asc'	=> 'forum',
						'desc'	=> 'forum DESC',
				)
		);
	
		$dataProvider=new CArrayDataProvider($userAbos, array(
				'id'			=> 'auto_id',
				'totalItemCount'=> $item_count,
				'sort' 			=> $sort,
				'pagination'=>array(
						'pageSize'=> $pageSize,
				)
		));
	
		return $dataProvider;
	}
	
	
}
