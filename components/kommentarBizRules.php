<?php
class kommentarBizRules {

	public static function holeMinutenDifferenzMeinesKommentares($startwert,$user_id,$limit=48000) {
		
		if($user_id != Yii::app()->user->getId()) {
			return false;
		}
		$differenz = time() - strtotime($startwert);
		
		$arr['dif'] = $differenz;
		$arr['time'] = time();
		$arr['startwert'] = strtotime($startwert);
		$arr['limit'] = $limit;
		
		if($differenz > $limit) {
			$output =  false;
		} else {
			$output =  true;
		}
	
		return $output;
	}

}
?>