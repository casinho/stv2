<?php
class squadBizRules {
	

	public static function istSquadLeader($squad_id=0) {
		
		if(Yii::app()->user->isGuest) {
			return false;
		}
		
		
		$attributes['user_id'] 	= Yii::app()->user->getId();
		$attributes['squad_id'] = $squad_id;
		
		$sql = "SELECT * FROM user2squad WHERE user_id = ".Yii::app()->user->getId()." AND squad_id = ".$squad_id." AND (leader_flag = 1 OR orga_flag = 1)";  
				
		$res = Yii::app()->db->createCommand($sql)->queryRow();

		if(!empty($res)) {
			return true;
		} else {
			return false;
		}
	}
	
	
}
?>