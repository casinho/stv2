<?php

/**
 * This is the model class for table "forum".
 *
 * The followings are the available columns in table 'forum':
 * @property string $forum_id
 * @property string $forum_titel
 * @property string $forum_icon
 * @property integer $parent_id
 * @property integer $child_id
 * @property string $beschreibung
 * @property integer $zugriffs_flag
 * @property integer $online_flag
 * @property integer $nummer
 * @property string $thread_id
 * @property string $thread_titel
 * @property string $thread_user_id
 * @property string $thread_user_nick
 * @property string $post_id
 * @property string $post_user_id
 * @property string $post_user_nick
 * @property string $datum_erstellt
 * @property string $datum_antwort
 * @property integer $anz_threads
 * @property integer $anz_posts
 */
class Forum extends CActiveRecord
{
	
	public $child;
	public $parent; 
	
	public $moderatoren;
	public $userAccess;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'forum';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('forum_titel, parent_id', 'required'),
			array('parent_id, child_id, zugriffs_flag, online_flag, nummer, anz_threads, anz_posts', 'numerical', 'integerOnly'=>true),
			array('forum_titel, thread_titel', 'length', 'max'=>255),
			array('forum_icon, thread_user_nick, post_user_nick', 'length', 'max'=>100),
			array('thread_id, thread_user_id, post_id, post_user_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('forum_id, forum_titel, forum_icon, parent_id, child_id, beschreibung, zugriffs_flag, online_flag, nummer, thread_id, thread_titel, thread_user_id, thread_user_nick, post_id, post_user_id, post_user_nick, datum_erstellt, datum_antwort, anz_threads, anz_posts', 'safe', 'on'=>'search'),
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
			'child' => array(
					self::HAS_MANY,
					'Forum',
					array('parent_id'=>'forum_id'),
			),				
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'forum_id' => 'Forum',
			'forum_titel' => 'Forum Titel',
			'forum_icon' => 'Forum Icon',
			'parent_id' => 'Parent',
			'child_id' => 'Child',
			'beschreibung' => 'Beschreibung',
			'zugriffs_flag' => 'Zugriffs Flag',
			'online_flag' => 'Online Flag',
			'nummer' => 'Nummer',
			'thread_id' => 'Thread',
			'thread_titel' => 'Thread Titel',
			'thread_user_id' => 'Thread User',
			'thread_user_nick' => 'Thread User Nick',
			'post_id' => 'Post',
			'post_user_id' => 'Post User',
			'post_user_nick' => 'Post User Nick',
			'datum_erstellt' => 'Datum Erstellt',
			'datum_antwort' => 'Datum Antwort',
			'anz_threads' => 'Anz Threads',
			'anz_posts' => 'Anz Posts',
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

		$criteria->compare('forum_id',$this->forum_id,true);
		$criteria->compare('forum_titel',$this->forum_titel,true);
		$criteria->compare('forum_icon',$this->forum_icon,true);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('child_id',$this->child_id);
		$criteria->compare('beschreibung',$this->beschreibung,true);
		$criteria->compare('zugriffs_flag',$this->zugriffs_flag);
		$criteria->compare('online_flag',$this->online_flag);
		$criteria->compare('nummer',$this->nummer);
		$criteria->compare('thread_id',$this->thread_id,true);
		$criteria->compare('thread_titel',$this->thread_titel,true);
		$criteria->compare('thread_user_id',$this->thread_user_id,true);
		$criteria->compare('thread_user_nick',$this->thread_user_nick,true);
		$criteria->compare('post_id',$this->post_id,true);
		$criteria->compare('post_user_id',$this->post_user_id,true);
		$criteria->compare('post_user_nick',$this->post_user_nick,true);
		$criteria->compare('datum_erstellt',$this->datum_erstellt,true);
		$criteria->compare('datum_antwort',$this->datum_antwort,true);
		$criteria->compare('anz_threads',$this->anz_threads);
		$criteria->compare('anz_posts',$this->anz_posts);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Forum the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function holeForen() {
		
		$criteria = new CDbCriteria;
		$criteria->condition = "online_flag = :flag";
		$criteria->params = array(':flag' => 1);		
		
		
		$foren = self::model()->with('child')->findAll($criteria);
		
		return $foren;		
	}
	
	public function aktualisiereForum($forum_id,$thread=null) {
	
		$forum = Forum::model()->findByPk($forum_id);
	
		if($thread == null) {
	
			$attributes['delete_flag'] 	= 0;
			$attributes['moved_forum_id'] = 0;
			$attributes['moved_thread_id']= 0;
			$conditions = array('order' => 'datum_antwort DESC');
			$thread = ForumThread::Model(null,$forum_id)->findByAttributes($attributes,$conditions);
			unset($attributes);
				
		}
	
		if($forum->online_flag == 1) {
			
			$qry = "SELECT COUNT(*) FROM forum_".$forum_id."_thread WHERE delete_flag = 0";
			$threads = Yii::app()->db->createCommand($qry)->queryScalar();			

			$qry = "SELECT COUNT(*) FROM forum_".$forum_id."_post WHERE delete_flag = 0";
			$posts = Yii::app()->db->createCommand($qry)->queryScalar();			
			
			$forum->thread_id			= $thread->thread_id;
			$forum->thread_titel		= $thread->thread_titel;
			$forum->thread_user_id		= $thread->user_id;
			$forum->thread_user_nick	= $thread->user_nick;
			$forum->post_user_id		= $thread->post_user_id;
			$forum->post_user_nick		= $thread->post_user_nick;			
			$forum->anz_posts			= $posts;
			$forum->anz_threads			= $threads;
			$forum->datum_antwort		= $thread->datum_antwort;
			$forum->datum_erstellt		= $thread->datum_erstellt;
			/*			
			GFunctions::pre($forum->attributes);			
			
			$forum->validate();
			
			GFunctions::pre($forum->getErrors());
			*/
			$forum->save();
			unset($forum);
		}
	}
	
	public static function getBoardsMoveTarget($forum_id) {
		
		$parent_sql = "SELECT forum_id,forum_titel FROM forum WHERE online_flag = 1 AND parent_id = 0";
	
	
		$boards = Yii::app()->db->createCommand($parent_sql)->queryAll();
	
		foreach($boards as $key => $value) {
	
			$child_sql = "SELECT
							forum_titel,forum_id
						FROM forum
						WHERE online_flag = 1 AND parent_id = ".$value['forum_id']."";
	
			$boards[$key]['childs'] = Yii::app()->db->createCommand($child_sql)->queryAll();
		}
	
		$output = array();
		foreach($boards as $k => $v) {
			$output[$v['forum_titel']] = Forum::getChildsToMove($v['childs']);
		}
		return $output;
		//return $boards;
	}
	
	public static function getChildsToMove($childs) {
		$output = array();
		foreach($childs as $key => $v) {
			$output[$v['forum_id']] = $v['forum_titel'];
		}
		return $output;
	}	
	
	public function holeAktuelleBeitraege($limit=10) {
		
		
		
		$criteria = new CDbCriteria();
		$criteria->group = 't.thread_id';
		$criteria->order = 't.datum_zeit DESC';
		$criteria->offset = 0;
		$criteria->limit = $limit;

		//$result = PostLog::model()->with('forum')->findAll($criteria);
		
		$qry = "SELECT p.auto_id,p.thread_id,p.post_id,f.forum_id,forum_titel FROM post_log AS p INNER JOIN forum AS f ON f.forum_id = p.forum_id ";
		
		if(Yii::app()->user->isGuest) {
			$qry.= "WHERE f.zugriffs_flag = 0 ORDER BY p.datum_zeit DESC LIMIT ".$limit;
		} else {
			if(Yii::app()->user->checkAccess('Superadmin')) {
				$qry.= "ORDER BY p.datum_zeit DESC LIMIT ".$limit;
			} else {
				$attributes['user_id'] = Yii::app()->user->getId();
				$foren = User2Forum::model()->findAllByAttributes($attributes);
				$a = array();
				
				foreach($foren as $k => $v) {
					$a[] = $v['forum_id'];
				}				
				
				if(!empty($a)) {
					$qry.= "WHERE f.zugriffs_flag = 0 OR p.forum_id IN (".implode(',',$a).") ORDER BY p.datum_zeit DESC LIMIT ".$limit;
				} else {
					$qry.= "WHERE f.zugriffs_flag = 0 ORDER BY p.datum_zeit DESC LIMIT ".$limit;
				}
			}
		}
			
		$result = Yii::app()->db->createCommand($qry)->queryAll();
		
		
			
		foreach($result as $k => $v) {
			$result[$k]['thread'] = ForumThread::model(null,$v['forum_id'])->findByPk($v['thread_id']);
			$result[$k]['post'] = ForumPost::model(null,$v['forum_id'])->findByPk($v['post_id']);
		}
		
		//GFunctions::pre($result);
		
				
		$sort = new CSort();
		$sort->defaultOrder = 't.datum_zeit DESC';
		
		
		$output = new CArrayDataProvider($result, array(
			'keyField' => 'auto_id',
			'pagination'=>array(
				'pageSize'=>10,
			),
//			'sort' => $sort,
		));
		
		return $output;
		
	}
	
	public static function checkForenZugriff($foren,$erlaubteForen) {
		if(!Yii::app()->user->isGuest && Yii::app()->user->checkAccess('Superadmin') !== false) {
			return $foren;
		} else {
			foreach($foren as $k => $v) {
				if($v['zugriffs_flag'] > 0) {
					if(!in_array($v['forum_id'],$erlaubteForen)) {
						unset($foren[$k]);
					}
				}
			}
		}
		
		return $foren;
	
	}
	
	

	public function holeMemberBeitraege($limit=10,$user_id=0) {
	
		$qry = "SELECT p.auto_id,p.thread_id,p.post_id,f.forum_id,forum_titel FROM post_log AS p INNER JOIN forum AS f ON f.forum_id = p.forum_id WHERE p.user_id = ".$user_id;
		
		if(Yii::app()->user->isGuest) {
			$qry.= " AND f.zugriffs_flag = 0 ORDER BY p.datum_zeit DESC LIMIT ".$limit;
		} else {
			if(Yii::app()->user->checkAccess('Superadmin')) {
				$qry.= " ORDER BY p.datum_zeit DESC LIMIT ".$limit;
			} else {
				$attributes['user_id'] = Yii::app()->user->getId();
				$foren = User2Forum::model()->findAllByAttributes($attributes);
				$a = array();
		
				foreach($foren as $k => $v) {
					$a[] = $v['forum_id'];
				}
		
				if(!empty($a)) {
					$qry.= " AND f.zugriffs_flag = 0 OR p.forum_id IN (".implode(',',$a).") ORDER BY p.datum_zeit DESC LIMIT ".$limit;
				} else {
					$qry.= " AND f.zugriffs_flag = 0 ORDER BY p.datum_zeit DESC LIMIT ".$limit;
				}
			}
		}
			
		$result = Yii::app()->db->createCommand($qry)->queryAll();		
				
		foreach($result as $k => $v) {
			$result[$k]['thread'] 	= ForumThread::model(null,$v['forum_id'])->findByPk($v['thread_id']);
			$result[$k]['post'] 	= ForumPost::model(null,$v['forum_id'])->findByPk($v['post_id']);
		}
	
		$sort = new CSort();
		$sort->defaultOrder = 't.datum_zeit DESC';
	
		$output = new CArrayDataProvider($result, array(
				'keyField' => 'auto_id',
				'pagination'=>array(
						'pageSize'=>10,
				),
				//'sort' => $sort,
		));
	
	
		return $output;
	
	}	
	
	public function getForumModeratoren() {
		$moderatoren = $this->getModeratoren();
	
		$out = array();
	
		$usr = array();
		$ids = array();
		
		if(!empty($moderatoren)) {
			foreach($moderatoren as $k => $v) {
				$usr[] = $v['text'];
				$ids[] = $v['id'];
				$out[] = array('id'=>$v['id'],'text'=>$v['text']);
			}
		}
	
		#GFunctions::pre($tag);
		//GFunctions::pre($ids);

		$str_usr = implode(', ',$usr);
		$str_ids = implode(',',$ids);
		
		
	
		$output = CHtml::link($str_usr,'#',array('data-pk'=>$this->forum_id,'class'=>'editable editable-click','data-value'=>$str_ids, 'rel'=>'Forum_moderatoren', 'id'=>'Forum_moderatoren_'.$this->forum_id));
		//$output = '<a href="#" data-pk="'.$this->user_id.'" class="editable editable-click" data-value="'.$str_ids.'">'.$str_tag.'</a>';
		return $output;

	}
	
	public function getModeratoren() {
	
		$qry = "SELECT u.user_id,u.user_nick FROM user2forum u2f INNER JOIN user AS u ON u.user_id = u2f.user_id WHERE u2f.forum_id = ".$this->forum_id." AND u2f.haupt_flag =  1";
	
		$res = Yii::app()->db->createCommand($qry)->queryAll();
	
		$output = array();
		foreach($res as $k => $v) {
			$output[] = array('id' => $v['user_id'], 'text' => $v['user_nick']);
		}
		return $output;
	}
	
	
	
	public static function getFlags() {
		$output[0] = array('value'=>0,'text'=>Yii::t('forum','public'));
		$output[1] = array('value'=>1,'text'=>Yii::t('forum','intern'));
		//$output[2] = array('value'=>2,'text'=>Yii::t('member','ex_member'));
	
		return $output;
	}	

	public static function getModeratorStatus() {
		$output[0] = array('value'=>0,'text'=>Yii::t('forum','kein_Moderator'));
		$output[1] = array('value'=>1,'text'=>Yii::t('forum','moderator'));
		//$output[2] = array('value'=>2,'text'=>Yii::t('member','ex_member'));
	
		return $output;
	}
	
	
	public static function getFlagsOptionen() {
	
		$array = array(Yii::t('forum','public'),Yii::t('forum','intern'));
	
		return $array;
	}
	
	public function getParentsOptionen() {
		$criteria = new CDbCriteria();
		$criteria->condition = 'parent_id =:parent_id';
		$criteria->params = array(':parent_id'=>0);
		$criteria->order = 'nummer ASC';
	
		$parents = Forum::model()->findAll($criteria);
	
		$output = array();
		$output[0] = Yii::t('forum','elternforum');
		foreach($parents as $k => $v) {
			$output[$v->forum_id] = $v->forum_titel;
		}
		return $output;
	}
	
	public static function getGueltigeGruppen() {
		$gruppen = array('Superadmin' => Yii::t('global', 'Clanleader'),
				'SquadLeader' => Yii::t('global', 'SquadLeader'),
				'Clan-Member' => Yii::t('global', 'Clan-Member'),);
		
		$squads = array();
		if (Yii::app()->user->checkAccess('Superadmin')) {
			$criteria = new CDbCriteria();
			$criteria->condition = 'st_flag = 1';
			$res = Squad::model()->findAll($criteria);
			foreach($res as $key => $v) {
				$squads[$v->squad_id] = $v->squad_tag;
			}
		} else {
			$criteria = new CDbCriteria();
			$criteria->condition = 'user_id = '.Yii::app()->user->getId().' AND (leader_flag = 1 OR orga_flag = 1)';
			if (Yii::app()->user->checkAccess('SquadLeader')) {
				$squadzuweisung = User2Squad::model()->findAll($criteria);
		
				foreach($squadzuweisung as $key => $v) {
					$squads[$v->squad->squad_id] = Yii::t('global','squad').': '.$v->squad->squad_tag;
				}
		
			}
		}
		
		foreach($squads as $k => $v) {
			$gruppen[$k] = $v;
		}
		
		$output = array();
		
		foreach($gruppen as $k => $v) {
			$output[] = array('id'=> $k, 'name'=>$v);
		}
		
		return $gruppen;		
	}
	
	public static function getStatus() {
		$output[0] = array('value'=>0,'text'=>Yii::t('forum','offline'));
		$output[1] = array('value'=>1,'text'=>Yii::t('forum','online'));
		//$output[2] = array('value'=>2,'text'=>Yii::t('member','ex_member'));
	
		return $output;
	}	
	
	protected function afterFind ()	{
		
		if($this->parent_id == 0) {
			$this->moderatoren = '';
		} else {
			$this->moderatoren = $this->getForumModeratoren();
		}
		
		parent::afterFind ();
	}	
	
}
