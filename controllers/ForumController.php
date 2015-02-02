<?php
class ForumController extends Controller {
	
	public $disableICheck;
	
	public $elternforum;
	public $forum;
	public $thread;
	public $post;
	public $land;
	public $mods;
	
	public $userRecht;
	public $modRecht;
	public $adminRecht;
	
	
	
	private $_transaction = null;
	
	
	/*
	 *  hier werden die PostIDs für den Button "Ausgewählte Posts löschen"
	 *  gespeichert
	 */
	public $cookieName;
	
	/*
	 * Cookie für Startbeitrag anzeigen ja / nein
	 */
	public $cookieStartbeitrag;
	
	private $_tld;
	
	public $paten = array();
	public $alarmmeldungen;
	
	
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules() {
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index', 'detail', 'thread', 'letzte_beitraege','post','poll'),
				'users'=>array('*'),
			),
			array('allow',
				'actions' => array('threaderstellen','threadbearbeiten','antworten', 'zitieren', 'alarm', 'bearbeiten', 'loeschen', 'postLoeschen', 'quickOptionen', 'threadLoeschen', 'getFlag', 'getParent', 'getOnlineStatus'),
				'roles' => array('Freigeschaltet')
			),
			array('allow',
				'actions' => array('quickOptionen', 'threadVerschieben','move'),
				'roles' => array('Superadmin','ForumAdmin','ForenModerator')
			),
			array('allow',
				'actions' => array('verwalten','updateForum','updateMods','getForumMods','create','update','addUser','entferneZuweisung','getModeratorStatus','updateModerator','sort'),
				'roles' => array('Superadmin','ForumAdmin')
			),								
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	private function setRechte() {
		if($this->post != null) {
			$this->userRecht = Yii::app()->user->checkAccess('Forum: Eigenen Beitrag bearbeiten',array('startwert'=>$this->post->datum_erstellt,'user_id'=>$this->post->user_id));
		}
		if($this->forum != null) {
			//$this->expertenRecht = Yii::app()->user->checkAccess('Experten');
			$this->modRecht = Yii::app()->user->checkAccess('ForenModerator',array('forum_id'=>$this->forum['forum_id']));
		}
	
		$this->adminRecht = Yii::app()->user->checkAccess('Superadmin');
	}
		
	
	private function isOpen() {
		if(1 == 1) {
			return false;
		} else {
			return true;
		}
	}
	
	protected function beforeAction($action) {
		if(defined('YII_DEBUG') && YII_DEBUG){
			//Yii::app()->assetManager->forceCopy = true;
		}
		Yii::app()->assetManager->forceCopy = true;
		$action = $action->getId();

		$ignoreActions 	= array('index','abo','postLoeschen','entferneZuweisung');
		$writeActions 	= array('antworten','zitieren');
		$modActions		= array('threadbearbeiten','bearbeiten','loeschen','postLoeschen','threadLoeschen','quickOptionen','threadVerschieben','move','updateModerator');
		
		if(!in_array($action,$ignoreActions)) {
	
			$forum_id = Yii::app()->getRequest()->getParam('id');
	
			if(is_numeric($forum_id)) {
	
			  
				$this->forum 	= $this->getForum($forum_id);
				if($this->forum == null) {
					throw new CHttpException('404',Yii::t('forum','forum_existiert_nicht'));
				} else {
					if($this->forum->zugriffs_flag == 1) {
						if(Yii::app()->user->checkAccess('Superadmin')===false) {
							
							$check = User2Forum::model()->findByAttributes(array('forum_id'=>$forum_id,'user_id'=>Yii::app()->user->getId()));
							if($check == null) {
								throw new CHttpException('404',Yii::t('forum','zugriff_verweigert'));
							}
							
						}
					}
				}
				
				$this->mods 	= array();
	
				$this->cookieName   		= 'post_loeschen_'.$this->forum->forum_id;
				$this->cookieStartbeitrag   = 'startbeitrag_'.$this->forum->forum_id;
			  
				$thread_id = Yii::app()->getRequest()->getParam('thread_id');
				
				if(is_numeric($thread_id) && $thread_id > 0) {
					$this->thread 				= ForumThread::holeThread($this->forum['forum_id'],$thread_id);
					
					if($this->thread != null) {
						if($this->thread->delete_flag == 1) {
							throw new CHttpException('404',Yii::t('forum','aktion_kann_nicht_durchgefuehr_werden_deleted'));
						}
						if($this->thread->closed_flag == 1) {
							if(in_array($action, $writeActions)) {
								throw new CHttpException('404',Yii::t('forum','aktion_kann_nicht_durchgefuehr_werden_closed'));
							}
						} 
					} else {
						throw new CHttpException('404',Yii::t('forum','thread_existiert_nicht'));
					}
					
					$this->cookieName   		= $this->cookieName.'_'.$this->thread['thread_id'];
					$this->cookieStartbeitrag   = $this->cookieStartbeitrag.'_'.$this->thread['thread_id'];
				}
	
				$post_id = Yii::app()->getRequest()->getParam('post_id');
		   
				if(is_numeric($post_id) && $post_id > 0) {
					$this->post 	= ForumPost::holeBeitrag($this->forum['forum_id'],$thread_id,$post_id);
				} else {
					if($action == 'threadbearbeiten') {
						$this->post = ForumPost::model(null,$this->forum['forum_id'])->findByAttributes(array('thread_id'=>$thread_id,'startbeitrag_flag'=>1));
					}
				}
		   
				$this->setRechte();
				
				if(in_array($action,$modActions)) {
					if($this->modRecht === false && $this->userRecht === false && $this->adminRecht === false) {
						throw new CHttpException('404',Yii::t('forum','aktion_verweigert'));
					}
				}
	
			} 
		}
		return parent::beforeAction($action);
	}	
	
	
	
	public function getForum($forum_id) {
		$criteria = new CDbCriteria;
		$criteria->condition = "forum_id = :id AND online_flag = :flag";
		$criteria->params = array(':flag' => 1, 'id'=>$forum_id);
		$criteria->order = 'nummer ASC';
			
		$forum = Forum::model()->find($criteria);
		return $forum;		
	}
	
