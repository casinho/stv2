<?php
class boardBizRules {
	
	public static function holeMinutenDifferenzMeinesBeitrages($startwert,$user_id,$limit=1800) {

		if($user_id != Yii::app()->user->getId()) {
			return false;
		}
		$differenz = time() - strtotime($startwert);
		if($differenz > $limit) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function istModDesForums($forum_id=0) {
		
		$attributes['user_id'] 	= Yii::app()->user->getId();
		$attributes['forum_id'] = (int)$forum_id;
				
		$model = User2Forum::model()->findByAttributes($attributes);

		if($model != null) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
	/* 
	 * Beiträge als Lesenswert definieren
	 * - User soll Wertung auch wieder entfernen dürfen
	 */
	public static function istMeineWertung($user_id) {
		
		if($user_id != Yii::app()->user->getId()) {
			return false;
		} else {
			return true;
		}
		
	}
	
}
?>