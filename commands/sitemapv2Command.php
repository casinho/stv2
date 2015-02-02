<?php

Yii::import('application.vendors.sitemap-class*');
require_once('Sitemap.php');

class sitemapv2Command extends CConsoleCommand {

			
	public function actionDoMap() {
		
		try {

			$sitemap = new Sitemap;
			
			
			
			$sitemap = new Sitemap('http://www.santitan.de/de/');
			$sitemap->setPath(Yii::getPathOfAlias('application').'/../downloads/');
			$sitemap->setFilename('sitemap_index');
			
				
			$heute = date('y-m-d');
				
			$sitemap->addItem('/de/', '1.0', 'daily', $heute);
			$sitemap->addItem('/de/news', '0.8', 'weekly', $heute);
			$sitemap->addItem('/de/member/squads', '0.4', 'monthly', $heute);
			$sitemap->addItem('/de/member', '0.3', 'monthly', $heute);
			$sitemap->addItem('/de/alphabetic/index/member/num', '0.3', 'monthly', $heute);
				
			$alphas = range('A', 'Z');
			foreach($alphas as $k => $v) {
				$sitemap->addItem('/de/alphabetic/index/member/'.$v, '0.3', 'monthly', $heute);
			}
				
			$sitemap->addItem('/de/member/ehemalige', '0.4', 'monthly', $heute);
			$sitemap->addItem('/de/clanwars', '0.4', 'monthly', $heute);
			$sitemap->addItem('/de/forum', '0.6', 'weekly', $heute);
			$sitemap->addItem('/de/sonstiges/impressum', '0.3', 'yearly', $heute);
				
			
			$models = array('User','Clans','News','Clanwars','File');
		
			foreach($models as $k => $v) {
				
				$name = str2lower($v);
				
				$sitemap->setFilename('sitemap_'.$name);
				
				if($v == 'User') {
					$criteria = new CDbCriteria();
					$criteria->condition = 'member_flag > 0';
					$model = $v::model()->findAll($criteria);
				} elseif($v == 'File') {
					$criteria = new CDbCriteria();
					$criteria->condition = 'typ = 1';
					$model = $v::model()->findAll($criteria);						
				} else {
					$model = $v::model()->findAll();
				}
				
				if($v == 'News') {
					$prio = '0.9';
				} else {
					$prio = '0.4';
				}
				
				foreach($model as $kk => $vv) {
					if($v == 'File') {
						$link = str_replace('./de','/de',$vv->getLink('maps/detail'));
					} else {
						$link = str_replace('./de','/de',$vv->getLink());
					}
					
					if(isset($vv->datum)) {
						if(isset($vv->datum_only)) {
							$sitemap->addItem($link, '0.4', 'weekly', $vv->datum_only);
						} else {
							$sitemap->addItem($link, '0.4', 'weekly', $vv->datum);
						}
					} else {
						$sitemap->addItem($link, '0.4', 'monthly', date('M Y'));
					}
				}
			}
			
			$sitemap->setFilename('sitemap_forum');
			
			$criteria = new CDbCriteria();
			$criteria->condition = 'online_flag = 1 AND zugriffs_flag = 0 AND parent_id > 0';
			
			$forum = Forum::model()->findAll($criteria);
			
			foreach($forum as $k => $v) {
				$sitemap->addItem('/de/'.GFunctions::normalisiereString($v->forum_titel).'/detail/forum/'.$v->forum_id, '0.7', 'weekly', $heute);
			}
			
			foreach($forum as $k => $v) {
				$criteria = new CDbCriteria();
				$criteria->condition = 'delete_flag = 0';
				$threads = ForumThread::model(null,$v->forum_id)->findAll($criteria);
				foreach($threads as $kk => $vv) {
					
					$seiten = $vv::holeAnzahlSeitenStatic($vv->anz_posts);
					
					for($i = 1; $i <= $seiten; $i++) {
						$sitemap->addItem('/de/'.GFunctions::normalisiereString($vv->thread_titel).'/thread/forum/'.$v->forum_id.'/thread_id/'.$vv->thread_id.'/page/'.$i, '0.7', 'weekly', $v->datum_antwort);
					}
				}
				
			}
			
			$sitemap->createSitemapIndex('http://www.santitan.de/downloads/', 'Today');
		
	
		} catch(Exception $e) {
			echo "huch?!? - ";
			print_r($e->getMessage());
		}

	}
	