	public function actionCreate()	{
		$model= new Forum;
		$model->setScenario('admin-create');
		
		$mods = array();
		if(isset($_POST['User2ForumMod'])) {
			foreach($_POST['User2ForumMod'] as $k => $v) {
				$u2f = new User2Forum();
				$u2f->haupt_flag = 1;
				$u2f->user_id = $v['user_id'];
				$mods[$v['user_id']] = $u2f;
			}
		}		

		$mods = $this->getModeratoren();

		$user = array();
		if(isset($_POST['User2Forum'])) {
			foreach($_POST['User2Forum'] as $k => $v) {
				$u2c = new User2Forum();
				$u2c->user_id = $v['user_id'];
				$u2c->usernick;
				$user[$v['user_id']] = $u2c;
			}
		}
		
		$oldUser = $this->getForenUser();		
		
		$this->performAjaxValidation($model);
	
		if(isset($_POST['Forum'])) {
	
			$model->forum_titel 	= $_POST['Forum']['forum_titel'];
			$model->forum_icon		= $_POST['Forum']['forum_icon'];
			$model->parent_id 		= $_POST['Forum']['parent_id'];
			$model->beschreibung	= $_POST['Forum']['beschreibung'];
			$model->zugriffs_flag	= $_POST['Forum']['zugriffs_flag'];
			$model->online_flag 	= $_POST['Forum']['online_flag'];
			//$model->nummer 			= $_POST['Forum']['nummer'];
	
			if($model->validate()) {
				$transaction = Yii::app()->db->beginTransaction();
				$model->save(false);
	
				
				if($model->zugriffs_flag == 1) {
					if(isset($_POST['User2Forum'])) {
						foreach($_POST['User2Forum'] as $k => $v) {
							if(!isset($oldUser[$k])) {
								$u2f = new User2Forum();
								$u2f->user_id 		= $v['user_id'];
								$u2f->haupt_flag 	= 0;
								$u2f->forum_id		= $model->forum_id;
								$u2c->usernick;
								$u2f->save();
								$user[$v['user_id']] = $u2f;
							} 
						}
					}
				} 				
				
				

				$transaction->commit();
				Yii::app()->user->setFlash('gespeichert',Yii::t('member','user_erfolgreich_angelegt'));
				$this->redirect(array('update','id'=>$model->forum_id));
				
			} else {
				#$transaction->rollBack();
				//GFunctions::pre($model->getErrors());
			}
		}
		
		$user = array_merge($user,$oldUser);
		
		$user = new CArrayDataProvider($user,array(
					'id' => 'user_id',	
					'pagination'=>array(
								'pageSize'=>10,

							),
				'sort'=>array(
						'attributes'=>array(
								'user_nick', 'user_id', 'haupt_flag'
						),											
					),				
		));
	
		$this->render('create',array(
				'model'=>$model,
				'mods' => $mods,
				'user' => $user,
				'aktion' => Yii::t('forum','forum_erstellen'),
		));
	
	}
	
	
	public function actionSort() 	{
		if (isset($_POST['items']) && is_array($_POST['items'])) {
			$ci = 1; // child
			$pi = 1; // parent
			foreach ($_POST['items'] as $item) {
				$model = Forum::model()->findByPk($item);
				if($model->parent_id == 0) {
					$model->nummer = $pi;
					$model->save();
					$pi+=1;
					$ci = 1; // child
				} else {
					$model->nummer = $ci;
					$model->save();
					$ci+=1;					
				}
			}
		}
	}
	
	
	public function actionUpdate($id)	{

		$model = $this->loadModel($id);
	
		$mods = array();
		if(isset($_POST['User2ForumMod'])) {
			foreach($_POST['User2ForumMod'] as $k => $v) {
				$u2c = new User2Forum();
				$u2c->haupt_flag = 1;
				$u2c->user_id = $v['user_id'];
				$mods[$v['user_id']] = $u2c;
			}
		}
	
		$mods = $this->getModeratoren();
	
		$user = array();
		
		$oldUser = $this->getForenUser($model->forum_id);	
		
		if(isset($_POST['User2Forum'])) {
			
			
			foreach($_POST['User2Forum'] as $k => $v) {
				
				$u2f = new User2Forum();
				$u2f->user_id = $v['user_id'];
				$u2f->usernick;
				$user[$v['user_id']] = $u2f;
			}
		}
		
		

		if(isset($_POST['gruppen'])) {
			foreach($_POST['gruppen'] as $k => $v) {
				if(is_numeric($v)) {
					$tmpUser = User2Squad::model()->findAllByAttributes(array('squad_id'=>$v));
					foreach($tmpUser as $kk =>$vv) {
						if(!isset($user[$vv['user_id']])) {
							$u2f = new User2Forum();
							$u2f->user_id 		= $vv['user_id'];
							$u2f->haupt_flag 	= 0;
							$u2f->forum_id		= $model->forum_id;
							$u2f->usernick;
							$user[$vv['user_id']] = $u2f;
						}
					}
				} else {
					
					$sql = "SELECT * FROM AuthAssignment WHERE itemname = '.$v.'";
					
					$tmpUser = Yii::app()->db->createCommand($sql)->queryAll();
					
					foreach($tmpUser as $kk =>$vv) {
						if(!isset($user[$vv['user_id']])) {
							$u2f = new User2Forum();
							$u2f->user_id 		= $vv['user_id'];
							$u2f->haupt_flag 	= 0;
							$u2f->forum_id		= $model->forum_id;
							$u2f->usernick;
							$user[$vv['user_id']] = $u2f;
						}
					}
				}
			}
		}		
	
		
	
		$this->performAjaxValidation($model);
	
		if(isset($_POST['Forum'])) {
	
			$model->forum_titel 	= $_POST['Forum']['forum_titel'];
			$model->forum_icon		= $_POST['Forum']['forum_icon'];
			$model->parent_id 		= $_POST['Forum']['parent_id'];
			$model->beschreibung	= $_POST['Forum']['beschreibung'];
			$model->zugriffs_flag	= $_POST['Forum']['zugriffs_flag'];
			$model->online_flag 	= $_POST['Forum']['online_flag'];
			//$model->nummer 			= $_POST['Forum']['nummer'];
	
			if($model->validate()) {
				$transaction = Yii::app()->db->beginTransaction();
				$model->save(false);
	
				if($model->zugriffs_flag == 1) {
					
					foreach($user as $k => $v) {
						if(!isset($oldUser[$k])) {
							$u2f = new User2Forum();
							$u2f->user_id 		= $k;
							$u2f->haupt_flag 	= 0;
							$u2f->forum_id		= $model->forum_id;
							$u2f->save(false);
						}						
					}
					
				} else {
					$attributes['forum_id'] = $model->forum_id;
					User2Forum::model()->deleteAllByAttributes($attributes);
				}
	
	
				$transaction->commit();
				Yii::app()->user->setFlash('gespeichert',Yii::t('member','user_erfolgreich_angelegt'));
				#$this->redirect(array('update','id'=>$model->forum_id));
	
			} else {
				#$transaction->rollBack();
				//GFunctions::pre($model->getErrors());
			}
		}

		$userOutput = array();
		
		foreach($user as $k => $v) {
			if(!isset($oldUser[$v['user_id']])) {
				$userOutput[$v['user_id']] = $v; 
			} else {
				$userOutput[$v['user_id']] = $v;
				unset($oldUser[$v['user_id']]);
			}
		}
		
		foreach($oldUser as $k => $v) {
			$userOutput[$v['user_id']] = $v;
		}	

		$csort = new CSort();
		$csort->attributes=array(
				'user_id' 		=> array( "asc"=>'user_id ASC', "desc" => 'user_id DESC'),
				'user_nick' 	=> array( "asc"=>'user_nick ASC', "desc" => 'user_nick DESC'),
				'haupt_flag' 	=> array( "asc"=>'haupt_flag ASC', "desc" => 'haupt_flag DESC'),
		);
		$csort->defaultOrder = 'user_nick ASC';
		
		
		$user = new CArrayDataProvider($userOutput,array(
				'id' 		=> 'user_id',
				'keyField'	=> 'user_id',
				'pagination'=>array(
						'pageSize'=>20,
				),
				'sort'=> $csort,		
		));		
	
		$this->render('create',array(
				'model'=>$model,
				'mods' => $mods,
				'user' => $user,
				'aktion' => Yii::t('forum','forum_bearbeiten'),
		));
	
	}	
	
