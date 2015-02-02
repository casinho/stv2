<?php

/**
 * This is the model class for table "forum_1_thread".
 *
 * The followings are the available columns in table 'forum_1_thread':
 * @property string $thread_id
 * @property string $thread_titel
 * @property string $user_id
 * @property string $user_nick
 * @property string $user_ip
 * @property integer $closed_flag
 * @property integer $sticky_flag
 * @property integer $poll_flag
 * @property string $moved_forum_id
 * @property string $moved_thread_id
 * @property string $edit_user_id
 * @property string $edit_user_nick
 * @property string $edit_user_ip
 * @property string $datum_erstellt
 * @property string $datum_antwort
 * @property string $post_id
 * @property string $post_user_id
 * @property string $post_user_nick
 * @property string $sprache
 * @property integer $delete_flag
 */
class ForumThread extends CActiveRecord
{

	protected $forum_id = NULL;
	private $_md;
	
	public $id;
	public $page = 1;
	
	private $_tmpOptionen;
	
	public $spiel;
	public $bank;
	public $user2voting;
	public $relevanteThemen;
	public $spielfehlentscheidung;
	public $master_id;
	
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
		return 'forum_'.$this->forum_id.'_thread';
	}	
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('thread_titel, user_id, user_nick, user_ip', 'required'),
			array('closed_flag, sticky_flag, poll_flag, delete_flag', 'numerical', 'integerOnly'=>true),
			array('thread_titel', 'length', 'max'=>255),
			array('user_id, moved_forum_id, moved_thread_id, edit_user_id, post_id, post_user_id', 'length', 'max'=>10),
			array('user_nick, user_ip, edit_user_nick, edit_user_ip, post_user_nick', 'length', 'max'=>100),
			array('sprache', 'length', 'max'=>4),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('thread_id, thread_titel, user_id, user_nick, user_ip, closed_flag, sticky_flag, poll_flag, moved_forum_id, moved_thread_id, edit_user_id, edit_user_nick, edit_user_ip, datum_erstellt, datum_antwort, post_id, post_user_id, post_user_nick, sprache, delete_flag', 'safe', 'on'=>'search'),
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
			'thread_id' => 'Thread',
			'thread_titel' => 'Thread Titel',
			'user_id' => 'User',
			'user_nick' => 'User Nick',
			'user_ip' => 'User Ip',
			'closed_flag' => 'Closed Flag',
			'sticky_flag' => 'Sticky Flag',
			'poll_flag' => 'Poll Flag',
			'moved_forum_id' => 'Moved Forum',
			'moved_thread_id' => 'Moved Thread',
			'edit_user_id' => 'Edit User',
			'edit_user_nick' => 'Edit User Nick',
			'edit_user_ip' => 'Edit User Ip',
			'datum_erstellt' => 'Datum Erstellt',
			'datum_antwort' => 'Datum Antwort',
			'post_id' => 'Post',
			'post_user_id' => 'Post User',
			'post_user_nick' => 'Post User Nick',
			'sprache' => 'Sprache',
			'delete_flag' => 'Delete Flag',
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

		$criteria->compare('thread_id',$this->thread_id,true);
		$criteria->compare('thread_titel',$this->thread_titel,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('user_nick',$this->user_nick,true);
		$criteria->compare('user_ip',$this->user_ip,true);
		$criteria->compare('closed_flag',$this->closed_flag);
		$criteria->compare('sticky_flag',$this->sticky_flag);
		$criteria->compare('poll_flag',$this->poll_flag);
		$criteria->compare('moved_forum_id',$this->moved_forum_id,true);
		$criteria->compare('moved_thread_id',$this->moved_thread_id,true);
		$criteria->compare('edit_user_id',$this->edit_user_id,true);
		$criteria->compare('edit_user_nick',$this->edit_user_nick,true);
		$criteria->compare('edit_user_ip',$this->edit_user_ip,true);
		$criteria->compare('datum_erstellt',$this->datum_erstellt,true);
		$criteria->compare('datum_antwort',$this->datum_antwort,true);
		$criteria->compare('post_id',$this->post_id,true);
		$criteria->compare('post_user_id',$this->post_user_id,true);
		$criteria->compare('post_user_nick',$this->post_user_nick,true);
		$criteria->compare('sprache',$this->sprache,true);
		$criteria->compare('delete_flag',$this->delete_flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function holeThreadUebersicht($forum_id=0,$foren_art=false,$language='') {
	
		/*
			$sql = "SELECT * FROM tm_".$board_id."_threads";
		$threads = Yii::app()->db->createCommand($sql)->queryAll();
		*/
		$csort = new CSort();
	
		$csort->defaultOrder = 'datum_antwort DESC';

		$csort->attributes = array(
					
				'erstellt' 	=> array(
						'asc'	=> 'datum_erstellt ASC',
						'desc'	=> 'datum_erstellt DESC',
				),
				'thread' 	=> array(
						'asc'	=> 'thread_titel DESC',
						'desc'	=> 'thread_titel ASC',
				),
				'antwort' 	=> array(
						'asc'	=> 'datum_antwort ASC',
						'desc'	=> 'datum_antwort DESC',
				),
				'posts' 	=> array(
						'asc'	=> 'anz_posts DESC',
						'desc'	=> 'anz_posts ASC',
				),
		);
	
	
		$newData = array();
	
		$count=Yii::app()->db->createCommand("SELECT COUNT(*) FROM forum_".$forum_id."_thread WHERE sticky_flag = 0 AND delete_flag = 0")->queryScalar();
		$sql="SELECT * FROM forum_".$forum_id."_thread WHERE sticky_flag = 0 AND delete_flag = 0";
		$dataProvider=new CSqlDataProvider($sql, array(
				'keyField'			=> 'thread_id',
				'totalItemCount' 	=> $count,
				'sort'				=> $csort,
				'pagination'=>array(
						'pageSize'	=> 10,
				),
		));
	
		// $dataProvider->getData() will return a list of arrays.
		return $dataProvider;
	}

	public static function holeWichtigeThreads($forum_id=0,$foren_art=false,$language='') {
	
		/*
		 $sql = "SELECT * FROM tm_".$board_id."_threads";
		$threads = Yii::app()->db->createCommand($sql)->queryAll();
		*/
		$csort = new CSort();
	
		$csort->defaultOrder = 'datum_antwort DESC';
	
		$csort->attributes = array(
					
				'erstellt' 	=> array(
						'asc'	=> 'datum_erstellt ASC',
						'desc'	=> 'datum_erstellt DESC',
				),
				'thread' 	=> array(
						'asc'	=> 'thread_titel DESC',
						'desc'	=> 'thread_titel ASC',
				),
				'antwort' 	=> array(
						'asc'	=> 'datum_antwort ASC',
						'desc'	=> 'datum_antwort DESC',
				),
				'posts' 	=> array(
						'asc'	=> 'anz_posts DESC',
						'desc'	=> 'anz_posts ASC',
				),
		);
	
	
		$newData = array();
	
		$count=Yii::app()->db->createCommand("SELECT COUNT(*) FROM forum_".$forum_id."_thread WHERE sticky_flag = 1 AND delete_flag = 0")->queryScalar();
		$sql="SELECT * FROM forum_".$forum_id."_thread WHERE sticky_flag = 1 AND delete_flag = 0";
		$dataProvider=new CSqlDataProvider($sql, array(
				'keyField'			=> 'thread_id',
				'totalItemCount' 	=> $count,
				'sort'				=> $csort,
				'pagination'=>array(
						'pageSize'	=> 20,
				),
		));
	
		// $dataProvider->getData() will return a list of arrays.
		return $dataProvider;
	}	
	
	
	/*
	 * Wird beim Loeschen / erstellen von Posts ausgefuehrt, um die Anzahl der Antworten
	* der Thread-Tabellen aktuell zu halten. Zudem werden hier die relevanten Daten für die Foren
	* Ansicht gesetzt, um diese nicht manuell holen zu müssen.
	*/
	
	public function aktualisiereLetztenThread($thread_id,$post=null) {
	
		if($post == null) {
			$attributes['thread_id'] 	= $thread_id;
			$attributes['delete_flag'] 	= 0;
			$conditions = array('order' => 'datum_erstellt DESC');
				
			$post = ForumPost::Model(null,$this->forum_id)->findByAttributes($attributes,$conditions);
		}
	
		$qry = "SELECT COUNT(*) FROM forum_".$this->forum_id."_post WHERE thread_id = ".$thread_id." AND delete_flag = 0";
		$anzahl = Yii::app()->db->createCommand($qry)->queryScalar();
	
		$thread = $this->findByPk($thread_id);
	
		$thread->post_id		= $post->post_id;
		$thread->post_user_id	= $post->user_id;
		$thread->post_user_nick	= $post->user_nick;
		$thread->datum_antwort	= $post->datum_erstellt;
		$thread->anz_posts 		= $anzahl;
	
	
		if($thread->validate()) {
			$thread->save(false);
		}
	}
	
	
	protected function afterSave() {
		parent::afterSave();
		
		if($this->isNewRecord) {
			/*
				if($this->forum_id == 25 || $this->forum_id == 26  || $this->forum_id == 27) {
			// an die Mitglieder des KT-Forums eine Mail schicken
			$attributes['user_id'] = Yii::app()->user->id;
	
			$usermails = User::model()->getUserKTMail($attributes, $this->forum_id);
	
			if(!empty($usermails)) {
	
			$forum 	= Forum::model()->findByPk($this->forum_id);
			$thema	= $this;
	
			$message = new YiiMailMessage('Wahretabelle.de: Neues Thema im Forum '.$forum->forum_name);
			$message->view = 'ktThema';
			$message->setBody(array(
					'forum' 	=> $forum,
					'thema' 	=> $thema,
			), 'text/html');
			$message->setTo('info@wahretabelle.de');
			$message->setBcc($usermails);
	
			$message->setFrom(array(Yii::app()->params['systemMail'] => 'WahreTabelle.de'));
			Yii::app()->mail->send($message);
			}
			}
			*/
			$this->isNewRecord = false;
		}
		/*
		 * In diversen Fällen soll das FOrum erst später aktualisiert werden
		* siehe ForumController: actionThreadbearbeiten()
		*/
		if($this->scenario != 'updateLater') {
			Forum::Model()->aktualisiereForum($this->forum_id,$this);
		}
		//$this->setAnzahlSeiten($this->getPrimaryKey());
	}
	
	
	/*
	 * Derzeit keine Verwendung
	*/
	private function setAnzahlSeiten($thread_id = 0) {
		$attributes['thread_id']  = $thread_id;
	
		$anzahl	= ForumThread::Model(null,$this->forum_id)->countByAttributes($attributes);
		GFunctions::pre($anzahl);
		if($anzahl < Yii::app()->params['page_size_thread']) {
			$this->page = 1;
		} else {
			$this->page = ceil($anzahl/Yii::app()->params['page_size_thread']);
		}
	}
	
	public function holeAnzahlSeiten() {
		if($this->anz_posts == 0) {
			$this->anz_posts = 1;
		}
		if($this->anz_posts < Yii::app()->params['page_size_thread']) {
			$this->page = 1;
		} else {
			$this->page = ceil($this->anz_posts/Yii::app()->params['page_size_thread']);
		}
		return $this->page;
	}
	
	public static function holeAnzahlSeitenStatic($posts = 0) {
		if($posts < Yii::app()->params['page_size_thread']) {
			$page = 1;
		} else {
			$page = ceil($posts/Yii::app()->params['page_size_thread']);
		}
		return $page;
	}	
	
	public function setThreadOffline($thread_id,$update = true) {
	
		$this->updateByPk($thread_id, array(
				'delete_flag' => 1
		));
	
		ForumPost::model(null,$this->forum_id)->updateAll(array('delete_flag'=>1),'thread_id = '.$thread_id.'');
	
		if($update === true) {
			Forum::Model()->aktualisiereForum($this->forum_id);
		}
		return true;
	}
	
	public function convertToBooleans() {
		$attributes = array('is_sticky', 'is_closed', 'is_archiv', 'postcounter');
	
		foreach ($attributes as $attr) {
			if(in_array($attr,$attributes)) {
				$this->$attr = ($this->$attr == 'x') ? true : false;
			}
		}
	
	}
	
	public function convertToStrings() {
		$attributes = array('is_sticky', 'is_closed', 'is_archiv', 'postcounter');
		foreach($_POST['ForumThread'] as $attr => $value) {
			if(in_array($attr,$attributes)) {
				$this->$attr = ($value == '1') ? 'x' : '';
			}
		}
	}
	
	public function convertAttributeToBoolean($attr,$value='x') {
		$this->$attr = ($this->$attr == $value) ? true : false;
	}	
	
	public static function getHtmlLinkStatic($data = array(),$htmlOptions = array(),$view = false) {
		if(!isset($data['thread_titel'])) {
			$anzeige = 'thread-titel';
		} else {
			$anzeige = $data['thread_titel'];
		}
	
		$linkText = ($view == false) ? $data['thread_titel'] : $view;
	
		$htmlOptions = array_merge($htmlOptions,array('title' => $anzeige));
	
		//TMFunctions::pre($data);
	
		if(isset($data['post_id'])) {
			$link = CHtml::link($linkText,Yii::app()->createUrl('forum/thread', array('id' => $data['forum_id'], 'thread_id' => $data['thread_id'], 'seo' => GFunctions::normalisiereString($anzeige), 'post_id' => $data['post_id'], '#' => 'p'.$data['post_id'])) ,$htmlOptions);
		} else {
			$link = CHtml::link($linkText,Yii::app()->createUrl('forum/thread', array('id' => $data['forum_id'], 'thread_id' => $data['thread_id'], 'seo' => GFunctions::normalisiereString($anzeige))),$htmlOptions);
		}
		return $link;
	}
	
	public function getLink($view='thread') {
		return Yii::app()->createUrl('forum/'.$view, array('id' => $this->forum_id, 'thread_id' => $this->thread_id, 'seo' => GFunctions::normalisiereString($this->thread_titel)));
	}
	
	public static function getThreadSeiten($threadObj,$forumObj,$htmlOptions = array()) {
	
		$anzahl = $threadObj['anz_posts'];
	
		if($anzahl < Yii::app()->params['page_size']) {
			$limit = 1;
		} else {
			$limit = ceil($anzahl/Yii::app()->params['page_size']);
		}
	
		$output = '';
		if($limit > 1) {
			if($limit <= 8) {
				for($i=1;$i<=$limit;$i++) {
					$output.= CHtml::link($i,Yii::app()->createUrl('forum/thread', array('id' => $forumObj['forum_id'], 'thread_id' => $threadObj['thread_id'], 'page' => $i, 'seo' => GFunctions::normalisiereString($threadObj['thread_titel']))),$htmlOptions).'&nbsp;';
				}
			} else {
				$last = $limit-4;
	
				for($i=1;$i<=$limit;$i++) {
					if($i == 1) {
						$output.= CHtml::link($i,Yii::app()->createUrl('forum/thread', array('id' => $forumObj['forum_id'], 'thread_id' => $threadObj['thread_id'], 'page' => $i, 'seo' => GFunctions::normalisiereString($threadObj['thread_titel']))),$htmlOptions).'&nbsp;...&nbsp;';
					}
					if($i >= $last) {
						$output.= CHtml::link($i,Yii::app()->createUrl('forum/thread', array('id' => $forumObj['forum_id'], 'thread_id' => $threadObj['thread_id'], 'page' => $i, 'seo' => GFunctions::normalisiereString($threadObj['thread_titel']))),$htmlOptions).'&nbsp;';
					}
				}
			}
		}
	
		return $output;
	}
	
	public static function getThreadIcon($thread,$forum=array(),$furtherClasses = '',$subStyle = '') {
	
		$basisclass = 'icon-file-text '.$furtherClasses;
		$basisstyle = '';
		

	
		//GFunctions::pre($thread);
	
		$style = 'position:absolute;margin-top:13px;margin-left:-11px;color:#444;';
		$subicon = TbHtml::tag('i',array('class'=>'s11','style'=>$style.$subStyle),'&nbsp;');		
		
		if($thread['poll_flag']>0) {
			$basisclass = 'icon-file '.$furtherClasses;
			$basisstyle = '';
			
			$style = 'position:absolute;margin-top:13px;margin-left:-11px;color:#444;';
			$subicon = TbHtml::tag('i',array('class'=>'icon-question s11','style'=>$style.$subStyle),'&nbsp;');
		}
		if($thread['closed_flag']==1) {
			$basisclass = 'icon-file '.$furtherClasses;
			$basisstyle = '';
			
			$style = 'position:absolute;margin-top:13px;margin-left:-10px;color:#444;';
			$subicon = TbHtml::tag('i',array('class'=>'icon-remove s11','style'=>$style.$subStyle),'&nbsp;');
		}
	
		/*
		if($thread['sticky_flag']==1) {
			#$class = '	';
			#$class.= ' thread-nf';
			if($thread['closed_flag'] == 'x') {
				#$class.='-closed';
			}
		} 
		*/
		if($thread['moved_forum_id'] > 0 && $thread['moved_thread_id'] > 0) {
			$basisclass = 'icon-file '.$furtherClasses;
			$basisstyle = '';
			
			$style = 'position:absolute;margin-top:13px;margin-left:-9px;color:#444;';
			$subicon = TbHtml::tag('i',array('class'=>'icon-mail-forward s11','style'=>$style.$subStyle),'&nbsp;');			
		}
	
		/*
		 * Todo: pruefen, ob thread ungelesen / neu ist
		*
		* $basisclass.= ' orange';
		*
		*/		
	
		
		$output = TbHtml::tag('span',array('class'=>$basisclass,'style'=>'font-size:28px;margin-top:10px;'.$basisstyle),'&nbsp;');
		$output.= $subicon;
		
		//return $output;
		
		return TbHtml::tag('div',array('class'=>'dib fl','style'=>'height:34px;width:25px;display:inline-block;'),$output);
	}

	public static function holeThread($forum_id=0,$thread_id=0) {
	
		if($forum_id != 0 && $thread_id != 0) {
			$thread = ForumThread::model(null,$forum_id)->findByPk($thread_id);
			return $thread;
		} else {
			return false;
		}
	}
	

}
