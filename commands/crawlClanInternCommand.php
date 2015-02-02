<?php
date_default_timezone_set('Europe/Berlin');

class crawlClanInternCommand extends CConsoleCommand {

	#protected $_basepath = 'C:/arsten/workspace/stv2/downloads/';///usr/home/www/tmv4/static/';
	public $content;
	public $iteration;
	
	public function actionCrawl() {
 	

		$file = 'clanintern.html';
		// Öffnet die Datei, um den vorhandenen Inhalt zu laden
		$fileData = '';
		
		$start = 1;
		$max = 30000;
		#$max = 10;
		
		$content = '';
		
		$gameArr = array();
		
		$tmp = '';
		
		$tmpContent = '';
		
		$failedContent = '';
		
		for($i = $start; $i < $max; $i++) {
			
			$this->iteration = $i;
			$url = 'http://www.clanintern.de/ewars.php4?clan='.$this->iteration;
			
			list($status) = get_headers($url);
			if (strpos($status,'200') !== false) {
				
				$string = $this->get_data($url);
				$find = 'Dieser Clan hat';
			
				if(strpos($string,$find)===false) {
		
					$find3 = '>CTF</td>';
		
					if(strpos($string,$find3)!==false) {
						$css = "font-weight:bold;";
					} else {
						$css = "font-weight:normal;";
					}
		
					$find2 = 'santitan';
					if(strpos($string,$find2)!==false) {
						$content.= "<a href=\"".$url."\" style='".$css."color:red;'>--> ".$url." <--</a><br />\n";
						$tmpContent.= "<a href=\"".$url."\" style='".$css."color:red;'>--> ".$url." <--</a>\n";
					} else {
						$content.= "<a href=\"".$url."\" style='".$css."'>".$url."</a><br />\n";
						$tmpContent.= "<a href=\"".$url."\" style='".$css."'>".$url."</a>\n";
					}
				}
			
				if($this->iteration%20==0) {
					echo $tmpContent;
					sleep(1);
					$tmpContent = '';
				}
			} else {
				$failedContent.= "<a href=\"".$url."\">".$url."</a><br />\n";				
			}
		
		}
			

/*		$c2 = '';
		foreach($gameArr as $k => $v) {
			$url = 'http://www.clanintern.de/ewars.php4?clan='.$i;
			$string = file_get_contents($url);
				
			$find = '>UT</td>';
				
			if(strpos($string,$find)!==false) {
				$c2.= "<a href=\"".$url."\">".$url."</a>\n";
			}
			
		}
*/		
		
		#$path = $this->_basepath.'/';
		/*
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}*/

		
		$fp = fopen(Yii::getPathOfAlias('application').'/../downloads/liste4.html','w');
		print_r($fp);
		fputs($fp, $content);
		fclose($fp);

		$fp = fopen(Yii::getPathOfAlias('application').'/../downloads/failed-liste4.html','w');
		print_r($fp);
		fputs($fp, $failedContent);
		fclose($fp);		
		
	}
	
	private function get_data($url){
	  $ch = curl_init();
	  $timeout = 5;
	  curl_setopt($ch,CURLOPT_URL,$url);
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	  $data = curl_exec($ch);
	  curl_close($ch);
	  return $data;
	}
}


?>