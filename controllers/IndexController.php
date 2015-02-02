<?php
class IndexController extends Controller {

	public function actionIndex() {
		
		$criteria = new CDbCriteria();
    	$criteria->order = 'datum DESC';
    	$criteria->offset = 0;
    	$criteria->limit = 8;
    	
    	$dataProvider = new CActiveDataProvider('News',
    		array(
    			'criteria'  => $criteria,
    			'pagination' => array('pageSize' => 5,),
    			'totalItemCount' => 5,
    		)
    	);    	
    	
    	$data = $dataProvider->getData();
		
    	$hotNews = array();
    	$normalNews = array();
    	
    	foreach($data as $k => $v) {
    		if($k > 0) {
    			$hotNews[] = $v;
    		} else {
    			$normalNews = $v;
    		}
    	}
    	

    	$topNews = new CActiveDataProvider('News');
    	$topNews->setData($hotNews);    	
    	
    	/*
    	$news = new CActiveDataProvider('News');
    	$news->setData($normalNews);
    	*/
    	$news = $normalNews;

    	$aktuelleBeitraege = Forum::holeAktuelleBeitraege(7);
    	
    	$letzteKommentare  = KommentarZuweisung::holeLetzteKommentare(4);
    	
    	$ga = array();

    	$ga['yesterday'] 	= StatistikGa::getVisitors('visits','yesterday');
    	$ga['all'] 			= StatistikGa::getVisitors('visits','all');
    	$ga['month'] 		= StatistikGa::getVisitors('visits','month');
    	$ga['views'] 		= StatistikGa::getVisitors('pageviews','yesterday');
    	if($ga['yesterday']>0 && $ga['views']>0) {
    		$ga['ratio'] 	= $ga['views']/$ga['yesterday'];
    	} else {
    		$ga['ratio'] 	= 0;
    	}
    	
    	//GFunctions::pre($ga);
    	
    	Yii::app()->clientScript->registerMetaTag('Unreal Tournament, UT 99, SanTiTan, CTF, TDM, Capture The Flag, Team Death Match, SaHiB, daRth, Beurer, st, Clan, Online-Gaming, Ego-Shooter', 'keywords');
    	Yii::app()->clientScript->registerMetaTag('SanTiTan ist ein Unreal Tournament (UT 99) Clan. Gespielt wurden Capture The Flag (CTF), Team Death Match (TDM) und Assault. Bekannteste Member von SanTiTan aus der Generation Unreal Tournament sind SaHiB, daRth und Beurer.', 'description', null, array('lang' => 'de'));
    	
    	$this->render('index',compact('news','topNews','aktuelleBeitraege','letzteKommentare','ga','xml'));
    }
    
    
    protected function gridThreadIcon($data,$row) {
    	return $this->renderPartial('_threadTitelIcon',array('data'=>$data),true);
    }
        
    protected function gridForumTitel($data,$row) {
    	return $this->renderPartial('_forumTitelZelle',array('data'=>$data),true);
    }

    protected function gridForumAntwort($data,$row) {
    	return $this->renderPartial('_forumAntwortZelle',array('data'=>$data),true);
    }    
    
	public function actionError() {
		if($error=Yii::app()->errorHandler->error) {
			if(Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
				$this->render('error', $error);
			}
		}
	}    
    
}