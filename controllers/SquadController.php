<?php

class SquadController extends Controller
{
	
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
						'actions'=>array('index','view','clans','member','maps','detail','squad','search','news'),
						'users'=>array('*'),
				),
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('update','verwalten','getSquadMember','addMap','addSpieler','edit'),
						'roles'=>array('SquadAdmin','SquadLeader'),
				),
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('delete','create','sort'),
						'roles'=>array('SquadAdmin'),
				),								
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}

	public function actionVerwalten() {
		$alleSquads = Squad::holeAlleSquads();
		$this->render('squadVerwalten', array('alleSquads' => $alleSquads));
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
	
	
		$model=new Squad;
	
	
		$this->performAjaxValidation($model);
	
		$spieler = array();
		if(isset($_POST['User2Squad'])) {
			foreach($_POST['User2Squad'] as $k => $v) {
				$u2c = new User2Squad();
				$u2c->user_id = $v['user_id'];
				$spieler[$v['user_id']] = $u2c;
			}
		}
	
		if(isset($_POST['Squad'])) {
				
			$model->squad_name		= $_POST['Squad']['squad_name'];
			$model->squad_tag		= $_POST['Squad']['squad_tag'];
			$model->info			= $_POST['Squad']['info'];
			$model->st_flag			= $_POST['Squad']['st_flag'];
			$model->war_flag		= $_POST['Squad']['war_flag'];
			$model->try_flag		= $_POST['Squad']['try_flag'];
			$model->try_info		= $_POST['Squad']['try_info'];
				
			if($model->validate()) {
				$transaction = Yii::app()->db->beginTransaction();
					
				$model->save(false);
					
				if(isset($_POST['User2Squad'])) {
					foreach($_POST['User2Squad'] as $k => $v) {
						$u2c = new User2Squad();
						$u2c->user_id 		= $v['user_id'];
						$u2c->leader_flag 	= $v['leader_flag'];
						$u2c->orga_flag 	= $v['orga_flag'];
						$u2c->squad_id		= $model->squad_id;
						$u2c->save();
						$spieler[$v['user_id']] = $u2c;
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
				'spieler' => $spieler,
				'aktion' => Yii::t('squad','squad_erstellen'),
		));		
		
	}
	
	public function actionUpdate($id)	{
	
		//Yii::app()->assetManager->forceCopy = true;
		
		if( (Yii::app()->user->checkAccess("Eigenen Squad bearbeiten",array("squad_id"=>$id)) === false) && (Yii::app()->user->checkAccess("Squad verwalten")===false) ) {
			throw new CHttpException('404',Yii::t('global','du_hast_nicht_genuegend_rechte'));
		}
		
	
		$model=$this->loadModel($id);
	
		$this->performAjaxValidation($model);
	
		$spieler = array();
		
		$attributes['squad_id'] = $id;
		
		$leader = array();
		$orga	= array();
		
		$data = User2Squad::model()->findAllByAttributes($attributes);
		if($data != null) {
			foreach($data as $k => $v) {
				$spieler[$v['user_id']] = $v;
				if($v->leader_flag == 1) {
					$leader[$v->auto_id] = $v->auto_id;
				}
				if($v->orga_flag == 1) {
					$orga[] = $v->auto_id;
				}				
			}
		}
		
		if(isset($_POST['User2Squad'])) {
			foreach($_POST['User2Squad'] as $k => $v) {
				$u2c = new User2Squad();
				$u2c->user_id = $v['user_id'];
				$spieler[$v['user_id']] = $u2c;
			}
		}
	
		if(isset($_POST['Squad'])) {
	
			$model->squad_name		= $_POST['Squad']['squad_name'];
			$model->squad_tag		= $_POST['Squad']['squad_tag'];
			$model->info			= $_POST['Squad']['info'];
			$model->st_flag			= $_POST['Squad']['st_flag'];
			$model->war_flag		= $_POST['Squad']['war_flag'];
			$model->try_flag		= $_POST['Squad']['try_flag'];
			$model->try_info		= $_POST['Squad']['try_info'];
	
			if($model->validate()) {
				$transaction = Yii::app()->db->beginTransaction();
					
				$model->save(false);
					
				if(isset($_POST['leader'])) {
					foreach($_POST['leader'] as $k => $v) {
						if(!in_array($v,$leader)) {
							User2Squad::model()->updateByPk($v, array('leader_flag' => 1));
						} 
						unset($leader[$v]);
					}
				}

				if(isset($_POST['orga'])) {
					foreach($_POST['orga'] as $k => $v) {
						if(!in_array($v,$orga)) {
							User2Squad::model()->updateByPk($v, array('orga_flag' => 1));
						}
						unset($orga[$v]);
					}
				}

				if(isset($_POST['delete'])) {
					foreach($_POST['delete'] as $k => $v) {
						User2Squad::model()->deleteByPk($v);
					}
				}
				
				
				
				foreach($leader as $k => $v) {
					User2Squad::model()->updateByPk($v, array('leader_flag'=>0));
				}
				
				foreach($orga as $k => $v) {
					User2Squad::model()->updateByPk($v, array('orga_flag'=>0));
				}
				
				if(isset($_POST['User2Squad'])) {
					foreach($_POST['User2Squad'] as $k => $v) {
						$u2c = new User2Squad();
						$u2c->user_id 		= $v['user_id'];
						$u2c->leader_flag 	= $v['leader_flag'];
						$u2c->orga_flag 	= $v['orga_flag'];
						$u2c->squad_id		= $model->squad_id;
						$u2c->save();
						$spieler[$v['user_id']] = $u2c;
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
				'spieler' => $spieler,
				'aktion' => Yii::t('squad','squad_bearbeiten'),
		));
	
	}	
	
	public function actionNews($id) {
	
		$squaddata = $this->loadModel($id);
		
		$alleNews = News::holeAlleRelevantenNews($id,'squad');
	
		$this->render('news', array('alleNews' => $alleNews,'squaddata'=>$squaddata));
	}	
	
	public function loadModel($id)
	{
		$model=Squad::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function actionAddSpieler($myInput) {
		$User2Squad = new User2Squad();
		$this->renderPartial('_addSpieler',array('User2Squad'=>$User2Squad,'myInput'=> $myInput));
	}
	
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='squad-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	
	public function actionAdd()
	{
		$this->render('add');
	}

	public function actionDelete($id) {
		
		$sql = "SELECT COUNT(*) FROM clanwars WHERE squad_id = ".$id."";
		$numWar = Yii::app()->db->createCommand($sql)->queryScalar();
		if($numWar > 0) {
			//throw new CDbException('Möööp, Squad hat Clanwars ausgetragen! Löschung nicht möglich.');
			$data['success'] 	= false;
			$data['msg']		= 'Möööp, Squad hat Clanwars ausgetragen! Löschung nicht möglich.'; 
				
			header('Content-Type: application/json');
			echo CJSON::encode($data);
			Yii::app()->end();			
		} else {

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
	}
	

	public function actionSort() {
	
		if (isset($_POST['items']) && is_array($_POST['items'])) {
			$i = 1;
			foreach ($_POST['items'] as $item) {
				$model = Squad::model()->findByPk($item);
				$model->squad_order = $i;
				$model->save();
				$i+=1;
			}
		}
	
		//$this->render('view');
	}	
	
	public function actionEdit() 	{
		if(Yii::app()->request->isAjaxRequest) {
			$squad_id = $_POST['squad_id'];
				
			$squad = Squad::model()->findByPk($squad_id);
				
			if($squad != null) {
				$squad->squad_order = $_POST['value'];
				if($squad->validate()) {
					$squad->save();
					$response['error'] 	= false;
				} else {
					$response['error'] = Yii::t('forum','fehlerhafte_eingabe');
				}
			} else {
				$response['error'] = Yii::t('forum','unerwarteter_fehler');
			}
			echo $response['output'] = $squad->squad_order;
			//header('Content-type:application/json');
			//echo CJSON::encode($response);
			Yii::app()->end();
		}
	}
	
	

	public function actionIndex()
	{
		$this->render('index');
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}