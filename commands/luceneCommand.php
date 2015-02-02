<?php

Yii::import('application.vendors.*');
require_once('Zend/Search/Lucene.php');

setlocale(LC_CTYPE, 'de_DE.utf8');
//setlocale(LC_CTYPE, 'de_DE.iso-8859-1');

class luceneCommand extends CConsoleCommand {

	public $start;
	
	public $zendIndex;
	public $zendDocument;
	
	private $_indexFiles = 'runtime.search';
			
	public function actionIndicateSearch() {
		
		try {
			
		
		$this->start = new CDbExpression('NOW()');

		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(
			new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ()
		);
		
			 
		$this->zendIndex 	= new Zend_Search_Lucene(Yii::getPathOfAlias('application.' . $this->_indexFiles), true);
				
		//$indicateModels = array('Forum');
		
		$indicateModels = array('User','Clans','News','Clanwars','Forum','File');
		
		foreach($indicateModels as $k => $model) {
			$this->indicateModel($model);
			$this->createCronEintrag($model);
		}
		
		$this->zendIndex->optimize();
		$this->zendIndex->commit();		
	
		}catch(Exception $e) {
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
			case 'File':
				$this->createMapIndixies();
				break;																				
		}
		
		$this->createCronEintrag($model);
	}
	
	public function createNewsIndixies() {
		
		$news = News::model()->findAll();
		foreach($news as $k => $v){

			$text = strip_tags($v->text);
			
			$this->zendDocument = new Zend_Search_Lucene_Document();
		
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('title',$this->sanitize($v->titel,false), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('link',$this->sanitize($v->getLink()), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('category',$this->sanitize('News'), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('content',$this->sanitize(html_entity_decode($v->text),false), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('content',$this->sanitize($text), 'utf-8'));
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
	
	public function createMapIndixies() {
	
		$criteria = new CDbCriteria();
		$criteria->condition = 'typ = 1';
		
		$maps = File::model()->with()->findAll($criteria);
		foreach($maps as $k => $v){
	
			$this->zendDocument = new Zend_Search_Lucene_Document();
	
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('title',$this->sanitize($v->name), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('link',$this->sanitize($v->getLink('maps/detail')), 'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('category',$this->sanitize('Maps'), 'utf-8'));
	
			$qry = "SELECT COUNT(*) FROM map2clanwar WHERE map_id = ".$v->id;
			$res = Yii::app()->db->createCommand($qry)->queryScalar(); 
			
			$text = array();
	
			$uebersetzung['{gespielt}'] = Yii::t('maps', '0#Clanwars|1#Clanwar|n>=2#Clanwars', array($res));
			$uebersetzung['{map}'] = $v->name;
				
			$text[] = Yii::t('maps','map_wurde_n_gespielt',$uebersetzung);

			$content = html_entity_decode(implode(', ',$text));
	
			$this->zendDocument->addField(Zend_Search_Lucene_Field::Text('content',$this->sanitize($content),'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id',$v->id,'utf-8'));
			$this->zendDocument->addField(Zend_Search_Lucene_Field::unIndexed('fremd_id2',0, 'utf-8'));
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
				$text.= strip_tags($vm->report);
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
					$strip = strip_tags($vp->msg);
					$txt[] = html_entity_decode($strip);
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