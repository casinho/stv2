<?php
class NewsController extends Controller {
	public $useXUpload = false;
	
	public $editRecht = false;
	
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	public function accessRules() {
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','detail'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('create','update','admin','upload','ajaxcrop','verwalten','search','HoleVerlinkungen'),
				'roles'=>array('NewsAdmin','SquadLeader'),
			),
			array('allow',
				'actions'=>array('delete'),
				'roles'=>array('NewsAdmin'),
			),				
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	public function actions() {
        return array(
            'upload'=>array(
                'class'=>'xupload.actions.XUploadAction',
                'path' =>Yii::app()->getBasePath() . "/../images/news/originals",
                'publicPath' => Yii::app()->getBaseUrl() . "/images/news/originals",
            ),
        );
	}

	/*
	private function setRechte() {
		$this->editRecht = Yii::app()->user->checkAccess('NewsAdmin')) {
			$this->editRecht = Yii::app()->user->checkAccess("Eigene News bearbeiten",array("user_id"=>$data["poster_id"])) || Yii::app()->user->checkAccess("NewsAdmin")
		}
	}
	*/
	
	protected function beforeAction($action) {
		//$this->setRechte();
		return parent::beforeAction($action);
	}	

	public function actionIndex() {

		$alleNews = News::holeAlleNews();
		$this->breadcrumbs = array('News');
	
		$this->render('newsUebersicht', array('alleNews' => $alleNews));
	}
	
	public function actionVerwalten() {
		$alleNews = News::model()->findAll(array('order' => 'datum DESC'));
		$this->render('newsVerwalten', array('alleNews' => $alleNews));
	}	
	
	/**
	 * @sitemap dataSource=getModelsUrls
	 */
	
	public function actionDetail() {
		
		$newsId = Yii::app()->request->getParam('id');
		$news = News::model()->with('links','bigImage')->findByPk($newsId);
		
		$newsVerlinkungen = NewsZuweisung::holeNewsVerlinkungen($newsId);
		
		$aktuelleNews 	= News::model()->holeStartseitenNews(5);
		
		$relevanteNews 	= $news->holeRelevanteNews(5);
		
		$this->render('newsAnsicht', array(
				'news' => $news,
				'aktuelleNews'=>$aktuelleNews,
				'relevanteNews'=>$relevanteNews,
				'newsVerlinkungen'=>$newsVerlinkungen
		));
	}
	
	public function getModelsUrls() {
		$models=News::model()->findAll();
		$data=array();
		foreach($models as $model)
			$data[]=array(
					'params'=>array('id'=>$model->id,'seo'=>GFunctions::normalisiereString($model->titel)),
					// Optional parameters
					'changefreq'=>'monthly',
					'priority'=>0.5,
					'lastmod'=>$model->datum,
			);
		return $data;
	}	
	
	public function actionAdmin() {
		$this->breadcrumbs = array(
			'Admin-Center' => array('admincenter/'),
			'News-Übersicht',
		);
		
		$alleNews = News::model()->findAll(array('order' => 'datum DESC'));
		$this->render('adminNewsUebersicht', array('alleNews' => $alleNews));
	}
	
