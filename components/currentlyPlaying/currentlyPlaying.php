<?php
class currentlyPlaying extends CWidget {
	
	public $type = false;
	public $limit = false;
	public $h1class = false;
	
	public function init() {
		return true;
	}
	
	public function run() {

		$xml = Utserver::model()->checkXML();
		
		$xml = false;
		
		if($xml !== false) {
		
			$i = 0;
			$spieler = array();
			
			
			
			foreach($xml as $k => $v) {
				
				if(isset($v->teams->team->player)) {
					$serverid 	= (int)$v->serverdata->serverid;
					$servername = (string)$v->serverdata->data;
					$map		= (string)$v->match->mapname;
				
					foreach($v->teams->team->player as $key => $value) {
						$xmlSpieler = $value;		
						$spieler[$i]['id'] 		= (string)$i;
						$spieler[$i]['nick'] 	= (string)$xmlSpieler;
						$attr = $value[0]->attributes();
	
						if((int)$attr['ping'] < 0) {
							$ping = '-';
						} else {
							$ping = (int)$attr['ping'];
						}
			
						$spieler[$i]['ping'] 	= $ping;
						$spieler[$i]['punkte'] 	= (int)$attr['punkte'];
						$spieler[$i]['serverid']= $serverid;
						$spieler[$i]['map']		= $map;
			
						$i+=1;
								
					}
				}
				
			}
			
			if(empty($spieler)) {
				return false;
			}
			
			$output = array();
			
			foreach($spieler as $k => $v) {
				
				if(strlen($v['nick']) >= 3 ) {
				
					$nick = GFunctions::validateForDB(trim($v['nick']));
					
					$nick = str_replace(array("st'","SanTiTan][","st.","|ST|"), '', $nick);
					
					$nick = utf8_encode($nick);
					
					$qry = "SELECT user_id FROM user WHERE member_flag > 0 AND user_nick = '".$nick."'";
					$user_id = Yii::app()->db->createCommand($qry)->queryScalar();
					
					if($user_id > 0) {
					
						$check = User::model()->findByPk($user_id);
						
						$server = Utserver::model()->findByPk($v['serverid']);
						
						if($check != null) {
							
							$t['{user}'] = $check->getHeadline();
							$t['{server}'] = $server->getHeadline();
							$t['{score}'] = $v['punkte'];
							
							$output[] = Yii::t('utserver','spieler_spielt_auf_server',$t);
						}
					}
				}
			}
			
			
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
			
			
			if(!empty($output) && $output !== false) {
				$this->render('currentlyPlaying',array('output'=>$output));
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
?>