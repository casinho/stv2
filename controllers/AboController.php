<?php

class AboController extends Controller {
	public function actionAdd() {
		$this->render('add');
	}

	public function actionDelete() {
		$this->render('delete');
	}

	public function actionEdit() {
		$this->render('edit');
	}

	public function actionIndex() {
		$this->render('index');
	}
	
	public function actionDetail() {
		//$master_id 	= Yii::app()->getRequest()->getParam('id');
		$master_id = 0;
		if(empty($master_id)) {
			$master_id = 0;
		}
		$abos = Forum2Thread2Abo::model()->holeUserAbos(false,10,$master_id);
		
		$this->renderPartial('_ajaxAbo', array(
				'abos'		=> $abos,
		), false, true);
		
	}
	
	public function actionThreadTracking() {
		if(Yii::app()->request->isAjaxRequest){
	
			if(!empty($_POST)) {
				if(isset($_POST['thread_id'],$_POST['forum_id'],$_POST['user_id'])) {
		
					$attributes['thread_id'] 	= $_POST['thread_id'];
					$attributes['forum_id'] 	= $_POST['forum_id'];
					$attributes['user_id'] 		= $_POST['user_id'];
		
					$check = Forum2Thread2Abo::model()->findByAttributes($attributes);
		
					$data['msg'] = '';
		
					if(isset($_POST['aktion'])) {
						if(empty($check)) {
		
							$attributes['datum'] = date('Y-m-d H:i:s');
		
							$abo = new Forum2Thread2Abo;
							$abo->attributes = $attributes;
							$abo->save();
							$data['msg'] = Yii::t('forum','thread_erfolgreich_abonniert');
		
		
							$data['value'] 		= Yii::t('forum','abonnement_entfernen');
							$data['info'] 		= Yii::t('forum','abonnement_entfernen_info');
							$data['btnClass'] 	= 'btn secondary';
						} else {
		
							$check->delete();
							$data['msg'] = Yii::t('forum','abonnement_geloescht');
		
							$data['value'] 		= Yii::t('forum','abonnieren');
							$data['info'] 		= Yii::t('forum','thema_abo_info');
							$data['btnClass'] 	= 'btn forum';
		
						}
					} else {
						if(empty($check)) {
							$data['value'] 	= Yii::t('forum','abonnieren');
							$data['info'] 	= Yii::t('forum','thema_abo_info');
							$data['btnClass'] 	= 'btn forum';
						} else {
							$data['value'] 	= Yii::t('forum','abonnement_entfernen');
							$data['info'] 	= Yii::t('forum','abonnement_entfernen_info');
							$data['btnClass'] 	= 'btn forum secondary';
						}
					}
		
					//
		
					header('Content-type: application/json');
					echo CJSON::encode($data);
					Yii::app()->end();
				}
			}
		}
	
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