	private function indicateModel($model) {
		$this->start = new CDbExpression('NOW()');
		
		switch($model) {
			case 'News':
				$this->createNewsIndixies();
				break;
			case 'Clanwars':
				$this->createClanwarIndixies();
				break;
			case 'User':
				$this->createUserIndixies();
				break;
			case 'Forum':
				$this->createForumIndixies();
				break;
			case 'Clans':
				$this->createClansIndixies();
				break;																
		}
		
		$this->createCronEintrag($model);
	}
	
	public function createNewsIndixies() {
		
		$news = News::model()->findAll();
		foreach($news as $k => $v){

			$this->zendDocument = new Zend_Search_Lucene_Document();
		
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('title',$this->sanitize($v->titel,false), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('link',$this->sanitize($v->getLink()), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('category',$this->sanitize('News'), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('content',$this->sanitize(html_entity_decode($v->text),false), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id',$this->sanitize($v->id), 'utf-8')	);			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id',$this->sanitize(0), 'utf-8'));	
			$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('created',time()));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('updated',time()));			

			$this->zendIndex->addDocument($this->zendDocument);
			
		}
		
		#$this->zendIndex->optimize();
		#$this->zendIndex->commit();		
	
	}

	public function createUserIndixies() {
	
		$criteria = new CDbCriteria();
		$criteria->condition = 'member_flag > 0';
		
		$user = User::model()->with('squad','land')->findAll($criteria);
		foreach($user as $k => $v){
			
			$this->zendDocument = new Zend_Search_Lucene_Document();
			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('title',$this->sanitize($v->user_nick), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('link',$this->sanitize($v->getLink()), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('category',$this->sanitize('User'), 'utf-8'));
	
			$text = array();
		
			$text[] = $v->membertype;
			if($v->land != null) {
				$text[] = $v->land->nationalname;
			}
			$memberSquads = array();
			foreach($v->squad as $tk => $tv) {
				$memberSquads[] = $tv->getHeadline();
			}			

			$text[] = Yii::t('member','squads').': '.implode(', ',$memberSquads);
			$text[] = Yii::t('member','status').': '.$v->status;
			
			$content = html_entity_decode(implode(', ',$text));
			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('content',$this->sanitize($content),'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id',$this->sanitize($v->user_id),'utf-8'));			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id2',$this->sanitize(0), 'utf-8'));			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('created',time()));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('updated',time()));			

			$this->zendIndex->addDocument($this->zendDocument);
		}
		
		#$this->zendIndex->optimize();
		#$this->zendIndex->commit();		
		

	}	
	

