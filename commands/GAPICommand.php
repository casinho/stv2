<?php
date_default_timezone_set('Europe/Berlin');
class GAPICommand extends CConsoleCommand {
	
	public function actionImport($args) {
		Yii::import('application.vendors.*');
		require_once('gapi-1.3/gapi.class.php');
		
		$ga_email = 'carsten.tetzlaff79@googlemail.com';
		$ga_password = 'imperator1979';
		$ga_profile_id = '89560314';
		//$ga_url = $_SERVER['REQUEST_URI'];
		
		$ga = new gapi($ga_email,$ga_password);
		
		$start_ts 	= time() - (60*60*24*15);
		$end_ts 	= time() - (60*60*24*1);
		
		$start_date = date('Y-m-d', $start_ts);
		$end_date 	= date('Y-m-d', $end_ts);		
		
		$ga->setDeveloperKey('AIzaSyCA5DoHBv1JSVsaQhE8jE4B-p0UNNSBhhg');
		
		$ga->requestReportData($ga_profile_id, array('day','week','month','year','date'), array('visits', 'pageviews', 'uniquePageviews', 'exitRate', 'avgTimeOnPage', 'entranceBounceRate'), 0, 0, $start_date, $end_date);
		$results = $ga->getResults();

		foreach($results as $result) {

			$datum = $this->formatDate($result->getDate());
			
			$model = StatistikGa::model()->findByPk($datum);
			
			if(empty($model)) {
				$model = new StatistikGa();
			}
			
			$model->day 				= $result->getDay();
			$model->week 				= $result->getWeek();
			$model->month 				= $result->getMonth();
			$model->year 				= $result->getYear();
			$model->datum				= $this->formatDate($result->getDate());
			$model->visits				= $result->getVisits();
			$model->pageviews	= $result->getPageviews();
			$model->unique_pageviews	= $result->getUniquepageviews();
			$model->avgtimeonpage		= $result->getAvgtimeonpage();
			$model->entrancebouncerate	= $result->getEntrancebouncerate();
			$model->exitrate			= $result->getExitrate();
			
			$model->save();
		}
		
		$ga = array();
		$ga['yesterday'] 	= StatistikGa::getVisitors('visits','yesterday');
		$ga['all'] 			= StatistikGa::getVisitors('visits','all');
		$ga['month'] 		= StatistikGa::getVisitors('visits','month');
		$ga['views'] 		= StatistikGa::getVisitors('pageviews','yesterday');
		if($ga['yesterday']>0 && $ga['views']>0) {
			$ga['ratio'] 	= $ga['yesterday']/$ga['views'];
		} else {
			$ga['ratio'] 	= 0;
		}
/*
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		$xml->addAttribute('gA', 'ga-values');
		$newsIntro = $newsXML->addChild('yesterday');
		$newsIntro->addAttribute('value', $ga['yesterday']);
		$newsIntro = $newsXML->addChild('all');
		$newsIntro->addAttribute('value', $ga['all']);
		$newsIntro = $newsXML->addChild('month');
		$newsIntro->addAttribute('value', $ga['month']);
		$newsIntro = $newsXML->addChild('views');
		$newsIntro->addAttribute('value', $ga['views']);
		$newsIntro = $newsXML->addChild('ratio');
		$newsIntro->addAttribute('value', $ga['ratio']);	

		
		
		$xml = new DOMDocument('1.0', 'UTF-8');
		$xml->formatOutput = true;
		
		$roo = $xml->createElement('rss');
		$roo->setAttribute('version', '2.0');
		$xml->appendChild($roo);
		
		$cha = $xml->createElement('channel');
		$roo->appendChild($cha);		
		
*/		
	}
	
	private function formatDate($date) {
		$y = substr($date, 0, -4);  // gibt "abcde" zurück
		$m = substr($date, 4, -2);  // gibt "cde" zurück
		$d = substr($date, 6);  // gibt false zurück
		$datum = $y.'-'.$m.'-'.$d;
		return $datum;
	}
}
?>