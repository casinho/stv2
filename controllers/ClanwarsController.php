<?php

class ClanwarsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	
	public $wertungenTyp = array(array('id'=>3,'typ' => 'Sieg'), array('id'=>2,'typ' => 'Niedelagen'), array('id'=>1,'typ' => 'Unentschieden'));
	
	public $editRecht = false;
	
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','clans','member','maps','detail','squad','search'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','verwalten','getSquadMember','addMap'),
				'roles'=>array('ClanwarAdmin','SquadLeader'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('delete'),
				'roles'=>array('ClanwarAdmin','Superadmin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	private function setRechte() {
		if(!$this->editRecht = Yii::app()->user->checkAccess('Superadmin')) {
			$this->editRecht = Yii::app()->user->checkAccess('ClanwarAdmin');
		}
	}
	
	protected function beforeAction($action) {
		$this->setRechte();
		return parent::beforeAction($action);
	}	
	
	public function actionVerwalten() {
		$alleClanwars = Clanwars::holeAlleClanwars();
		$this->render('clanwarsVerwalten', array('alleClanwars' => $alleClanwars));
	}
	
	
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()	{
		
		//Yii::app()->assetManager->forceCopy = true;
		
		$clans = array();
		$competitions = array();
		
		$model=new Clanwars;

		$model->poster_id 	= Yii::app()->user->getId();
		$model->gametype		= 1;
		$model->datum		= Yii::app()->dateFormatter->formatDateTime(time(),'medium',false);
		
		$this->performAjaxValidation($model);
		
		$imageBig = null;
		
		$maps = array();
		if(isset($_POST['Map2Clanwar'])) {
			foreach($_POST['Map2Clanwar'] as $k => $v) {
				$m2c = new Map2Clanwar();
				$m2c->attributes = $v;
				$maps[] = $m2c;
			}
		} 
		
		$spieler = array();
		if(isset($_POST['cw'])) {
			foreach($_POST['cw'] as $k => $v) {
				$u2c = new User2Clanwar();
				$u2c->user_id = $k;
				$spieler[$k] = $u2c;
			}
		} 
		
		if(isset($_POST['weitere_spieler']) && !empty($_POST['weitere_spieler'])) {
			$further = explode(",",$_POST['weitere_spieler']);
			foreach($further as $k => $v) {
				$u2c = new User2Clanwar();
				$u2c->user_id = $v;
				$spieler[$v] = $u2c;
			}
		}		
				
		if(isset($_POST['Clanwars'])) {
			
			$model->squad_id		= $_POST['Clanwars']['squad_id'];
			$model->spielerzahl		= $_POST['Clanwars']['spielerzahl'];
			$model->enemy_id		= $_POST['enemy_id'];
			$model->enemy_spieler	= $_POST['Clanwars']['enemy_spieler'];
			$model->liga_id			= $_POST['competition'];
			$model->servername		= $_POST['Clanwars']['servername'];
			$model->scorelimit		= $_POST['Clanwars']['scorelimit'];
			$model->timelimit		= $_POST['Clanwars']['timelimit'];
			//$model->sonstiges		= $_POST['Clanwars']['sonstiges'];
			$model->anzahl_maps		= count($maps);
			//$model->ringer1			= $_POST['Clanwars']['ringer1'];
			//$model->ringer2			= $_POST['Clanwars']['ringer2'];
			$model->report			= $_POST['Clanwars']['report'];
			$model->wertung			= $_POST['Clanwars']['wertung'];
			$model->endscore		= $_POST['Clanwars']['endscore'];
			$model->geg_endscore	= $_POST['Clanwars']['geg_endscore'];
			$model->fazit			= $_POST['Clanwars']['fazit'];
			$model->datum			= Yii::app()->dateFormatter->format("yyyy-MM-dd",strtotime($_POST['datum']));
			
			
			if($model->validate()) {
				$transaction = Yii::app()->db->beginTransaction();
			
				$model->save(false);
			
				$maps = array();
				if(isset($_POST['Map2Clanwar'])) {
					$i = 1;
					foreach($_POST['Map2Clanwar'] as $k => $v) {
						$m2c = new Map2Clanwar();
						$m2c->attributes = $v;
						$m2c->clanwar_id = $model->id;
						$m2c->map_nr = $i;
						$m2c->enemy_id = $model->enemy_id;
						$m2c->save(false);
						$i+=1;
					}
				}
				
				if(isset($_POST['cw'])) {
					foreach($_POST['cw'] as $k => $v) {
						$u2c = new User2Clanwar();
						$u2c->user_id = $k;
						$u2c->clanwar_id = $model->id;
						$u2c->save(false);						
					}
				}				

				if(isset($_POST['weitere_spieler']) && !empty($_POST['weitere_spieler'])) {
					$further = explode(",",$_POST['weitere_spieler']);
					foreach($further as $k => $v) {
						$u2c = new User2Clanwar();
						$u2c->user_id = $v;
						$u2c->clanwar_id = $model->id;
						$u2c->save(false);
					}
				}
				
			
				$transaction->commit();		
				$this->redirect($model->getLink());
			}	
			
			/*
			if($model->save()) {
				$vorausgewaehlte_squads = $vorausgewaehlte_clans = $vorausgewaehlte_member = $vorausgewaehlte_competition = array();
				$this->verarbeiteUebergebeneZuordnungen($news->id, compact('vorausgewaehlte_squads', 'vorausgewaehlte_clans', 'vorausgewaehlte_member', 'vorausgewaehlte_competition'));
				$this->redirect(array('update','id'=>$news->id));
			}*/
		}
		
		

		$this->render('create', array(
				'model' => $model, 
				'size' => 'teaser',
				'group' => 'clanwars',
				'clans' => $clans,
				'competitions' => $competitions,
				'maps' => $maps,
				'spieler' => $spieler,
				'aktion' => Yii::t('clanwars','clanwar_erstellen'),
		));	
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)	{
		
		$clans = array();
		$competitions = array();
		
		$model=$this->loadModel($id);

		$cw1 = Yii::app()->user->checkAccess("Squad CW bearbeiten",array("squad_id"=>$model->squad_id));
		
		if((Yii::app()->user->checkAccess("Squad CW bearbeiten",array("squad_id"=>$model->squad_id)) === false) && (Yii::app()->user->checkAccess("Eigenen CW bearbeiten",array("user_id"=>$model->poster_id)) === false) && (Yii::app()->user->checkAccess("Clanwars verwalten")===false) ) {
			throw new CHttpException('404',Yii::t('global','du_hast_nicht_genuegend_rechte'));
		}		
		
		
		$model->poster_id 	= Yii::app()->user->getId();
		$model->gametype		= 1;
		$model->datum		= Yii::app()->dateFormatter->formatDateTime($model->datum,'medium',false);
		
		$this->performAjaxValidation($model);
		
		$imageBig = null;
		
		$attributes['clanwar_id'] = $model->id;
		$maps = Map2Clanwar::model()->findAllByAttributes($attributes);

		if(isset($_POST['Map2Clanwar'])) {
			foreach($_POST['Map2Clanwar'] as $k => $v) {
				if(empty($v['auto_id'])) {
					$m2c = new Map2Clanwar();
					$m2c->attributes = $v;
					$maps[] = $m2c;
				}
			}
		} 
		
		$attributes['clanwar_id'] = $model->id;
		$data = User2Clanwar::model()->findAllByAttributes($attributes);
		$spieler = array();
		
		$alteSpieler = array();
		
		foreach($data as $k => $v) {
			$spieler[$v->user_id] = true;
			$alteSpieler[] = $v->user_id;
		}

		$neueSpieler = array();
		
		if(isset($_POST['cw'])) {
			// geloeschte USer filtern
			foreach($_POST['cw'] as $k => $v) {
				$u2c = new User2Clanwar();
				$u2c->user_id = $k;
				$spieler[$k] = $u2c;
				$neueSpieler[] = $k;
			}
		} 
		
		if(isset($_POST['weitere_spieler']) && !empty($_POST['weitere_spieler'])) {
			$further = explode(",",$_POST['weitere_spieler']);
			foreach($further as $k => $v) {
				$neueSpieler[] = $v;
			}
		}
		
		
		$delSpieler = array();
		
		foreach($alteSpieler as $k => $alte_user_id) {
			if(!in_array($alte_user_id,$neueSpieler)) {
				$delSpieler[] = $alte_user_id;
			}
		}

		$newSpieler = array();
		
		foreach($neueSpieler as $k => $neue_user_id) {
			if(!in_array($neue_user_id,$alteSpieler)) {
				$newSpieler[] = $neue_user_id;
			}
		}		

		
		
		

		//GFunctions::pre($delSpieler);
		
		if(isset($_POST['Clanwars'])) {
			
			$model->squad_id		= $_POST['Clanwars']['squad_id'];
			$model->spielerzahl		= $_POST['Clanwars']['spielerzahl'];
			$model->enemy_id		= $_POST['enemy_id'];
			$model->enemy_spieler	= $_POST['Clanwars']['enemy_spieler'];
			$model->liga_id			= $_POST['competition'];
			$model->servername		= $_POST['Clanwars']['servername'];
			$model->scorelimit		= $_POST['Clanwars']['scorelimit'];
			$model->timelimit		= $_POST['Clanwars']['timelimit'];
			//$model->sonstiges		= $_POST['Clanwars']['sonstiges'];
			$model->anzahl_maps		= count($maps);
			//$model->ringer1			= $_POST['Clanwars']['ringer1'];
			//$model->ringer2			= $_POST['Clanwars']['ringer2'];
			$model->report			= $_POST['Clanwars']['report'];
			$model->wertung			= $_POST['Clanwars']['wertung'];
			$model->endscore		= $_POST['Clanwars']['endscore'];
			$model->geg_endscore	= $_POST['Clanwars']['geg_endscore'];
			$model->fazit			= $_POST['Clanwars']['fazit'];
			$model->datum			= Yii::app()->dateFormatter->format("yyyy-MM-dd",strtotime($_POST['datum']));
			
			
			if($model->validate()) {
				$transaction = Yii::app()->db->beginTransaction();
			
				$model->save(false);
			
				if(isset($_POST['Map2Clanwar'])) {
					$i = 1;
					foreach($_POST['Map2Clanwar'] as $k => $v) {
						if(!empty($v['auto_id'])) {
							$m2c = Map2Clanwar::model()->findByPk($v['auto_id']);
						} else {
							$m2c = new Map2Clanwar();
						}
						
						if(isset($v['loeschen']) && $v['loeschen']==1 && !empty($v['auto_id'])) {
							$m2c->delete();
						} else {
							$m2c->attributes = $v;
							$m2c->clanwar_id = $model->id;
							$m2c->map_nr = $i;
							$m2c->enemy_id = $model->enemy_id;
							$m2c->report = $v['report'];
							$m2c->save(false);
							$i+=1;
						}
					}
				}
				
				if(isset($_POST['cw'])) {

					foreach($newSpieler as $k => $v) {
						$u2c = new User2Clanwar();
						$u2c->user_id = $v;
						$u2c->clanwar_id = $model->id;
						$u2c->save(false);						
					}

					foreach($delSpieler as $k => $v) {
						$attributes['clanwar_id'] = $model->id;
						$attributes['user_id'] = $v;
						User2Clanwar::model()->deleteAllByAttributes($attributes);
					}
				}				

			
				$transaction->commit();		
				$this->redirect($model->getLink());
			}	
			
			/*
			if($model->save()) {
				$vorausgewaehlte_squads = $vorausgewaehlte_clans = $vorausgewaehlte_member = $vorausgewaehlte_competition = array();
				$this->verarbeiteUebergebeneZuordnungen($news->id, compact('vorausgewaehlte_squads', 'vorausgewaehlte_clans', 'vorausgewaehlte_member', 'vorausgewaehlte_competition'));
				$this->redirect(array('update','id'=>$news->id));
			}*/
		}
		
		

		$this->render('create', array(
				'model' => $model, 
				'size' => 'teaser',
				'group' => 'clanwars',
				'clans' => $clans,
				'competitions' => $competitions,
				'maps' => $maps,
				'spieler' => $spieler,
				'aktion' => Yii::t('clanwars','clanwar_bearbeiten'),
		));	
		
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
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

	/**
	 * Lists all models.
	 */
	public function actionIndex() 	{
		
		$squad_id = Yii::app()->request->getParam('id');
		
		if(!empty($squad_id)) {
			$alleClanwars= Clanwars::holeAlleClanwars($squad_id);
			$squad = Squad::model()->findByPk($squad_id);
			$data = $this->chartStatistik($squad_id);
		} else {
			$alleClanwars= Clanwars::holeAlleClanwars();
			$squad = null;
			$data = $this->chartStatistik();			
		}
		
		$letzteKommentare  = KommentarZuweisung::holeLetzteKommentare(4,'clanwars');
		
		$this->render('index',array(
			'alleClanwars'=>$alleClanwars,
			'squadData' => $data,
			'squad' => $squad,	
			'letzteKommentare' => $letzteKommentare	
		));
	}
	
	private function chartStatistik($id = 0) {
		
		$criteria = new CDbCriteria();
		if($id == 0) {
			$criteria->condition = "st_flag =:st_flag AND war_flag = :war_flag";
			$criteria->params = array(':st_flag' => 1,':war_flag' => 1);
		} else {		
			$criteria->condition = "t.squad_id = :squad_id";
			$criteria->params = array(':squad_id' => $id);
		}		
		
		$squads = Squad::model()->findAll($criteria);
		
		$squadData 	= array();
		
		$squadRow = array();
		$squadRow[0][] = 'Squad';
		
		$i = 0;
		
		foreach($squads as $k => $v) {
		
			$squadData[$k] = $v->attributes;
		
			$squadVal[$k][$i] = $v->squad_name;
		
			foreach($this->wertungenTyp as $kk => $vv) {
		
				$i+=1;
		
				$typus = Clanwars::getClanwarStatus($vv['id']);
				if(!in_array($typus,$squadRow[0])) {
					$squadRow[0][] = $typus;
				}
		
				$sql = "SELECT COUNT(*) FROM clanwars WHERE squad_id = ".$v['squad_id']." AND wertung = ".$vv['id']."";
				$squadVal[$k][$i] = (int)Yii::app()->db->createCommand($sql)->queryScalar();
			}
		
			$i=0;
		}	

		$data = array_merge($squadRow,$squadVal);
		return $data;
	}
	
	public function actionSquad($id) {

		$squad = Squad::model()->findByPk($id);
		
		$alleClanwars= Clanwars::holeSquadClanwars($id);
	
		$data = $this->chartStatistik($id);
		
		$this->render('squad',array(
				'alleClanwars'=>$alleClanwars,
				'squadData' => $data,
				'squad' => $squad,
		));
	}	
	
	public function actionDetail($id) {
		
		$cw 		= Clanwars::model()->with('gegner','squad')->findByPk($id);
		if($cw===null)
			throw new CHttpException(404,'The requested page does not exist.');		
		
		$maps 		= $cw->holeMaps();
		$lineup 	= $cw->holeLineup();
		$weitereCw 	= $cw->holeWeitereClanwars();
		
		$this->render('detail',array(
			'cw'		=> $cw,
			'maps'		=> $maps,
			'lineup' 	=> $lineup,
			'weitereCw' => $weitereCw,
		));		
		
	}
	
	public function actionGetSquadMember($id) {
		
		$criteria = new CDbCriteria();
		$criteria->condition = 't.squad_id = :squad_id';
		$criteria->params = array(':squad_id'=>$id);		
		
		$squad	= Squad::model()->with('user','user2squad')->find($criteria);
		
		$this->renderPartial('_squadMember',array('squad'=>$squad));
	}
	
	public function actionAddMap($id,$myInput) {
	

		$Map2Clanwar = new Map2Clanwar();
		
		$criteria = new CDbCriteria();
		$criteria->condition = 't.squad_id = :squad_id';
		$criteria->params = array(':squad_id'=>$id);		
		
		$squad	= Squad::model()->with('user','user2squad')->find($criteria);
	
		$this->renderPartial('_addMap',array('squad'=>$squad,'Map2Clanwar'=>$Map2Clanwar,'myInput'=> $myInput));
	}	

	public function actionClans() {
		
		$alleClans = Clanwars::holeAlleClans();
		
		$this->render('clan',array(
			'alleClans'=>$alleClans,
			//'squadData' => $data,
		));		
	}	
	
	public function actionMember() {
		
		$alleMember = Clanwars::holeAlleMember();
		
		$this->render('member',array(
			'alleMember'=>$alleMember,
			//'squadData' => $data,
		));		
	}
	
	public function actionMaps() {
	
		$squad_id = Yii::app()->request->getParam('id');
		
		if(!empty($squad_id)) {
			$alleMaps = Clanwars::holeAlleMaps($squad_id);
			$squad = Squad::model()->findByPk($squad_id);
			$data = $this->chartStatistik($squad_id);
		} else {
			$alleMaps = Clanwars::holeAlleMaps();
			$data = $this->chartStatistik();
			$squad = null;
		}
		
		$this->render('maps',array(
				'alleMaps'=>$alleMaps,
				'squadData' => $data,
				'squad' => $squad,
		));
	}	
	

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Clanwars('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Clanwars']))
			$model->attributes=$_GET['Clanwars'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Clanwars::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='clanwars-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionSearch($id, $index) {
	
		$string = trim($id);
	
	
		$retval = array();
	
		if($index == 'id') {
			//$result = Yii::app()->db->createCommand("SELECT l.text,l.id FROM link AS l INNER JOIN clanwars AS c ON c.liga_id = l.id WHERE l.text LIKE :match OR l.tag LIKE :match GROUP BY l.id")->bindValue(':match',"%".$q."%")->queryAll();
			$result = Yii::app()->db->createCommand("SELECT id,name FROM file WHERE id = :id")->bindValue(':id',$id)->queryRow();
			if(!empty($result)) {
				$retval= array('id' => $result['id'], 'name' => $result['name']);
			}
		} elseif($index == 'map') {
			$result = Yii::app()->db->createCommand("SELECT id,name FROM file WHERE name LIKE :match AND typ = 1")->bindValue(':match',"%".$id."%")->queryAll();
			foreach($result as $k => $v) {
				$retval[] = array('id' => $v['id'], 'name' => $v['name']);
			}			
		} elseif($index == 'user') {
			$result = Yii::app()->db->createCommand("SELECT user_id,user_nick FROM user WHERE user_nick LIKE :match")->bindValue(':match',"%".$id."%")->queryAll();
			foreach($result as $k => $v) {
				$retval[] = array('id' => $v['user_id'], 'name' => $v['user_nick']);
			}			
		} elseif($index == 'user_id') {
			$result = Yii::app()->db->createCommand("SELECT user_id,user_nick FROM user WHERE user_id = :id")->bindValue(':id',$id)->queryRow();
			if(!empty($result)) {
				$retval= array('id' => $result['user_id'], 'name' => $result['user_nick']);
			}			
		}
			
		header('Content-Type: application/json');
		echo CJSON::encode($retval);
		Yii::app()->end();
	}
	
	
}