	public function actionCreate() {
		$news = new News();
		$news->wichtig 		= 0;
		$news->poster_id 	= Yii::app()->user->getId();
		$news->datum		= Yii::app()->dateFormatter->formatDateTime(time(),'medium','short');
		
		$this->performAjaxValidation($news);
		
		$imageBig = null;
		
		if(isset($_POST['News'])) {
			
			$news->titel 		= $_POST['News']['titel'];
			$news->name 		= isset($_POST['News']['name']) ? $_POST['News']['name'] : Yii::app()->user->name;
			$news->text 		= $_POST['News']['text'];
			$news->slidertext	= $_POST['News']['slidertext'];
			$news->slidertextposition = $_POST['News']['slidertextposition'];
			$news->email		= '';
			$news->kategorie_id = isset($_POST['News']['kategorie_id']) ? $_POST['News']['kategorie_id'] : 0;
			$news->wichtig		= isset($_POST['News']['wichtig']) ? 1 : 0;
			$news->image_id		= isset($_POST['News']['image_id']) ? $_POST['News']['image_id'] : 0;
			$news->big_image_id	= isset($_POST['News']['big_image_id']) ? $_POST['News']['big_image_id'] : 0;
			$news->datum		= Yii::app()->dateFormatter->format("yyyy-MM-dd HH:mm:ss",strtotime($_POST['News']['datum']));
			$news->datum_only	= Yii::app()->dateFormatter->format("yyyy-MM-dd",strtotime($_POST['News']['datum']));
			$news->zeit_only	= Yii::app()->dateFormatter->format("HH:mm:ss",strtotime($_POST['News']['datum']));

			if($news->big_image_id > 0) {
				$imageBig = Files::model()->findByPk($news->big_image_id);
			} 
			
			
			if($news->save()) {
				$vorausgewaehlte_squads = $vorausgewaehlte_clans = $vorausgewaehlte_member = $vorausgewaehlte_competition = array();
				$this->verarbeiteUebergebeneZuordnungen($news->id, compact('vorausgewaehlte_squads', 'vorausgewaehlte_clans', 'vorausgewaehlte_member', 'vorausgewaehlte_competition'));
				//$this->redirect(array('update','id'=>$news->id));
				$this->redirect($news->getLink());
			}
		}
		$this->breadcrumbs = array(
			'Admin-Center' => array('admincenter/'),
			'Neue News erstellen',
		);
		
		$vorausgewaehlte['squads'] 		= array();
		$vorausgewaehlte['member'] 		= array();
		$vorausgewaehlte['clans'] 		= array();
		$vorausgewaehlte['competition'] = array();
		
		
		$this->render('create', array(
				'model' => $news, 
				'imageBig' => $imageBig, 
				'vorausgewaehlte'=>$vorausgewaehlte,
				'size' => 'teaser',
				'group' => 'news',
		));
	}
	
	
	public function actionUpdate() {

		
		$news = News::model()->findByPk(Yii::app()->request->getParam('id'));
		
		if(Yii::app()->user->checkAccess("Eigene News bearbeiten",array("user_id"=>$news->poster_id))===false && Yii::app()->user->checkAccess("NewsAdmin") === false) {
			throw new CHttpException('404',Yii::t('global','du_hast_nicht_genuegend_rechte'));
		}		
		
		$news->datum = Yii::app()->dateFormatter->format("dd.MM.yyyy HH:mm",strtotime($news->datum));
		if($news->big_image_id > 0) {
			$imageBig = Files::model()->findByPk($news->big_image_id);
		} else {
			$imageBig = null;
		}		
		$zuordnungModels = NewsZuweisung::model()->findAllByAttributes(array('news_id' => $news->id));

		
		$vorausgewaehlte['squads'] 		= array();
		$vorausgewaehlte['member'] 		= array();
		$vorausgewaehlte['clans'] 		= array();
		$vorausgewaehlte['competition'] = array();
				
		$vorausgewaehlte_squads = $vorausgewaehlte_member = $vorausgewaehlte_clans = $vorausgewaehlte_competition = array();
		
		
		foreach($zuordnungModels as $zuordnungModel) {
			
			if($zuordnungModel->zuweisung == 'squad') {
				$vorausgewaehlte['squads'][] = $this->getSquads($zuordnungModel->fremd_id, true);
				$vorausgewaehlte_squads[] = $zuordnungModel->fremd_id;
			} elseif($zuordnungModel->zuweisung == 'clans') {
				$vorausgewaehlte['clans'][] = $this->getClans($zuordnungModel->fremd_id, true);
				$vorausgewaehlte_clans[] = $zuordnungModel->fremd_id;
			} elseif($zuordnungModel->zuweisung == 'user') {
				$vorausgewaehlte['member'][] = $this->getUsers($zuordnungModel->fremd_id, true);
				$vorausgewaehlte_member[] = $zuordnungModel->fremd_id;
			} elseif($zuordnungModel->zuweisung == 'link') {
				$vorausgewaehlte['competition'][] = $this->getCompetition($zuordnungModel->fremd_id, true);
				$vorausgewaehlte_competition[] = $zuordnungModel->fremd_id;
			} 
		}

		
		$this->performAjaxValidation($news);
		
		if(isset($_POST['News'])) {

			$news->titel 		= $_POST['News']['titel'];
			$news->name 		= isset($_POST['News']['name']) ? $_POST['News']['name'] : Yii::app()->user->name;
			$news->poster_id 	= Yii::app()->user->getId();
			$news->text 		= $_POST['News']['text'];
			$news->slidertext	= $_POST['News']['slidertext'];
			$news->slidertextposition = $_POST['News']['slidertextposition'];
			$news->email		= '';
			$news->kategorie_id = isset($_POST['News']['kategorie_id']) ? $_POST['News']['kategorie_id'] : 0;
			$news->wichtig		= isset($_POST['News']['wichtig']) ? 1 : 0;
			$news->image_id		= isset($_POST['News']['image_id']) ? $_POST['News']['image_id'] : 0;
			$news->big_image_id	= isset($_POST['News']['big_image_id']) ? $_POST['News']['big_image_id'] : 0;
			$news->datum		= Yii::app()->dateFormatter->format("yyyy-MM-dd HH:mm:ss",strtotime($_POST['News']['datum']));
			$news->datum_only	= Yii::app()->dateFormatter->format("yyyy-MM-dd",strtotime($_POST['News']['datum']));
			$news->zeit_only	= Yii::app()->dateFormatter->format("HH:mm:ss",strtotime($_POST['News']['datum']));

			if($news->big_image_id > 0) {
				$imageBig = Files::model()->findByPk($news->big_image_id);
			} 
			if($news->save()) {
				$this->verarbeiteUebergebeneZuordnungen($news->id, compact('vorausgewaehlte_squads', 'vorausgewaehlte_clans', 'vorausgewaehlte_member', 'vorausgewaehlte_competition'));				
				$this->redirect(array('detail','id'=>$news->id));
			}
		}
		

		
		$this->breadcrumbs = array(
			'Admin-Center' => array('admincenter/'),
			'News-Übersicht' => array('news/admin'),
			$news->titel,
		);
		$this->render('create', array(
				'model' => $news, 
				'imageBig' => $imageBig, 
				'vorausgewaehlte'=>$vorausgewaehlte,
				'size' => 'teaser',
				'group' => 'news',
		));
	}
	
