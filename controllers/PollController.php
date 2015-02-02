<?php

class PollController extends Controller {
	
	public function filters() {
		return array(
				'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function accessRules() {
		return array(
			array('allow',
					'actions' => array('delete','view','add','edit','save'),
					'users'=>array('*'),
			),
			array('deny',  // deny all users
					'users'=>array('*'),
			),
		);
	}	
	
	
	public function actionSave() {
		
		if(!empty($_POST)) {
			if(isset($_POST['thread_id'],$_POST['forum_id'],$_POST['option'])) {

				if(Yii::app()->user->isGuest) {
					$data['info'] = Yii::t('forum','du_musst_eingeloggt_sein');
					$data['case'] = 'error';
					header('Content-type: application/json');
					echo CJSON::encode($data);
					Yii::app()->end();					
				}
				
				
				$attributes['forum_id'] 	= $_POST['forum_id'];
				$attributes['thread_id'] 	= $_POST['thread_id'];
				
				sleep(2);
				
				foreach($_POST['option'] as $k => $v) {
					$poll = Polls::model()->findByPk($v);
					$poll->saveCounters(array('count_votes'=>1));
					unset($poll);
					
					$attributes['option_id'] 	= $v;
					$attributes['user_id'] 		= Yii::app()->user->getId();
					
					$stimme = User2Poll::model()->findByAttributes($attributes);
					if($stimme == null) {
						$stimme = new User2Poll();
					}
					$stimme->forum_id 	= $_POST['forum_id'];
					$stimme->thread_id	= $_POST['thread_id'];
					$stimme->user_id 	= Yii::app()->user->getId();
					$stimme->option_id 	= $v;
					$stimme->datum		= new CDbExpression('NOW()');

					$stimme->save(false);
					unset($stimme);					
				} 
				
				$data['info'] = Yii::t('forum','stimme_erfolgreich_gespeichert');
				$data['case'] = 'success';
				
			} else {
				$data['info'] = Yii::t('forum','daten_konnten_nicht_gespeichert_werden');
				$data['case'] = 'error';
			}
			
			header('Content-type: application/json');
			echo CJSON::encode($data);
			Yii::app()->end();
				
		}
	}
	
	public function actionAdd()
	{
		$this->render('add');
	}

	public function actionDelete() 	{
		
		if(Yii::app()->request->isAjaxRequest) {
			$abo_id = Yii::app()->request->getParam('id');
			if(!empty($abo_id)) {
				ForumAbo::model()->deleteByPk($abo_id);
				return true;
			}
		}
		
		Yii:app()->end;
	}

	public function actionEdit()
	{
		$this->render('edit');
	}

	public function actionView()
	{
		$this->render('view');
	}

}