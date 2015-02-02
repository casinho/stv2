<?php
class showServer extends CWidget {
	
	public $type = false;
	public $limit = false;
	public $h1class = false;
	
	public function init() {
		return true;
	}
	
	public function run() {

		$xml = Utserver::model()->checkXML();
		$output = array();
		#foreach($xml as $k => $v) {
			#GFunctions::pre($v);
			#die();
			#$output[] = $v;
			#if(!isset($output[$k]['image']) || $output[$k]['image']=='') {
				#$output[$k]['image'] = 'images/man.jpg';
			#}
			/*
			$output[$k]['image'] = '';
			
			$titel = $v->server->hostname;
			$titel.= ' ('.(int)$v->match->maxplayers.'/'.(int)$v->match->numpalyers.')';
			#$output[$k]['url'] = Yii::app()->createUrl('server/detail',array('id'=>$v['id'],'seo'=>GFunctions::normalisiereString($titel)));
			$output[$k]['titel'] = $titel;
			
			$text = $v->match->mapname;
			$text.= ' ('.$v->match->maxplayers.'/'.$v->match->numpalyers.')';
			$text.= CHtml::tag('br');
			
			GFunctions::pre($output);
			die();
			*/
		#}
		
		if($xml != false) {
			$this->render('showServer',array('xml'=>$xml));
		} else {
			//return false;
		}
	}
}
?>