	public function createClansIndixies() {
	
		$clans = Clans::model()->with('land')->findAll();
		foreach($clans as $k => $v){
				
			$this->zendDocument = new Zend_Search_Lucene_Document();
				
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('title',$this->sanitize($v->clan), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('link',$this->sanitize($v->getLink()), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('category',$this->sanitize('Clans'), 'utf-8'));
	
			$text = array();

			if($v->tag != '') {
				$text[] = Yii::t('clans','tag').': '.$v->tag;
			}			
			
			if($v->land != null) {
				$text[] = $v->land->nationalname;
			}
	
			if($v->channel != '') {
				$text[] = Yii::t('clans','channel').': '.$v->channel;
			}

			if($v->homepage != '') {
				$text[] = Yii::t('clans','homepage').': '.$v->homepage;
			}			
			
			$content = html_entity_decode(implode(', ',$text));
				
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('content',$this->sanitize($content),'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id',0,'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id2',0, 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('created',time()));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('updated',time()));
	
			$this->zendIndex->addDocument($this->zendDocument);
		}
	
		#$this->zendIndex->optimize();
		#$this->zendIndex->commit();
	
	
	}	
	
	
	public function createClanwarIndixies() {
	
		$clanwars = Clanwars::model()->findAll();
		foreach($clanwars as $k => $v){
				
			$this->zendDocument = new Zend_Search_Lucene_Document();
			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('title',$this->sanitize($v->getHeadline()), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('link',$this->sanitize($v->getLink()), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('category',$this->sanitize('Clanwars'), 'utf-8'));
	
			
			$text = $v->report;
			
			$maps 		= $v->holeMaps();
			
			foreach($maps as $km => $vm) {
				$text.= $vm->report;
			}
			
			$text = html_entity_decode($text);
			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('content',$this->sanitize($text), 'utf-8'));

			$lineup 	= $v->holeLineup();

			$p = array();
			foreach($lineup as $kl => $vl) {
				if($vl->user != null) {
					$p[] = $vl->user->user_nick;
				}
			}
			
			if(!empty($p)) {
				$player = implode(', ',$p);
			} else {
				$player = '-';
			}
	
			/*
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('lineup',
					$this->sanitize($player)
					, 'utf-8')
			);

			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('matchtype',
					$this->sanitize($v->liga->name)
					, 'utf-8')
			);			
			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('tags',
					$this->sanitize($v->liga->tag)
					, 'utf-8')
			);
			*/
			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id',$this->sanitize($v->id), 'utf-8'));			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id2',$this->sanitize(0), 'utf-8'));			
			$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('created',time()));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('updated',time()));			

			$this->zendIndex->addDocument($this->zendDocument);
		}
		
		#$this->zendIndex->optimize();
		#$this->zendIndex->commit();		
		
	}	
	
	public function createForumIndixies() {
	
		$criteria = new CDbCriteria();
		//$criteria->condition = 'online_flag > 0 AND parent_id > 0 AND forum_id = 5';
		$criteria->condition = 'online_flag > 0 AND parent_id > 0 ';
	
		$foren = Forum::model()->findAll($criteria);
		
		$tcriteria = new CDbCriteria();
		$tcriteria->condition = 'delete_flag = 0 AND moved_forum_id = 0';
		
		foreach($foren as $k => $v) {

			$threads = ForumThread::model(null,$v->forum_id)->findAll($tcriteria);
			
			#echo "ANZ THREAD: ".count($threads).PHP_EOL;
			
			foreach($threads as $kt => $vt) {
				
				$this->zendDocument = new Zend_Search_Lucene_Document();
				
				$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('title',$this->sanitize($vt->thread_titel), 'utf-8'));				

				$pcriteria = new CDbCriteria();
				$pcriteria->condition = 'delete_flag = 0 AND thread_id = '.$vt->thread_id;				
				
				$posts = ForumPost::model(null,$v->forum_id)->findAll($pcriteria);
				
				$txt = array();
				foreach($posts as $kp => $vp) {
					$txt[] = html_entity_decode($vp->msg);
				}
				
				
				
				$content = implode(', ',$txt);
				
				$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('content',$this->sanitize($content), 'utf-8'));				
				$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id',$this->sanitize($v->forum_id), 'utf-8'));				
				$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id2',$this->sanitize($vt->thread_id), 'utf-8'));
				$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('category',$this->sanitize('Forum'), 'utf-8'));	
				$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('link',$this->sanitize($vt->getLink()), 'utf-8'));
				
				#var_dump($this->sanitize($vt->thread_titel));
				#var_dump($this->sanitize($content));
				/*
				echo "======================================".PHP_EOL;
				echo $vt->thread_titel.' - '.$v->forum_titel.PHP_EOL;
				echo $content.PHP_EOL.PHP_EOL;
				*/
				
				$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('created',time()));
				$this->zendDocument->addField(Zend_Search_Lucene_Field::UnIndexed('updated',time()));	
							
				$this->zendIndex->addDocument($this->zendDocument);				
			}

		}
		
		
		#$this->zendIndex->optimize();
		#$this->zendIndex->commit();		
	}	
	
	
	private function sanitize($input,$encode = true){
		if($encode === true) {
			//$input = utf8_encode($input);
		}
		//return htmlentities(strip_tags($input));
		return strip_tags($input);
	}	
	
	private function createCronEintrag($typ) {
		$status	= 1;	// 1 = OK
		$cron 	= 'Lucene indiziert: '.$typ;
		$info   = 'Die Lucene-Indexierung wurde erfolgreich durchgeführt';
		Cronjob::erstelleEintrag($cron,$this->start,$status,$info);		
	}
	
	
}
?>