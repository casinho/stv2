<?php

/**
 * This is the model class for table "forum_1_post".
 *
 * The followings are the available columns in table 'forum_1_post':
 * @property string $post_id
 * @property string $thread_id
 * @property string $user_id
 * @property string $user_nick
 * @property string $user_ip
 * @property string $titel
 * @property string $msg
 * @property string $datum_erstellt
 * @property string $datum_bearbeitet
 * @property string $edit_user_id
 * @property string $edit_user_nick
 * @property string $edit_user_ip
 * @property string $sprache
 * @property integer $delete_flag
 */
class ForumPost extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	protected $forum_id = NULL;
	private $_md;
	private $_postLogging = true;
	
	public $page = 1;

	public $userdaten;
	public $verein;
	public $user2posts;
	public $threadUser;
	
	public $anzeigeoptionen;
	
	public $verschieben = false;
	
	public function __construct($scenario, $forum_id = null) {
		$this->forum_id = $forum_id;
		parent::__construct($scenario);
	}
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ForumBeitrag the static model class
	 */
	public static function model($className=__CLASS__) {
		$className = (is_null($className)) ? __CLASS__ : $className;
		$params = func_get_args(); 
		$new_table_id = (is_array($params) && isset($params[1])) ? $params[1] : NULL;

		/*
		 * Wir koennen hier nicht die Parent-Methode aufrufen,
		 * da wir einen zweiten Parameter benoetigen.
		 * Die folgenden Zeilen sind aber auch zu finden in parent::model();
		 */
		$model = new $className(null, $new_table_id);
		$model->_md=new CActiveRecordMetaData($model);
        $model->attachBehaviors($model->behaviors());
		return $model;
	}
	
	protected function instantiate($attributes)	{
		$class=get_class($this);
		$model=new $class(null, $this->forum_id);
		return $model;
	}
	
	public function getMetaData() {
		if($this->_md!==null) {
			return $this->_md;
		} else {
			return $this->_md=self::model(get_class($this), $this->forum_id)->_md;
		}
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'forum_'.$this->forum_id.'_post';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('thread_id, user_id, user_nick, user_ip, msg, datum_erstellt, sprache', 'required'),
			array('delete_flag', 'numerical', 'integerOnly'=>true),
			array('thread_id, user_id, edit_user_id', 'length', 'max'=>10),
			array('user_nick, user_ip, edit_user_nick, edit_user_ip', 'length', 'max'=>100),
			array('titel', 'length', 'max'=>255),
			array('sprache', 'length', 'max'=>4),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('post_id, thread_id, user_id, user_nick, user_ip, titel, msg, datum_erstellt, datum_bearbeitet, edit_user_id, edit_user_nick, edit_user_ip, sprache, delete_flag', 'safe', 'on'=>'search'),
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
			'post_id' => 'Post',
			'thread_id' => 'Thread',
			'user_id' => 'User',
			'user_nick' => 'User Nick',
			'user_ip' => 'User Ip',
			'titel' => 'Titel',
			'msg' => 'Msg',
			'datum_erstellt' => 'Datum Erstellt',
			'datum_bearbeitet' => 'Datum Bearbeitet',
			'edit_user_id' => 'Edit User',
			'edit_user_nick' => 'Edit User Nick',
			'edit_user_ip' => 'Edit User Ip',
			'sprache' => 'Sprache',
			'delete_flag' => 'Delete Flag',
			'post_option' => Yii::t('forum','beitrag_wie_oft'),
			'post_flag' => Yii::t('forum','beitrag_als_hinweis'),		
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

		$criteria->compare('post_id',$this->post_id,true);
		$criteria->compare('thread_id',$this->thread_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('user_nick',$this->user_nick,true);
		$criteria->compare('user_ip',$this->user_ip,true);
		$criteria->compare('titel',$this->titel,true);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('datum_erstellt',$this->datum_erstellt,true);
		$criteria->compare('datum_bearbeitet',$this->datum_bearbeitet,true);
		$criteria->compare('edit_user_id',$this->edit_user_id,true);
		$criteria->compare('edit_user_nick',$this->edit_user_nick,true);
		$criteria->compare('edit_user_ip',$this->edit_user_ip,true);
		$criteria->compare('sprache',$this->sprache,true);
		$criteria->compare('delete_flag',$this->delete_flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getAnzeigeOptionen() {
		$optionen = array();
	
		$optionen[0] = Yii::t('forum','immer');
		$optionen[1] = Yii::t('forum','stuendlich');
		$optionen[2] = Yii::t('forum','alle_x_stunde',array('{stunde}'=>2));
		$optionen[4] = Yii::t('forum','alle_x_stunde',array('{stunde}'=>4));
		$optionen[24] = Yii::t('forum','einmal_am_tag');
		$optionen[48] = Yii::t('forum','alle_x_tage',array('{tag}'=>2));
		$optionen[72] = Yii::t('forum','alle_x_tage',array('{tag}'=>3));
		$optionen[168] = Yii::t('forum','alle_x_tage',array('{tag}'=>7));
	
		return $optionen;
	}
	
	
	protected function afterSave() {
		parent::afterSave();
	
		if(get_class(Yii::app()) != 'CConsoleApplication') {
			
			ForumThread::Model(null,$this->forum_id)->aktualisiereLetztenThread($this->thread_id,$this);
				
			if($this->isNewRecord) {
				$this->aktualisiereBeitragsCounter();
				if (!$this->verschieben) {
					if($this->_postLogging === true) {
						$postLog = new PostLog();
	
						$postLog->forum_id 	= $this->forum_id;
						$postLog->thread_id = $this->thread_id;
						$postLog->post_id 	= $this->post_id;
						$postLog->user_id 	= $this->user_id;
						$postLog->datum_zeit= new CDbExpression('NOW()');
	
						$postLog->save(false);
					}
					
					try {
						$this->sendAboMails();
					} catch(Exception $e) {
						//print_r($e->getMessage());
					}
					
					
				}
				$this->isNewRecord = false;
			}
			$this->setAnzahlSeiten($this->getPrimaryKey());
		}
	}

	
	private function sendAboMails() {
		
		$attributes['forum_id'] 	= $this->forum_id;
		$attributes['thread_id'] 	= $this->thread_id;
		
		//GFunctions::pre($attributes);
		
		$abos = Forum2Thread2Abo::model()->findAllByAttributes($attributes);
		
		if($abos != null) {
		
			foreach($abos as $k => $v) {
				
				if($v['user_id']!=$this->user_id) {
					
					$user = User::model()->findByPk($v['user_id']);
					
					$forum 	= Forum::model()->findByPk($this->forum_id);
					$thread = ForumThread::model(null,$this->forum_id)->findByPk($this->thread_id);
					
					if($user != null) {
						if(!empty($user->email)) {
							$message = new YiiMailMessage(Yii::app()->params['clan'].': '.Yii::t('forum', 'neue_antwort'));
							$message->view = 'neueAntwort';
							$message->setBody(array('user'=>$user,'forum'=>$forum,'thread'=>$thread, 'post' => $this->attributes,'host'=>Yii::app()->request->getHostInfo()), 'text/html');
							$message->setTo(array($user->email=>$user->email));
							$message->setFrom(array(Yii::app()->params['noReplyMail'] => Yii::app()->params['clan']));
							
							try {
								Yii::app()->mail->send($message);
							} catch (Exception $e) {
								GFunctions::pre($e);
							}						
						}
					}
					
				}
				
			}
		}
		
	}
	
	private function aktualisiereBeitragsCounter($user_id = false,$plus = true,$anzahl = 1) {
		if($user_id === false) {
			$user_id = $this->user_id;
		}
		User2Count::updateCounts($user_id,'forum',$plus,$anzahl);
		return true;
	}	
	
	public function setPostOffline($post_id,$updateThread = true) {
		$post = $this->findByPk($post_id);
		$post->delete_flag = 1;
		$post->save(false);
		if($updateThread===true) {
			ForumThread::Model(null,$this->forum_id)->aktualisiereLetztenThread($post->thread_id);
		}
	}
	

	private function setAnzahlSeiten($post_id = 0) {
	
		$attributes['thread_id']  		= $this->thread_id;
		$attributes['startbeitrag_flag']= $this->startbeitrag_flag;
		$attributes['delete_flag']  	= 0;
	
		$anzahl	= $this->countByAttributes($attributes);
	
		if($anzahl < Yii::app()->params['page_size_thread']) {
			$this->page = 1;
		} else {
			$this->page = ceil($anzahl/Yii::app()->params['page_size_thread']);
		}
	}
	
	public static function holeStartBeitrag($forum_id,$thread_id) {
		if($forum_id != 0 && $thread_id != 0) {
				
			$attributes['thread_id'] 		= $thread_id;
			$attributes['delete_flag'] 		= 0;
			$attributes['startbeitrag_flag']= 1;
			$conditions['order'] 			= 'datum_erstellt ASC';
				
			$startbeitrag = ForumPost::model(null,$forum_id)->findByAttributes($attributes,$conditions);
			$startbeitrag['userdaten'] = User::holeForenDaten($startbeitrag['user_id']);
			return $startbeitrag;
		} else {
			return false;
		}
	}
	
	public static function holeBeitrag($forum_id,$thread_id,$post_id) {
		if($forum_id != 0 && $thread_id != 0 && $post_id != 0) {
	
			$attributes['post_id'] 			= $post_id;
			$attributes['thread_id'] 		= $thread_id;
			$attributes['delete_flag'] 		= 0;
	
			$startbeitrag = ForumPost::model(null,$forum_id)->findByAttributes($attributes);
			$startbeitrag['userdaten'] = User::holeForenDaten($startbeitrag['user_id']);
			return $startbeitrag;
		} else {
			return false;
		}
	
	}
	
	public static function getHtmlLinkStatic($data, $anzeige = false, $seite='post',$htmlOptions = array()) {
		if(!isset($data['forum_id'],$data['thread_id'],$data['post_id'])) {
			return false;
		} else {
			if(!isset($htmlOptions['title'])) {
				$htmlOptions = array_merge($htmlOptions,array('title' => $anzeige));
			}
			return CHtml::link($anzeige,array('forum/'.$seite, 'id' => $data['forum_id'], 'thread_id' => $data['thread_id'], 'post_id' => $data['post_id'] , 'seo' => GFunctions::normalisiereString($data['thread_titel'])),$htmlOptions);
		}
	}

	public static function getLinkStatic($data,$view='post') {
		return Yii::app()->createUrl('forum/'.$view, array('id' => $data['forum_id'], 'thread_id' => $data['thread_id'], 'post_id' => $data['post_id'] , 'seo' => GFunctions::normalisiereString($data['thread_titel'])));
	}	
	
	public static function holeThreadBeitraege($forum_id,$thread_id,$jumpToPage=false) {
		$csort = new CSort();
	
		$csort->defaultOrder = 'datum_erstellt ASC';
		/*
			$csort->attributes = array(
						
					'erstellt' 	=> array(
							'asc'	=> 'datum_erstellt ASC',
							'desc'	=> 'datum_erstellt DESC',
					),
					'thread' 	=> array(
							'asc'	=> 'thread_title DESC',
							'desc'	=> 'thread_title ASC',
					),
					'antwort' 	=> array(
							'asc'	=> 'datum_antwort ASC',
							'desc'	=> 'datum_antwort DESC',
					),
					'posts' 	=> array(
							'asc'	=> 'count_replies DESC',
							'desc'	=> 'count_replies ASC',
					),
			);*/
	
	
		$count=Yii::app()->db->cache(60)->createCommand("SELECT COUNT(*) FROM forum_".$forum_id."_post WHERE thread_id = ".$thread_id." AND delete_flag = 0 AND startbeitrag_flag = 0")->queryScalar();
	
		if($jumpToPage !== false) {
			// zero-based!
			$jumpToPage = $jumpToPage - 1;
				
			$pagination = array('pageSize'=>10,'currentPage'=>$jumpToPage);
		} else {
			$pagination = array('pageSize'=>10);
		}
	
		$sql = "SELECT * FROM forum_".$forum_id."_post WHERE thread_id = ".$thread_id." AND delete_flag = 0 AND startbeitrag_flag = 0";
	
		$dataProvider=new CSqlDataProvider($sql, array(
				'keyField'			=> 'post_id',
				'totalItemCount' 	=> $count,
				'sort'				=> $csort,
				'pagination'		=> $pagination,
		));
	
		$i = 0;
	
		$newData = array();
	
		foreach($dataProvider->getData() as $k) {
			$newData[$i] = $k;
			$newData[$i]['userdaten'] = User::holeForenDaten($k['user_id']);
			$i+=1;
		}
	
		$dataProvider->setData($newData);
	
		return $dataProvider;
	}
	
	public static function holeLetzteBeitraege($forum_id,$thread_id,$post_id,$datum_erstellt) {
		$csort = new CSort();
	
		$csort->defaultOrder = 'datum_erstellt DESC';
	
		//$count=Yii::app()->db->cache(60)->createCommand("SELECT COUNT(*) FROM tm_".$forum_id."_posts WHERE thread_id = ".$thread_id." AND delete_flag = 0 AND startbeitrag_flag = 0")->queryScalar();
	
		$count = 5;
	
		$sql = "SELECT * FROM forum_".$forum_id."_post WHERE thread_id = ".$thread_id." AND post_id <= ".$post_id." AND datum_erstellt <= '".$datum_erstellt."' AND delete_flag = 0";
	
		$dataProvider=new CSqlDataProvider($sql, array(
				'keyField'			=> 'post_id',
				'totalItemCount' 	=> $count,
				'sort'				=> $csort,
				'pagination'=>array(
						'pageSize'	=> 5,
				),
		));
	
		$i = 0;
	
		$newData = array();
	
		foreach($dataProvider->getData() as $k) {
			$newData[$i] = $k;
			$newData[$i]['userdaten'] = User::holeForenDaten($k['user_id']);
			$i+=1;
		}
	
		$dataProvider->setData($newData);
	
		return $dataProvider;
	}
	
	/*
	 * Nummer des Beitrages innerhalb des Threads wiedergeben
	 */
	
	public function getNummer() {
		return ForumPost::model(null,$this->forum_id)->count( 'thread_id = '.$this->thread_id.' AND post_id < '.$this->post_id);
	}
	
	public static function getAnzahlSeiten(&$postObj) {
	
		if(!isset($postObj->post_id)) {
			return false;
		}
	
		$anzahlPosts = Yii::app()->db->cache(60)->createCommand("SELECT COUNT(*) FROM forum_".$postObj->forum_id."_post WHERE thread_id = ".$postObj->thread_id." AND datum_erstellt <= '".$postObj->datum_erstellt."' AND delete_flag = 0 AND startbeitrag_flag = 0 ORDER BY datum_erstellt DESC")->queryScalar();

		#GFunctions::pre($anzahlPosts);
		#GFunctions::pre(Yii::app()->params['page_size']);
		
		if($anzahlPosts < Yii::app()->params['page_size_beitrag']) {
			$seite = 1;
		} else {
			$seite = ceil($anzahlPosts/Yii::app()->params['page_size_beitrag']);
		}
	
		return $seite;
	}
	
	
}