	public function actionDelete($id) {
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])) {
			//$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('verwalten'));
		
			$data['success'] = true;
				
			header('Content-Type: application/json');
			echo CJSON::encode($data);
			Yii::app()->end();
		}			
	}
	
	public function loadModel($id)
	{
		$model=News::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function actionAjaxCrop() {
		$news = News::model()->findByPk(Yii::app()->request->getParam('id'));
		
		Yii::import('ext.jcrop.EJCropper');
		
		$jcropper = new EJCropper();
		$jcropper->thumbPath = 'images/news/originals';
		
		// get the image cropping coordinates (or implement your own method)
 		$coords = $jcropper->getCoordsFromPost('imageId');
 		
		// some settings ...
		$jcropper->targ_w = $coords['w'];
		$jcropper->targ_h = $coords['h'];
		$jcropper->jpeg_quality = 85;
		$jcropper->png_compression = 8;
		
		// returns the path of the cropped image, source must be an absolute path.
		$memlimit = ini_get('memory_limit');
		ini_set('memory_limit', '512M');
		$thumbnail = $jcropper->crop('images/news/upload/'.$news->bild, $coords);
		ini_set('memory_limit', $memlimit);
	}
	
	protected function performAjaxValidation($model) {
		if(isset($_POST['ajax']) && $_POST['ajax']==='news-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionSearch($q, $index) {
		$string = trim($q);
	
		$retval = array();
		
		if($index == 'squad_new') {
			$result = Squad::model()->findAll('st_flag = :st_flag AND squad_name LIKE :match',array('st_flag'=>1,':match' => "%".$q."%"));
			foreach($result as $k => $v) {
				$retval[] = array('id' => $v['squad_id'], 'name' => $v['squad_name']);
			}
		} elseif($index == 'member_new') {
			$result = User::model()->findAll('user_nick LIKE :match',array(':match' => "%".$q."%"));
			foreach($result as $k => $v) {
				$retval[] = array('id' => $v['user_id'], 'name' => $v['user_nick']);
			}
		} elseif($index == 'competition_new') {
			//$result = Yii::app()->db->createCommand("SELECT l.text,l.id FROM link AS l INNER JOIN clanwars AS c ON c.liga_id = l.id WHERE l.text LIKE :match OR l.tag LIKE :match GROUP BY l.id")->bindValue(':match',"%".$q."%")->queryAll();
			$result = Yii::app()->db->createCommand("SELECT l.text,l.id FROM link AS l WHERE (l.text LIKE :match OR l.tag LIKE :match) AND l.typ = 2 GROUP BY l.id")->bindValue(':match',"%".$q."%")->queryAll();
			foreach($result as $k => $v) {
				$retval[] = array('id' => $v['id'], 'name' => $v['text']);
			}
		} elseif($index == 'clan_new') {
			$result = Clans::model()->findAll('clan LIKE :match',array(':match' => "%".$q."%"));
			foreach($result as $k => $v) {
				$retval[] = array('id' => $v['clan_id'], 'name' => $v['clan']);
			}
		}
			
		header('Content-Type: application/json');
		echo CJSON::encode($retval);
		Yii::app()->end();
	}
	
	

	private function getSquads($Ids, $getSingle = false) {
		$retval = array();
		$Ids = (is_array($Ids) ? $sIds : array($Ids));
		foreach($Ids as $Id) {
			$obj = Squad::model()->findByPK($Id);
			if(is_object($obj)) {
				if($getSingle) {
					$retval = array('id' => $obj->squad_id, 'name' => $obj->squad_name);
				} else {
					$retval[] = array('id' => $obj->squad_id, 'name' => $obj->squad_name);
				}
			}
		}
		return $retval;
	}
	
	private function getClans($Ids, $getSingle = false) {
		$retval = array();
		$Ids = (is_array($Ids) ? $sIds : array($Ids));
		foreach($Ids as $Id) {
			$obj = Clans::model()->findByPK($Id);
			if(is_object($obj)) {
				if($getSingle) {
					$retval = array('id' => $obj->clan_id, 'name' => $obj->clan);
				} else {
					$retval[] = array('id' => $obj->clan_id, 'name' => $obj->clan);
				}
			}
		}
		return $retval;
	}	

	private function getUsers($Ids, $getSingle = false) {
		$retval = array();
		$Ids = (is_array($Ids) ? $sIds : array($Ids));
		foreach($Ids as $Id) {
			$obj = User::model()->findByPK($Id);
			if(is_object($obj)) {
				if($getSingle) {
					$retval = array('id' => $obj->user_id, 'name' => $obj->user_nick);
				} else {
					$retval[] = array('id' => $obj->user_id, 'name' => $obj->user_nick);
				}
			}
		}
		return $retval;
	}
	
	private function getCompetition($Ids, $getSingle = false) {
		$retval = array();
		$Ids = (is_array($Ids) ? $sIds : array($Ids));
		foreach($Ids as $Id) {
			$obj = Link::model()->findByPK($Id);
			if(is_object($obj)) {
				if($getSingle) {
					$retval = array('id' => $obj->id, 'name' => $obj->text);
				} else {
					$retval[] = array('id' => $obj->id, 'name' => $obj->text);
				}
			}
		}
		return $retval;
	}	
	
	public function actionHoleVerlinkungen() {
		$squadModels = $clanModels = $competitionModels = $userModels = array();
		
		
		if(isset($_POST)) {
			if(isset($_POST['squads'])) {
				foreach($_POST['squads'] as $data) {
					$squadModels[] = Squad::model()->findByPK($data['id']);
				}
			}
			if(isset($_POST['users'])) {
				foreach($_POST['users'] as $data) {
					$userModels[] = User::model()->findByPK($data['id']);
				}
			}
			if(isset($_POST['clans'])) {
				foreach($_POST['clans'] as $data) {
					$clanModels[] = Clans::model()->findByPk($data['id']);
				}
			}
			if(isset($_POST['competitions'])) {
				foreach($_POST['competitions'] as $data) {
					$competitionModels[] = Link::model()->findByPK($data['id']);
				}
			}
		}
		$this->renderPartial('_verlinkungen', compact('squadModels', 'userModels', 'clanModels', 'competitionModels'));
	}
	
	private function verarbeiteUebergebeneZuordnungen($news_id, $array) {
		
		extract($array);
		
		if(isset($_POST['Squad']['squad_id'])) {
			$squadIds = $this->splitTokenInput($_POST['Squad']['squad_id']);
			$neueZuordnungen['squad'] = array_diff($squadIds, $vorausgewaehlte_squads);
			$geloeschteZuordnungen['squad'] = array_diff($vorausgewaehlte_squads, $squadIds);
		}
		if(isset($_POST['User']['user_id'])) {
			$userIds = $this->splitTokenInput($_POST['User']['user_id']);
			$neueZuordnungen['user'] = array_diff($userIds, $vorausgewaehlte_member);
			$geloeschteZuordnungen['user'] = array_diff($vorausgewaehlte_member, $userIds);
	
		}		
		if(isset($_POST['Clans']['clan_id'])) {
			$clanIds = $this->splitTokenInput($_POST['Clans']['clan_id']);
			$neueZuordnungen['clans'] = array_diff($clanIds, $vorausgewaehlte_clans);
			$geloeschteZuordnungen['clans'] = array_diff($vorausgewaehlte_clans, $clanIds);
		
		}		
		if(isset($_POST['Link']['id'])) {
			$competitionIds = $this->splitTokenInput($_POST['Link']['id']);
			$neueZuordnungen['link'] = array_diff($competitionIds, $vorausgewaehlte_competition);
			$geloeschteZuordnungen['link'] = array_diff($vorausgewaehlte_competition, $competitionIds);
		}		

		$this->speichereVeraenderteZuordnung($news_id, $neueZuordnungen, $geloeschteZuordnungen);
	}
	
	private function speichereVeraenderteZuordnung($news_id, $neueZuordnungen, $geloeschteZuordnungen) {
		foreach($neueZuordnungen as $kategorie => $daten) {
			if(count($daten) == 0) {
				continue;
			}

			foreach($daten as $id) {
				$zuweisung = new NewsZuweisung();
				$zuweisung->news_id 	= $news_id;
				$zuweisung->fremd_id 	= $id;
				$zuweisung->zuweisung	= $kategorie;
				$zuweisung->save(false);
			}
		}
	
		foreach($geloeschteZuordnungen as $kategorie => $daten) {
			if(count($daten) == 0) {
				continue;
			}
			foreach($daten as $id) {
				NewsZuweisung::model()->deleteAll("news_id = ".$news_id." AND fremd_id = ".$id." AND zuweisung = '".$kategorie."'");
			}
		}
	}

	private function splitTokenInput($string = null) {
		if(!$string) {
			return array();
		}
		if(stripos($string, ',') !== false) {
	
			return explode(',', $string);
		}
		return array($string);
	}
	
	
	
}
?>