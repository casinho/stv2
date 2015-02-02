<?php
class newsBizRules {
	
	public static function istMeineNews($user_id) {

		if($user_id != Yii::app()->user->getId()) {
			return false;
		}
		return true;
	}
	
	/*
	 * Brauche ich dies hier wirklich?
	 */
	
	
	public static function istNewsMeinerSquadMitglieder($user_id=0) {
		
		$attributes['user_id'] 	= Yii::app()->user->getId();
		$attributes['squad_id'] = $squad_id;
		
		$squads = User2Squad::model()->findAllByAttributes($attributes);
		
		
		$sql = "SELECT * FROM user2squad WHERE user_id = ".Yii::app()->user->getId()." (leader_flag = 1 OR orga_flag = 1)";  
				
		$res = Yii::app()->db->createCommand($sql)->queryAll();
		
		if(empty($res)) {
			return false;
		} else {
			foreach($res as $k => $v) {
				$sql = "SELECT * FROM user2squad WHERE user_id = ".$user_id." AND squad_id = ".$v['squad_id']."";
				$ergo = Yii::app()->db->createCommand($sql)->queryRow();
				if(!empty($ergo)) {
					return true;
				}
			} 
		}

		return false;
	}
	
}
?>