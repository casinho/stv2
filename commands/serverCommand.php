<?php


class serverCommand extends CConsoleCommand {

			
	public function actionCheckServer() {
		
		try {

			Utserver::model()->checkXML(1);
			
			Utserver::model()->pushData();
	
		}catch(Exception $e) {
			print_r($e->getMessage());
		}

	}
	
	
	
	private function sanitize($input,$encode = true){
		if($encode === true) {
			//$input = utf8_encode($input);
		}
		//return htmlentities(strip_tags($input));
		return strip_tags($input);
	}	
	
	private function createCronEintrag($typ) {
		$status	= 1;	// 1 = OK
		$cron 	= 'Server eingetragen: '.$typ;
		$info   = 'Server wurden erfolgreich eingelesen';
		Cronjob::erstelleEintrag($cron,$this->start,$status,$info);		
	}
	
	
}
?>