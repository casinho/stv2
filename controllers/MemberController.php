<?php

class MemberController extends Controller
{
	
	public $wertungenTyp = array(array('id'=>3,'typ' => 'Sieg'), array('id'=>2,'typ' => 'Niedelagen'), array('id'=>1,'typ' => 'Unentschieden'));	
	
	public function actionDetail($id) {
		
		$member = User::model()->with('land','squad')->findByPk($id);

		$memberSquads = array();
		foreach($member->squad as $k => $v) {
			$memberSquads[] = $v->getHeadline(); 
		}
		
		
		$einsaetze = $member->holeAlleEinsaetze();
		
		$attributes['st_flag'] 	= 1;
		$attributes['war_flag'] = 1;
		

		$qry = "SELECT s.squad_id,s.squad_name FROM clanwars AS w INNER JOIN user2clanwar AS u2w ON u2w.clanwar_id = w.id INNER JOIN squad AS s ON s.squad_id = w.squad_id WHERE u2w.user_id = ".$member->user_id." GROUP BY w.squad_id";
		
		$squads = Yii::app()->db->createCommand($qry)->queryAll();
		
		$squadData 	= array();
		
		$squadRow = array();
		$squadRow[0][] = 'Squad';
		
		$i = 0;
		$j = 0;
		
		$squadVal = array();
		
		foreach($squads as $k => $v) {
				
			$squadData[$k] = $v;
				
			$squadVal[$k][$i] = $v['squad_name'];
				
			foreach($this->wertungenTyp as $kk => $vv) {
		
				$i+=1;
		
				$typus = Clanwars::getClanwarStatus($vv['id']);
				if(!in_array($typus,$squadRow[0])) {
					$squadRow[0][] = $typus;
				}
		
				$sql = "SELECT COUNT(*) FROM clanwars AS w INNER JOIN user2clanwar AS u2w ON u2w.clanwar_id = w.id WHERE w.squad_id = ".$v['squad_id']." AND w.wertung = ".$vv['id']." AND u2w.user_id = ".$member->user_id;
				$squadVal[$k][$i] = (int)Yii::app()->db->createCommand($sql)->queryScalar();
			}
				
			$i=0;
			$j+=1;
		}
		
		if($j > 0) {
			$data = array_merge($squadRow,$squadVal);
		} else {
			$data = array();
		}
		
		$relevanteNews 	= News::model()->holeRelevanteNews(5,$member->user_id,'user');
		
		$letzteBeitraege = Forum::holeMemberBeitraege(7,$member->user_id);
		
		$this->render('detail',array(
				'member'=>$member,
				'einsaetze'=>$einsaetze,
				'squadData'=>$data,
				'memberSquads'=>$memberSquads,
				'relevanteNews' => $relevanteNews,
				'letzteBeitraege' => $letzteBeitraege,
		));
	}

	
	protected function gridForumTitel($data,$row) {
		return $this->renderPartial('_forumTitelZelle',array('data'=>$data),true);
	}
	
	protected function gridForumAntwort($data,$row) {
		return $this->renderPartial('_forumAntwortZelle',array('data'=>$data),true);
	}
	
	public function actionIndex()	{
		$chosenLetter = Yii::app()->getRequest()->getParam('id');
		if(empty($chosenLetter)) {
			$chosenLetter = 'alle';
		}
		$alleMember= User::holeAlleMember($chosenLetter);
		
		$squads	= Squad::model()->findAllByAttributes(array('st_flag'=>1,'war_flag'=>1));
		
		$this->render('index',array(
			'alleMember' => $alleMember,
			'squads'	 => $squads,	
		));
	}

	public function actionEhemalige()	{
		$chosenLetter = Yii::app()->getRequest()->getParam('id');
		if(empty($chosenLetter)) {
			$chosenLetter = 'alle';
		}
		$alleMember= User::holeEhemaligeMember($chosenLetter);
	
		$squads	= Squad::model()->findAllByAttributes(array('st_flag'=>1,'war_flag'=>1));
	
		$this->render('ehemalige',array(
				'alleMember' => $alleMember,
				'squads'	 => $squads,
		));
	}	
	