	public function actionIndex() {
		
		$id = $this->id.$this->action->id;
		
		//$foren = Yii::app()->cache->get($id);
		$foren = false;
		if($foren===false) {
		
			$id = Yii::app()->request->getParam('id');
		
			if(is_numeric($id) && $id > 0) {
				$forum_id 	 = Yii::app()->request->getParam('id');
			} else {
				$forum_id	 = '';
			}
		
		
			$criteria = new CDbCriteria;
			$criteria->condition = "t.online_flag = :flag AND t.parent_id = :parent_id";
			$criteria->params = array(':flag' => 1, ':parent_id' => 0);
			$criteria->order = 'nummer ASC';
		
			//$foren = Forum::model()->findAll($criteria);
			$erlaubteForen = array();
			if(!Yii::app()->user->isGuest && Yii::app()->user->checkAccess('ForumAdmin') === false) {
					
				$tmpForen = Yii::app()->db->createCommand("SELECT forum_id FROM user2forum WHERE user_id = ".Yii::app()->user->getId()."")->queryAll();
					
				foreach($tmpForen as $k => $v) {
					$erlaubteForen[] = $v['forum_id'];
				}
			}
			
			
			$eltern_sql = "SELECT forum_id AS id, forum_id,forum_titel,nummer,zugriffs_flag FROM forum WHERE online_flag = 1 AND parent_id = 0";
			
			if(!empty($forum_id)) {
				$eltern_sql.= " AND forum_id = ".$forum_id;
			} else {
				$eltern_sql.= " ORDER BY nummer ASC";
			}
			
			$foren = Yii::app()->db->createCommand($eltern_sql)->queryAll();			
			
			foreach($foren as $key => $value) {
			
				$kind_sql = "SELECT forum_id AS id, forum_id,forum_titel,nummer,zugriffs_flag,beschreibung,thread_titel,thread_id,thread_user_nick,thread_user_id,datum_antwort,anz_threads,anz_posts FROM forum WHERE online_flag = 1 AND parent_id = ".$value['forum_id']."";
					
				$kinder = Yii::app()->db->createCommand($kind_sql)->queryAll();
					
				$gefilterteKinder = Forum::checkForenZugriff($kinder,$erlaubteForen);
					
				$foren[$key]['kinder'] = $gefilterteKinder;
			
				/*		
				foreach($foren[$key]['kinder'] as $k => $v) {
			
					$boards[$key]['childs'][$k]['board_icon'] 	= ForumZuweisung::getBoardIcon($v,'small');
			
					if(isset($boardAlarme[$v['board_id']])) {
						// ich muss hier die MasterId nehmen!
						$boards[$key]['childs'][$k]['alarme'] = $boardAlarme[$v['master_id']];
						$gesamtAlarme+=$boardAlarme[$v['master_id']];
					} else {
						$boards[$key]['childs'][$k]['alarme'] = 0;
					}
				}
				*/
			}
				
			$dataProvider=new CArrayDataProvider($foren, array(
					'id'			=> 'forum_id',
					'pagination'	=> false,
			));
			
			//Yii::app()->cache->set($id,$foren,30);
		}
		$this->render('foren',array(
				'foren' => $dataProvider,
			)
		);		
		
	}
	
