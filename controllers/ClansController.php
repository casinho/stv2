<?php

class ClansController extends Controller
{
	
	public function accessRules() {
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('index','view','detail','search','detail'),
						'users'=>array('*'),
				),
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('update','verwalten','getSquadMember','add','edit','create'),
						'roles'=>array('SquadAdmin','SquadLeader','ClanwarAdmin'),
				),				
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}	



	public function actionEdit()
	{
		$this->render('edit');
	}

	public function actionIndex()
	{
		$this->render('index');
	}
	
	
	
	public function actionDetail($id) {
	
		$clan		= Clans::model()->findByPk($id);
	
		$clanwars	= $clan->holeClanwars();
		//$lineup 	= $cw->holeLineup();
		//$weitereCw 	= $cw->holeWeitereClanwars();
		$relevanteNews 	= News::model()->holeRelevanteNews(5,$clan->clan_id,'clans');
	
		$this->render('detail',array(
				'clan'		=> $clan,
				'clanwars'	=> $clanwars,
				'relevanteNews' => $relevanteNews,
		));
	
	}
	
	public function actionVerwalten() {
		$alleClans = Clans::holeAlleClans();
		$this->render('clansVerwalten', array('alleClans' => $alleClans));
	}
	
	public function actionCreate()	{
	
		//Yii::app()->assetManager->forceCopy = true;
	
	
		$model=new Clans;
	
	
		$this->performAjaxValidation($model);

	
		if(isset($_POST['Clans'])) {
	
			$model->clan			= $_POST['Clans']['clan'];
			$model->tag				= $_POST['Clans']['tag'];
			$model->claninfo		= $_POST['Clans']['claninfo'];
			$model->homepage		= $_POST['Clans']['homepage'];
			$model->homepage_flag	= $_POST['Clans']['homepage_flag'];
			$model->channel			= $_POST['Clans']['channel'];
			$model->land_id			= $_POST['Clans']['land_id'];
	
			if($model->validate()) {
					
				$model->save(false);
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
				'aktion' => Yii::t('clans','clan_erstellen'),
		));
	
	}	
	
	public function actionUpdate($id)	{
	
		//Yii::app()->assetManager->forceCopy = true;
		if( (Yii::app()->user->checkAccess("ClanwarAdmin") === false) && (Yii::app()->user->checkAccess("SquadLeader")===false) ) {
			throw new CHttpException('404',Yii::t('global','du_hast_nicht_genuegend_rechte'));
		}
		
		
		$model=$this->loadModel($id);
	
	
		$this->performAjaxValidation($model);
	
	
		if(isset($_POST['Clans'])) {
	
			$model->clan			= $_POST['Clans']['clan'];
			$model->tag				= $_POST['Clans']['tag'];
			$model->claninfo		= $_POST['Clans']['claninfo'];
			$model->homepage		= $_POST['Clans']['homepage'];
			$model->homepage_flag	= $_POST['Clans']['homepage_flag'];
			$model->channel			= $_POST['Clans']['channel'];
			$model->land_id			= $_POST['Clans']['land_id'];
	
			if($model->validate()) {
					
				$model->save(false);
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
				'aktion' => Yii::t('clans','clan_bearbeiten'),
		));
	
	}
	
	
	public function actionAdd() {
		$model=new Clans;
		$model->setScenario('form');

		if(isset($_POST['term'])) {
			$model->clan 		= $_POST['term'];
			$model->land_id 	= Yii::app()->params['unbekannt_land_id'];
				
			$this->renderPartial('_form',array('model'=>$model,'case'=>'ajax'));
			Yii::app()->end();
			/*
				if($model->save()) {
			header('Content-type: application/json');
			echo CJSON::encode($model);
			Yii::app()->end();
			}*/
		} elseif(isset($_POST['Clans'])) {
				
			$model->clan 		= $_POST['Clans']['clan'];
			$model->tag 		= $_POST['Clans']['tag'];
			$model->homepage	= $_POST['Clans']['homepage'];
			$model->channel 	= $_POST['Clans']['channel'];
			$model->land_id 	= $_POST['Clans']['land_id'];
				
			$valid=$model->validate();
			
			if($valid){
				$model->save(false);
				$array = array('status'=>'success','id'=>$model->clan_id,'text'=>$model->clan);
				header('Content-Type: application/json');
				echo CJSON::encode($array);
				Yii::app()->end();
			} else{
				$error = CActiveForm::validate($model);
				if($error!='[]') {
					echo $error;
				}
				Yii::app()->end();
			}
		}
	
	
	
	}
	
	
	public function actionSearch($id, $index) {
	
		$string = trim($id);
	
	
		$retval = array();
	
		if($index == 'id') {
			//$result = Yii::app()->db->createCommand("SELECT l.text,l.id FROM link AS l INNER JOIN clanwars AS c ON c.liga_id = l.id WHERE l.text LIKE :match OR l.tag LIKE :match GROUP BY l.id")->bindValue(':match',"%".$q."%")->queryAll();
			$result = Yii::app()->db->createCommand("SELECT clan_id,clan FROM clans WHERE clan_id = :id")->bindValue(':id',$id)->queryRow();
			if(!empty($result)) {
				$retval= array('id' => $result['clan_id'], 'name' => $result['clan']);
			}
				
		}
			
		header('Content-Type: application/json');
		echo CJSON::encode($retval);
		Yii::app()->end();
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
		$model=Clans::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='squad-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}	
	
}