	public function actionSquads() {
		
		$criteria = new CDbCriteria();
		$criteria->condition = 'st_flag = :st_flag AND war_flag = :war_flag';
		$criteria->params = array(':st_flag' => 1, ':war_flag'=>1);		
		$criteria->order = 'squad_order';
		
		$squads	= Squad::model()->with('user','user2squad')->findAll($criteria);
		
		foreach($squads as $k => $v) {
			$v->setLeaderData();
		}

		$arrayDataProvider=new CArrayDataProvider($squads, array(
			'keyField'=>'squad_id',
			/* 'sort'=>array(
					'attributes'=>array(
					'username', 'email',
					),
				), 
			*/
			'pagination'=>array(
				'pageSize'=>25,
			),
		));		

		$criteria = new CDbCriteria();
		$criteria->condition = 't.squad_id = :squad_id';
		$criteria->params = array(':squad_id' => 15);
		$criteria->order = 'squad_order';
		$ehren	= Squad::model()->with('user','user2squad')->findAll($criteria);

		$ehrenDataProvider=new CArrayDataProvider($ehren, array(
				'keyField'=>'squad_id',
				/* 'sort'=>array(
				 'attributes'=>array(
							'username', 'email',
				 ),
				),
		*/
				'pagination'=>array(
						'pageSize'=>25,
				),
		));		
		
			
		$this->render('squads',array(
				'squads'	=> $arrayDataProvider,
				'ehren'	 	=> $ehrenDataProvider
		));		
	}

	public function actionSquad($id) {
	
		$criteria = new CDbCriteria();
		$criteria->condition = 't.squad_id = :squad_id';
		$criteria->params = array(':squad_id'=>$id);
	
		$squad	= Squad::model()->with('user','user2squad')->find($criteria);
		$squad->setLeaderData();
	
		$arrayDataProvider=new CArrayDataProvider($squad->user, array(
				'keyField'=>'user_id',
				/* 'sort'=>array(
				 'attributes'=>array(
				 		'username', 'email',
				 ),
				),
		*/
				'pagination'=>array(
						'pageSize'=>100,
				),
		));

		$criteria = new CDbCriteria();
		$criteria->condition = 't.squad_id = :squad_id';
		$criteria->params = array(':squad_id'=>$id);
		
		$squad2	= Squad::model()->with('noMember','user2clanwar')->find($criteria);
		
		if($squad2 != null) {

				
			$ehemDataProvider=new CArrayDataProvider($squad2->noMember, array(
				'keyField'=>'user_id',
				/* 'sort'=>array(
				 'attributes'=>array(
				 		'username', 'email',
				 ),
				),
		*/
				'pagination'=>array(
						'pageSize'=>100,
				),
			));
		} else {
			$ehemDataProvider = false;
		}		

		$criteria = new CDbCriteria();
		$criteria->condition = 't.squad_id = :squad_id';
		$criteria->params = array(':squad_id'=>$id);
		
		//$squad3	= Squad::model()->with('noMember','user2clanwar')->find($criteria);
		$squad3 = null;
		if($squad3 != null) {
		
		
			$andereDataProvider=new CArrayDataProvider($squad3->noMember, array(
					'keyField'=>'user_id',
					/* 'sort'=>array(
					 'attributes'=>array(
					 		'username', 'email',
					 ),
					),
			*/
					'pagination'=>array(
							'pageSize'=>100,
					),
			));
		} else {
			$andereDataProvider = false;
		}		
		
		$relevanteNews 	= News::model()->holeRelevanteNews(3,$squad->squad_id,'squad');
		
		$this->render('squad',array(
				'member' 	=> $arrayDataProvider,
				'ehem' 		=> $ehemDataProvider,
				'andere'	=> $andereDataProvider,
				'squaddata' => $squad,
				'relevanteNews' => $relevanteNews,
				
		));
	}
	
	protected function gridMemberData($data,$row) {
		return $this->renderPartial('_memberData',array('data'=>$data),true);
	}
	
/*
	protected function gridForumAntwort($data,$row) {
		return $this->renderPartial('_forumAntwortZelle',array('data'=>$data),true);
	}
*/	
	
	
	
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