	public function actionEntferneZuweisung() {
		
		$auto_id = Yii::app()->getRequest()->getParam('id');
		
		User2Forum::model()->deleteByPk($auto_id);
		
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) {
			//$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('verwalten'));
		
			$data['success'] = true;
		
			header('Content-Type: application/json');
			echo CJSON::encode($data);
			Yii::app()->end();
		}
		
	}
	
	public function actionUpdateForum() {
		if(!empty($_POST)) {
			Forum::model()->updateByPk($_POST['pk'], array($_POST['name'] => $_POST['value']));
		}
	}	

	public function actionGetForumMods($id) {
		$model = Forum::model()->findByPk($id);
		$mods = $model->getModeratoren();
		$j['out'] = array();
		$j['tag'] = array();
		$j['ids'] = array();
		if($mods != null) {
			foreach($mods as $k => $v) {
				$j['ids'][] = $v['id'];
				$j['tag'][] = $v['text'];
				$j['out'][] = array('id'=>$v['id'],'text'=>$v['text']);
			}
		}
		
		if(empty($j['tag'])) {
			$j['tag'][] = 'Click to edit';
		}
		echo CJSON::encode($j);
		Yii::app()->end();		
	}
	
	
	public function actionUpdateMods() {
		if(!empty($_POST)) {
		
			
			$oldModData = Forum::model()->findByPk($_POST['pk'])->getModeratoren();

			
			$oldMods = array();
			foreach($oldModData as $k => $v) {
				$oldMods[] = $v['id'];
			}
			
			if(isset($_POST['value'])) {
				$newMods = $_POST['value'];
			} else {
				$newMods = array();
			}

			$attributes = array();

			$attributes['forum_id'] = $_POST['pk'];

			if(!empty($oldMods)) {
					
				if(empty($newMods)) {
					User2Forum::model()->deleteAllByAttributes($attributes);
				} else {

					foreach($oldMods as $k => $v) {
						if(!in_array($v,$newMods)) {
							$attributes['user_id'] = $v;
							User2Forum::model()->deleteAllByAttributes($attributes);
							unset($oldMods[$k]);
						}
					}

					foreach($newMods as $k => $v) {
						if(!in_array($v,$oldMods)) {
							$attributes['user_id'] 		= $v;
							$attributes['haupt_flag']	= 1;
							$model = new User2Forum();
							$model->attributes = $attributes;
							$model->save(false);
						}
					}

				}
			} else {
				foreach($newMods as $k => $v) {
					$attributes['user_id'] 		= $v;
					$attributes['haupt_flag']	= 1;
					$model = new User2Forum();
					$model->attributes = $attributes;
					$model->save(false);
				}
			}

			//User::model()->updateByPk($_POST['pk'], array($_POST['name'] => $_POST['value']));
		}
	}
	
	
	public function actionDetail() {
			
		$sticky 	= ForumThread::holeWichtigeThreads($this->forum['forum_id']);
		$threads	= ForumThread::holeThreadUebersicht($this->forum['forum_id']);
			
		$this->render('detail',array(
				'sticky' 	=> $sticky,
				'threads' 	=> $threads,
		));
	}	
	
	public function actionVerwalten() {
		
		$foren = Forum::model()->findAll(array('order' => 'nummer ASC, parent_id ASC'));
		
		$this->render('forumVerwalten',array(
				'foren' 	=> $foren,
		));
		
		
	}
	
	public function actionGetFlag() {
		$output = Forum::getFlags();
		echo json_encode($output);
	}

	public function actionGetOnlineStatus() {
		$output = Forum::getStatus();
		echo json_encode($output);
	}	

	public function actionGetModeratorStatus() {
		$output = Forum::getModeratorStatus();
		echo json_encode($output);
	}
	
	
	public function actionUpdateModerator() {
		$model = User2Forum::model()->findByPk($_POST['pk']);
		if($model != null) {
			
			$item = 'ForenModerator';
			
			$sql = "SELECT * FROM AuthAssignment WHERE userid = ".$model->user_id." AND itemname = '".$item."'";
			$res = Yii::app()->db->createCommand($sql)->queryRow();
			
			if(empty($res)) {
				Yii::app()->authManager->assign('ForenModerator',$model->user_id);
			}
			$model->haupt_flag = $_POST['value'];
			$model->save();
		} 
	}
	
	public function actionGetParent() {
		$output = $this->getParents();
		echo json_encode($output);
	}
	
	public function getModeratoren() {
		
		$qry = "SELECT u.user_id,u.user_nick FROM AuthAssignment AS a INNER JOIN user AS u ON u.user_id = a.userid WHERE a.itemname IN ('Superadmin','ForenModerator','ForumAdmin') GROUP BY u.user_id ORDER BY u.user_nick ASC ";
		$res = Yii::app()->db->createCommand($qry)->queryAll();
		
		$output = array();
		foreach($res as $k => $v) {
			$output[] = array('id' => $v['user_id'], 'text' => $v['user_nick']);
		}
		
		return $output;
	}	

	public function getForenUser($forum_id = 0) {
	
		$criteria = new CDbCriteria();
		$criteria->condition = 'forum_id = :forum_id';
		$criteria->params = array(':forum_id'=>$forum_id);		
		
		$user = User2Forum::model()->findAll($criteria);

		$output = array();
		foreach($user as $k => $v) {
			$v->usernick;
			$output[$v->user_id] = $v;
		}
		
		return $output;
	}
	
	
	public function getParents() {
		$criteria = new CDbCriteria();
		$criteria->condition = 'parent_id =:parent_id';
		$criteria->params = array(':parent_id'=>0);
		$criteria->order = 'nummer ASC';
	
		$parents = Forum::model()->findAll($criteria);
	
		$output = array();
		$output[0] = array('value'=>0,'text'=>'----');
		foreach($parents as $k => $v) {
			$output[] = array('value' => $v->forum_id, 'text' => $v->forum_titel);
		}
		return $output;
	}	
	
	public function actionAddUser($myInput) {
		$User2Forum = new User2Forum();
		$this->renderPartial('_addUser',array('User2Forum'=>$User2Forum,'myInput'=> $myInput));
	}
	
	
	public function actionThreaderstellen() {
	
		$thread = $this->speichereThread();
		$post 	= $this->speicherePost($thread);
	
		$optionen = array();
	
		$this->render('threaderstellen',array(
				'thread' 	=> $thread,
				'reply'		=> $post,
				'aktion'	=> Yii::t('forum','neues_thema'),
				'optionen'	=> $optionen,
				'optionenDb'=> array(),
		));
	}
	
	
	
	
	protected function gridForumTitel($data,$row) {
		return $this->renderPartial('_forumTitelZelle',array('data'=>$data),true);
	}
	
	protected function gridForumThread($data,$row) {
		return $this->renderPartial('_forumThreadZelle',array('data'=>$data),true);
	}
	
	protected function gridThreadIcon($data,$row) {
		return $this->renderPartial('_threadTitelIcon',array('data'=>$data,'forum'=>$this->forum),true);
	}
	
	protected function gridThreadTitel($data,$row) {
		//TMFunctions::pre($data);
		return $this->renderPartial('_threadTitelZelle',array('data'=>$data,'forum'=>$this->forum,'alarmmeldungen'=>$this->alarmmeldungen),true);
	}
	
	protected function gridThreadErstellt($data,$row) {
		if($data['moved_forum_id']==0 && $data['moved_thread_id']==0) {
			return $this->renderPartial('_threadErstelltZelle',array('data'=>$data,'forum'=>$this->forum),true);
		}
	}
	
	protected function gridThreadAntwort($data,$row) {
		if($data['moved_forum_id']==0 && $data['moved_thread_id']==0) {
			return $this->renderPartial('_threadAntwortZelle',array('data'=>$data,'forum'=>$this->forum),true);
		}
	}	
	
	public function actionThread() {
	
		$letzterBeitrag = Yii::app()->getRequest()->getParam('post_id');
		$pageGesetzt	= Yii::app()->getRequest()->getParam('page');
	
	
	
		if(!empty($letzterBeitrag) && empty($pageGesetzt)) {
			$seite = ForumPost::getAnzahlSeiten($this->post);
		} else {
			$seite = false;
		}
	
		$startbeitrag = ForumPost::holeStartBeitrag($this->forum['forum_id'],$this->thread['thread_id']);
		$beitraege    = ForumPost::holeThreadBeitraege($this->forum['forum_id'],$this->thread['thread_id'],$seite);
	
		$this->render('thread',array(
				'startbeitrag' 	=> $startbeitrag,
				'beitraege' 	=> $beitraege,
		));
	}
	
	
	public function actionThreadbearbeiten() {
		
		$thread = $this->speichereThread();
		$post 	= $this->speicherePost($thread);
	
		$optionenDb = Yii::app()->db->createCommand("SELECT * FROM polls WHERE forum_id = ".$this->forum['forum_id']." AND thread_id = ".$this->thread['thread_id'])->queryAll();
		
		//GFunctions::pre($optionen);
		
		
		$this->render('threaderstellen',array(
				'thread' 	=> $thread,
				'reply'		=> $post,
				'aktion'	=> Yii::t('forum','neues_thema'),
				'optionenDb'=> $optionenDb,
				'optionen'	=> array(),
				'edit'		=> true,
		));
	}	
	
	private function speichereThread() {
		
		if(Yii::app()->controller->action->id=='threaderstellen') {
			$thread	= new ForumThread('insert',$this->forum['forum_id']);
			if(!isset($_POST['ForumThread'])) {
				$thread->poll_flag 		= 0;
			}
		} else {
			$thread	= $this->thread;
		}
		
		/* Da ich bei diesem Projekt auf Flags setze, brauche ich diese Methode vorerst nicht.
		 * 
		 * $thread->convertToBooleans(); 
		 */
		
		//GFunctions::pre($_POST);
		
		
		if(isset($_POST['ForumThread'])) {

			$thread->thread_titel 		= $_POST['ForumPost']['titel'];
			$thread->user_id 			= Yii::app()->user->getId();
			$thread->user_nick 			= Yii::app()->user->name;
			$thread->user_ip			= Yii::app()->request->getUserHostAddress();
			

			$thread->poll_flag			= $_POST['ForumThread']['poll_flag'];
			
			if(isset($_POST['stimmen']) && ((int)$_POST['stimmen']>1)) {
				$thread->poll_flag		= $_POST['stimmen'];
			}
			
		
			$thread->datum_erstellt		= new CDbExpression('NOW()');
			$thread->datum_antwort		= new CDbExpression('NOW()');
			
			$thread->sprache			= Yii::app()->getLanguage();
			
			if($thread->validate()) {

				$this->_transaction = Yii::app()->db->beginTransaction();
				
				/*
				 * scenario muss hier gesetzt werden, da ansonsten der Post zuvor gespeichert wird
				 * und die aftersave-Medthode in ForumPost bereits alle Aktualisierungen (fehlerhaft) 
				 * durchführen würde
				 */
				$thread->scenario = 'updateLater';
				$thread->save(false);
				$reply = $this->speicherePost($thread);
				if(!$reply->validate()) {
					$this->_transaction->rollBack();
				} else {
					$reply->save(false);
					
					$this->speichereOptionen($thread);
					
					$this->_transaction->commit();

					$sprache 	= Yii::app()->language;
					$titel 		= GFunctions::normalisiereString($thread->thread_titel);
					$forum_id	= $this->forum['forum_id'];
					$thread_id	= $thread->thread_id;
					$seite 		= $reply->page;
					$post_id	= $reply->post_id;
					
					$url = Yii::app()->createUrl('forum/thread',array('id' => $forum_id, 'thread_id' => $thread_id, 'page' => $seite, 'seo' => $titel, '#'=>'startbeitrag'));
					$this->redirect($url);					
				}					
			} else {
				//GFunctions::pre($thread->getErrors());
			}
			
		}
		//$thread->convertToBooleans();
		return $thread;
	}
	
	public function speichereOptionen($threadObj) {
		if($threadObj->poll_flag == 1 && isset($_POST['optionen']) && !empty($_POST['optionen'])) {
			foreach($_POST['optionen'] as $k => $v) {
				if(trim($v) != '') {
					$pollOption = new Polls();
					$pollOption->thread_id 	= $threadObj->thread_id;
					$pollOption->forum_id 	= $this->forum['forum_id'];
					$pollOption->option		= $v;
					$pollOption->sort		= $k+1;
					$pollOption->save(false);
					unset($pollOption);
				}
			}
		}
	}
	
	
	// verwertet die QuickOptions aus der Forenansicht (delete | sticky | unsticky...)
	public function actionQuickOptionen() {

		if(isset($_POST['aktion'])) {

			switch($_POST['aktion']) {
				case 'sticky':
					$this->thread->sticky_flag = 1;
					$message = Yii::t('forum','thread_erfolgreich_festgepinnt');
					break;
				case 'unlock':
					$this->thread->sticky_flag = 0;
					$message = Yii::t('forum','thread_erfolgreich_wieder_geloest');
					break;
				case 'close':
					$this->thread->closed_flag = 1;
					$message = Yii::t('forum','thread_erfolgreich_geschlossen');
					break;
				case 'open':
					$this->thread->closed_flag = 0;
					$message = Yii::t('forum','thread_erfolgreich_geoeffnet');
					break;
				
			}
			
			$this->thread->edit_user_id		= Yii::app()->user->getId();
			$this->thread->edit_user_nick	= Yii::app()->user->name;

			$this->thread->save(false);
			
			$data['success'] 	= true;
			$data['info'] 		= $message;
			
			header('Content-type: application/json');
			echo CJSON::encode($data);
			Yii::app()->end();
				
			
		}
	}
	
	public function actionAntworten() {
		$moderationshinweis = $this->holeModerationsHinweis();
		$reply = $this->speicherePost();

		$letzteBeitraege = ForumPost::holeLetzteBeitraege($this->forum['forum_id'],$this->thread['thread_id'],$this->post['post_id'],$this->post['datum_erstellt']);
		
		$this->render('antworten',array(
			'aktion'	=> Yii::t('forum','antworten'),
			'beitrag' 	=> $this->post,
			'reply'		=> $reply,	
			'beitraege' => $letzteBeitraege,
			'moderationshinweis' => $moderationshinweis,			
		));	
	}	

	public function actionZitieren() {
		$moderationshinweis = $this->holeModerationsHinweis();
		$reply = $this->speicherePost();
	
		$letzteBeitraege = ForumPost::holeLetzteBeitraege($this->forum['forum_id'],$this->thread['thread_id'],$this->post['post_id'],$this->post['datum_erstellt']);
	
		$this->render('antworten',array(
			'aktion'	=> Yii::t('forum','zitieren'),
			'beitrag' 	=> $this->post,
			'reply'		=> $reply,
			'beitraege' => $letzteBeitraege,
			'moderationshinweis' => $moderationshinweis,
		));
	}	
	
	public function actionBearbeiten() {
		$reply = $this->speicherePost();
		
		$this->render('antworten',array(
			'aktion'	=> Yii::t('forum','bearbeiten'),
			'reply'		=> $reply,
			'moderationshinweis' => null,
		));
	}

	public function actionLoeschen() {

		if(!empty($_POST)) {
			if(isset($_POST['post_id'])) {
				if($_POST['post_id']=='cookie') {
					$anzahl = 0;
					
					if(isset(Yii::app()->request->cookies[$this->cookieName])) {
							$cookies = json_decode(Yii::app()->request->cookies[$this->cookieName]->value, true);
						if(!empty($cookies)) {
							foreach($cookies as $k => $v) {
								if($v['value']==1) {
									$post_id = $v['id'];
									ForumPost::model(null,$this->forum['forum_id'])->setPostOffline($post_id,false);
									$anzahl+=1;
								}
							}
							if($anzahl > 0) {
								ForumThread::Model(null,$this->forum['forum_id'])->aktualisiereLetztenThread($this->thread->thread_id);							
							}
						}
						unset(Yii::app()->request->cookies[$this->cookieName]);
					}
					
					$uebersetzung['{Beitraege}'] = Yii::t('forum', '0#Beitraege|1#Beitrag|n>=2#Beitraege', array($anzahl));
					$data['info'] 		= Yii::t('forum','beitraege_erfolgreich_geloescht_info',$uebersetzung);
				
				} else {
					ForumPost::model(null,$this->forum['forum_id'])->setPostOffline($_POST['post_id']);
					$data['info'] 		= Yii::t('forum','beitrag_erfolgreich_geloescht_info');
					sleep(1);
				}
			} elseif(isset($_POST['thread_id'])) {
				if($_POST['thread_id']=='cookie') {
					$anzahl = 0;
					
					if(isset(Yii::app()->request->cookies[$this->cookieName])) {
					
						$cookies = json_decode(Yii::app()->request->cookies[$this->cookieName]->value, true);
						if(!empty($cookies)) {
							foreach($cookies as $k => $v) {
								$thread_id = $v['value'];
								ForumThread::model(null,$this->forum['forum_id'])->setThreadOffline($thread_id,false);
								$anzahl+=1;
							}
							if($anzahl > 0) {
								Forum::Model()->aktualisiereForum($this->forum['forum_id']);
							}
						}
						unset(Yii::app()->request->cookies[$this->cookieName]);
					}
					
					$uebersetzung['{Threads}'] = Yii::t('forum', '0#Threads|1#Threads|n>=2#Threads', array($anzahl));
					
					$data['info'] 		= Yii::t('forum','threads_erfolgreich_geloescht_info',$uebersetzung);						
				} else {
					
					ForumThread::model(null,$this->forum['forum_id'])->setThreadOffline($_POST['thread_id']);
					
					if(isset($_POST['redirect'])) {
						$data['info'] 		= Yii::t('forum','der_thread_wurde_geloescht',array('{titel}' => $this->thread['thread_titel'],'{forum}'=>$this->forum['forum_titel']));
						$data['redirect'] 	= Yii::app()->createUrl('forum/detail',array('id'=>$this->forum->forum_id,'seo'=>GFunctions::normalisiereString($this->forum->forum_titel)));
					} else {
						$data['info'] 		= Yii::t('forum','der_thread_wurde_geloescht_info',array('{titel}' => $this->thread['thread_titel'],'{forum}'=>$this->forum['forum_titel']));
					}
				}
			}
			
			$data['status'] 	= 'success';
			$data['button']		= TbHtml::button(Yii::t('global','ok'), array('data-dismiss' => 'modal'));				
			
			header('Content-type: application/json');
			echo CJSON::encode($data);
			Yii::app()->end();				
		} 
	}

	public function actionPostLoeschen() {
		if(Yii::app()->request->isPostRequest) {
		
			$id = $_POST['post_id'];
				
			echo CHtml::beginForm('','post',array('id'=>'deleteForm'));
			echo Yii::t('forum','post_wirklich_loeschen');
			//echo CHtml::tag('p',array(),$id);
			echo CHtml::hiddenField('post_id',$id,array('id'=>'ajaxDeleteButton'));
			echo CHtml::endForm();
			//throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionThreadLoeschen() {
		if(Yii::app()->request->isPostRequest) {
	
			$id = $_POST['thread_id'];
	
			echo CHtml::beginForm('','post',array('id'=>'deleteForm'));
			echo Yii::t('forum','thread_wirklich_loeschen');
			//echo CHtml::tag('p',array(),$id);
			if(isset($_POST['redirect'])) {
				echo CHtml::hiddenField('redirect',true);
			}
			echo CHtml::hiddenField('thread_id',$id,array('id'=>'ajaxDeleteButton'));
			echo CHtml::endForm();
			//throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}	
	
	public function actionThreadVerschieben() {
		if(Yii::app()->request->isPostRequest) {
	
			$id = $_POST['thread_id'];
	
			echo CHtml::beginForm('','post',array('id'=>'moveForm'));
			echo Yii::t('forum','wohin_thread_verschieben');
			echo CHtml::tag('br');
			echo CHtml::tag('br');
			$mForen = Forum::getBoardsMoveTarget($this->forum['forum_id']);
			$dropdown = CHtml::dropDownList('moveId','',
				$mForen,
              	array('empty' => Yii::t('global','bitte_waehlen')));
			echo $dropdown;
			echo CHtml::hiddenField('thread_id',$id,array('id'=>'ajaxMoveButton'));
			echo CHtml::endForm();
			//throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}	
	
	public function actionPoll() {
		$this->renderPartial('_pollErgebnis',
				array(),
				false,
				true);
	}	
	
	public function actionMove() {
	
		if(Yii::app()->request->isAjaxRequest){
				
			if(!empty($_POST)) {
				if(isset($_POST['moveId'])) {
	
					
					$neueForumId = $_POST['moveId'];
					
					$thread = ForumThread::model(null,$this->forum['forum_id'])->findByPk($_POST['thread_id']);
					
					$targetForum = Forum::model()->findByPk($neueForumId);
					
					/* 1. Thread in neuer Tabelle Speichern	 */
					
					$data = $thread->attributes;
					$data['edit_user_id']		= Yii::app()->user->getId();
					$data['edit_user_nick']		= Yii::app()->user->name;
					
					unset($data['thread_id']);
					unset($data['moved_forum_id']);
					unset($data['moved_thread_id']);
					
					
					$newThread = new ForumThread('insert',$neueForumId);
					$newThread->setAttributes($data,false);
					$newThread->save(false);

					/* 2 - alten Thread aktualisieren */
					$thread->moved_forum_id 	= $neueForumId;
					$thread->moved_thread_id 	= $newThread->getPrimaryKey();
					$thread->edit_user_id		= Yii::app()->user->getId();
					$thread->edit_user_nick	= Yii::app()->user->name;
					$thread->update();
					
					//ForumThread::model(null,$this->forum['forum_id'])->setThreadOffline($_POST['thread_id']);
					
					/* 2 - Posts des Alten Threads in neue Tabelle kopieren 
					 * - zuerst tabellenfelder auslesen 
					 */
					$postModel = new ForumPost(null,$neueForumId);
					$columns = $postModel->getMetaData()->columns;
					unset($postModel);
					
					$fields = array();
					foreach($columns as $key => $val) {
						if(!isset($fields[$key])) {
							if($key!='post_id') {
								$fields[] = $key;
							}
						}
					}				

					$insertString = implode(',',$fields);

					$valueFields = array();
					foreach($fields as $k => $v) {
						if($v == 'thread_id') {
							$valueFields[] = $newThread->getPrimaryKey();
						} else {
							$valueFields[] = $v;
						}
					}
					
					$valueString = implode(',',$valueFields);
					/*
					 * Posts in neue tabelle kopieren
					 */
					
					$sql = "INSERT INTO forum_".$neueForumId."_post (".$insertString.") SELECT ".$valueString." FROM forum_".$this->forum['forum_id']."_post WHERE thread_id = ".$thread->thread_id."";
					Yii::app()->db->createCommand($sql)->execute();
					
					/*
					 * Alte Posts offline setzen
					 */
					ForumPost::model(null,$this->forum['forum_id'])->updateAll(array('delete_flag'=>1),'thread_id = '.$thread->thread_id.'');
					

					
					$data['info'] 		= Yii::t('forum','thread_erfolgreich_verschoben',array('{forum}'=>$targetForum['forum_titel'],'{thread}'=>$thread->thread_titel));
					$data['status'] 	= 'success'; 
					$data['button']		= TbHtml::button(Yii::t('global','ok'), array('data-dismiss' => 'modal'));
	
				} else {
					ForumPost::model(null,$this->forum['forum_id'])->setPostOffline($_POST['post_id']);
					$data['info'] 		= Yii::t('forum','beitrag_erfolgreich_geloescht_info');
					sleep(1);
				}
				header('Content-type: application/json');
				echo CJSON::encode($data);
				Yii::app()->end();
			}
		} else {
			return false;
		}
	}
	
	
	private function prepareNachricht($n) {
		if(!is_array($n) && isset($this->post->user_id)) {
			$link = CHtml::tag('b',array(),Yii::t('forum','zitat_von_user',array('{user}'=>User::getStaticHtmlLink($this->post->attributes))));
			$nachricht = CHtml::tag('div',array('class'=>'quote'),$link.'<br />'.$n);
			$nachricht.= CHtml::tag('p');
		} 
		return $nachricht;
	}
	
	private function speicherePost($threadObj=null) {
		
		$action = Yii::app()->controller->action->id; 
		
		$createNewObject = array('antworten','zitieren','threaderstellen'); 
		
		if(in_array($action,$createNewObject)) {
			$reply = new ForumPost('insert',$this->forum['forum_id']);
			
			if(Yii::app()->controller->action->id=='zitieren') {
				$nachricht = $this->prepareNachricht($this->post['msg']);
				$reply->msg = $nachricht;
				//CHtml::tag('div',array('class'=>'Teaser'),$this->post['msg']);
			}
			
			
			
// TODO: multiquotes!
			
		} else {
			$reply = $this->post;
		}
		
		if(!empty($_POST['ForumPost'])) {
			if($threadObj == null) {	
				
				$threadObj = $this->thread;
				
				$reply->thread_id 			= $this->thread->thread_id;
				$reply->startbeitrag_flag	= 0;
				
			} else {
				$reply->thread_id 			= $threadObj->thread_id;
				$reply->startbeitrag_flag	= 1;
			}
			$reply->titel 				= $_POST['ForumPost']['titel'];
			$reply->msg 				= $_POST['ForumPost']['msg'];
			if(Yii::app()->controller->action->id != 'bearbeiten' && Yii::app()->controller->action->id != 'threadbearbeiten') {
				$reply->user_id				= Yii::app()->user->getId();
				$reply->user_nick			= Yii::app()->user->name;
				$reply->user_ip				= Yii::app()->request->getUserHostAddress();
				$reply->datum_erstellt		= new CDbExpression('NOW()');
			}
			
			
			if(isset($_POST['ForumPost']['post_flag'])) {
				$reply->post_flag	= $_POST['ForumPost']['post_flag'];
			} else {
				$reply->post_flag	= 0;
			}
			if(isset($_POST['ForumPost']['post_option'],$_POST['ForumPost']['post_flag'])) {
				$reply->post_option	= $_POST['ForumPost']['post_option'];
			} else {
				$reply->post_option	= 0;
			}
				
			if(!empty($_POST['ForumPost']['otext'])) {
				$reply->direktlink			= $_POST['ForumPost']['direktlink'];
				$reply->otext				= $_POST['ForumPost']['otext'];
			}
		
			if(Yii::app()->controller->action->id == 'bearbeiten' || Yii::app()->controller->action->id == 'threadbearbeiten') {
				$reply->edit_user_id		= Yii::app()->user->getId();
				$reply->edit_user_nick		= Yii::app()->user->name;
				$reply->datum_bearbeitet	= new CDbExpression('NOW()');
			}				
			
			/*
			$reply->quellen_id			= '';
			$reply->quelle				= '';
			*/	
			$reply->sprache				= Yii::app()->getLanguage();

				
			//$reply->scenario = 'aboinfo';
				
			if($reply->validate()) {
				
				if($this->_transaction != null) {
					return $reply;
				}
				
				/*
				if(isset($_POST['yt1']) && 1 == 0) {
					$preview = $this->buildPreview($beitrag, $thema, $forum);
						
					$letzteBeitraege = ForumPost::holeLetzteBeitraege($this->forum['forum_id'],$this->thread['thread_id'],$this->post['post_id'],$this->post['datum_erstellt']);
						
					$this->render('antworten',array(
							'preview'	=> $preview,
							'beitrag' 	=> $this->post,
							'reply'		=> $reply,
							'beitraege' => $letzteBeitraege,
					));
						
					Yii::app()->end();
		
				}
				*/
				if($reply->save(false)) {
						
					$sprache 	= Yii::app()->language;
					$titel 		= GFunctions::normalisiereString($threadObj->thread_titel);
					$forum_id	= $this->forum['forum_id'];
					$thread_id	= $threadObj->thread_id;
					$seite 		= $reply->page;
					$post_id	= $reply->post_id;
						
						
					$url = Yii::app()->createUrl('forum/thread',array('id' => $forum_id, 'thread_id' => $threadObj->thread_id, 'page' => $seite, 'seo'=>$titel, '#'=>'p'.$post_id));
					$this->redirect($url);
				}
			}
		}
		return $reply;
	}
	
	public function holeModerationsHinweis() {
		
		
		$attributes['post_flag'] 	= 1;
		$attributes['thread_id']	= $this->thread->thread_id;
		$condition = array('order'=>'datum_erstellt DESC');
		
		$moderationshinweis = ForumPost::model(null,$this->forum['forum_id'])->findByAttributes($attributes,$condition);
		unset($attributes,$condition);

		$cookieName = 'checkModCookie_'.$moderationshinweis['thread_id'].'_'.$moderationshinweis['post_id'];

		/*
		 * Wenn der Cookie gesetzt ist (also der Hinweis bestätig wurde
		 * , soll null returnt werden)
		 */
		if(isset($_COOKIE[$cookieName])) {
			return null;
		} 
		
		return $moderationshinweis;
	}
	
	public function actionPost() {
	
		$beitrag = $this->post;

		$beitragsnummer = $beitrag->getNummer();
		
		$this->render('post',array(
			'beitrag' 	=> $this->post,
			'beitragsnummer' => $beitragsnummer,	
		));
	}	
	
	public function actionLetzte_beitraege() {
		
		Forum::Model()->scenario = 'view_foren';
		
		$beitraege = ForumNeueBeitraege::holeLetzteBeitraege(array(), 100);
		
		$this->breadcrumbs = array(
			'Community' => array('forum/'),
			'Letzte Beiträge',
		);		
		
		$this->render('letzteBeitraege',array(
				'beitraege' => $beitraege,
			)
		);		
	}
		
	public function actionAlarm() {

		$forum		= Forum::Model()->findByPk(Yii::app()->request->getParam('forum_id'));
		$thema		= ForumThema::Model(null,$forum->forum_id)->findByPk(Yii::app()->request->getParam('thema_id'));
		$beitrag	= ForumBeitrag::Model(null,$forum->forum_id)->findByPk(Yii::app()->request->getParam('beitrag_id'));
		
		$thema->beitrag_id = $beitrag->beitrag_id;
		
		$thema->setAnzahlSeiten();
		
		$alarm = new Alarm;
		
		$this->breadcrumbs = array(
			'Community' => array('forum/'),
			$forum->forum_name => array($forum->getForumLink()),
			$thema->titel => $thema->getThemaLink(),
			'Beitrag bearbeiten',
		);		
		
		if(isset($_POST['Alarm'])) {
			
			$alarm->attributes 		= $_POST['Alarm'];
			$alarm->link 			= Yii::app()->createUrl('forum/'.WTFunctions::normalisiereString($thema->titel).'/'.$forum->forum_id.'/'.$thema->thema_id.'?page='.$thema->page.'#b'.$beitrag->beitrag_id);
			$alarm->absender_id 	= Yii::app()->user->getId();
			$alarm->absender_name	= Yii::app()->user->name; 

			//$this->redirect($url);			
			
			
			
			//$emails = array('tetzlaff@transfermarkt.de','wandtke@transfermarkt.de','carsten-tetzlaff@web.de');
			
			$emails = $alarm->getAdminMails();
			
			if($alarm->validate()) {
				$message = new YiiMailMessage('Wahretabelle.de: Alarmmeldung'); 
				$message->view = 'alarm'; 
				$message->setBody(array(
									'alarm'=>$alarm,
									'forum' 	=> $forum,
									'thema' 	=> $thema,
									'beitrag'	=> $beitrag,
								), 'text/html'); 
				$message->setTo($emails);
				//$message->addTo('tetzlaff@transfermarkt');

				$message->from = Yii::app()->params['systemMail']; 
				Yii::app()->mail->send($message);

				Yii::app()->user->setFlash('alarmieren','Dein Hinweis wurde gespeichert und wird zeitnah geprüft werden - vielen Dank für Deinen Einsatz.');
				$this->refresh();
			}
		}

		$this->render('alarm',array(
				'forum' 	=> $forum,
				'thema' 	=> $thema,
				'beitrag'	=> $beitrag,
				'alarm'		=> $alarm,
			)
		);	
	}
	
	public function loadModel($id)
	{
		$model=Forum::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}	
	
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='forum-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
}
