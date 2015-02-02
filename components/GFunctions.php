<?php
class GFunctions {
	/**
	 * Wrapper-Methode zum ersetzen von Sonderzeichen,
	 * ausführen von Kleinschreibung und
	 * Abschneiden von Leerzeichen am anfang und e
	 * nde des Strings
	 * @var string Der zu normalisierende String
	 * @return string Der normalisierte String
	 */
	public static function normalisiereString($string = null) {
		if(empty($string)) {
			return '';
		}

		$multibyte = (function_exists('mb_strtolower')) ? true : false;
		$multibyte = false;
		
		$string = trim($string);
		
		$string = str_replace('°','',$string);		
		
		$string = GFunctions::entferneSonderzeichen($string);
		$string = ($multibyte) ? mb_strtolower($string) : strtolower($string);		
		
		return $string; 
	}
	
	/**
	 * Ersetzt alle "Non-Word-Character" ausser "-" (Minus) durch "-" (Minus).
	 * Ein Doppeltes "-" (Minus) ("--") wird anschliessend durch "-" ersetzt.
	 * @var string Der String in dem Sonderzeichen ersetzt werden sollen
	 * @return string Der String ohne Sonderzeichen
	 */
	public static function entferneSonderzeichen($string = null) {
		if(empty($string)) {
			return '';
		}
		// aus sonderzeichen html entitaeten erstellen
		$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
		
		// html entitaeten das "&", ";" und entitaetennamen abschneiden
		$string = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1' , $string);
		
		// html entitaeten, welche einen utf-8 code erhalten haben entfernen
		$string = preg_replace('~&#([0-9]{1,4});~i', '' , $string);
		
		// htmlentities ersetzt keine leerzeichen. Wir wollen aber ein "-" (Minus);
		//$sucheUndErsetze = array(' ' => '-');
		
		/*
		$sucheUndErsetze = array(
			'ü' => 'ue',
			'ö' => 'oe',
			'ä' => 'ae',
			'ß'	=> 'ss',
		);
		*/
		//$string = strtr($string, $sucheUndErsetze);
		
		// was jetzt noch an "non-word charactern" uebrig ist, einfach entfernen.
		// Leerzeichen zaehlen dazu.
		$string = preg_replace('#[^\w-]#ui', '-', $string);
		/*
		
		$string = preg_replace('~[^0-9a-z]+~i', '-', $string);
		*/
		
		return preg_replace('#(-){2,}#i', '-', $string);
	}
	
	
	public static function luceneCharSetter($string) {

		$string=str_replace("ü","#ue#",$string);
		$string=str_replace("ä","#ae#",$string);
		$string=str_replace("ö","#oe#",$string);
		$string=str_replace("Ü","#Ue#",$string);
		$string=str_replace("Ä","#Ae#",$string);
		$string=str_replace("Ö","#Oe#",$string);		
		$string=str_replace("ß","#ss#",$string);
		return $string;		
	}	

	public static function luceneCharGetter($string) {
		$string=str_replace("#ue#",'ü',$string);
		$string=str_replace("#ae#",'ä',$string);
		$string=str_replace("#oe#",'ö',$string);
		$string=str_replace("#Ue#",'Ü',$string);
		$string=str_replace("#Ae#",'Ä',$string);
		$string=str_replace("#Oe#",'Ö',$string);		
		$string=str_replace("#ss#",'ß',$string);
		return $string;			}
	
	
	public static function createRandomString($length = 5) {
		mt_srand((double)microtime()*1000000);
		$letter_small	= range('a','z');
		$letter_big   	= range('A','Z');
		$int		 	= range(0,9);
	
		// Anzahl der Arrays bestimmen
		$ls_max = count($letter_small)-1;
		$lb_max = count($letter_big)-1;
		$in_max = count($int)-1;
		$var_RandomString = '';
		for($i = 0; $i < $length; $i+=1) {
			$var_RandomString.= $letter_small[mt_rand(0,$ls_max)];
			$var_RandomString.= $letter_big[mt_rand(0,$lb_max)];
			$var_RandomString.= $int[mt_rand(0,$in_max)];
		}
		$output = str_shuffle($var_RandomString);
		return $output;
	}	
	
	function Slug($string) {
    	return preg_replace('~[^0-9a-z]+~i', '-', $string);
	}
	
	
	public static function shortText($string = NULL,$lenght = 500) {
		if(strlen($string) > $lenght) {
	        $string = substr($string,0,$lenght)."...";
	        $string_ende = strrchr($string, " ");
	        $string = str_replace($string_ende," ...", $string);
	    }
	    return $string;
	}


	public static function getDatenFuerSpieltagsAnzeigeBySaisonId($saisonId, $gesamtAnzeige = false,$saisonAnzeige = false) {
		$spieltagsAnzeige['gesamtAnzeige'] = $gesamtAnzeige;
		$spieltagsAnzeige['saisonAnzeige'] = $saisonAnzeige;
        $spieltagsAnzeige['aktuellerSpieltag'] = Spiel::model()->getNummerAktuellerSpieltag($saisonId);
        if($gesamtAnzeige == true) {
        	$spieltagsAnzeige['gewaehlterSpieltag'] = Yii::app()->request->getParam('spieltag', 35);
        } else {
        	$spieltagsAnzeige['gewaehlterSpieltag'] = Yii::app()->request->getParam('spieltag', $spieltagsAnzeige['aktuellerSpieltag']);
        }
        $saison = Saison::getAnzahlSpieltageBySaisonId($saisonId);
        $spieltagsAnzeige['anzahlSpieltage'] 	= $saison->anzahlSpieltage;
        $spieltagsAnzeige['saisonId'] 			= $saisonId;
        if($saisonAnzeige == true) {
        	$spieltagsAnzeige['saisons'] 		= Saison::model()->getAlleSaisons();
        }
        return $spieltagsAnzeige;
	}
	
	

	public static function getDatumZeit($timestamp,$dateWidth='medium',$timewidth='short') {
		$dateFormatter = Yii::app()->getDateFormatter();
		return $dateFormatter->formatDateTime($timestamp, $dateWidth, $timewidth);
	}	

	public static function setTableClass($status,$row,$class='') {
		
		if($row%2==0) {
			$css = 'odd pn';
		} else {
			$css = 'even pn';
		}
		
		if(empty($status)) {
			$css.= ' '.$class; 
		} 
		return $css;
	}
	
	public static function getUserLink($user_nick = 0, $showSystemUser = false, $htmlOptions = array()) {
		if(!empty($user_nick)) {
			$link =  CHtml::normalizeUrl(array('profil/index', 'user' => $user_nick));
			echo CHtml::link($user_nick,CHtml::encode($link),$htmlOptions);
			return true;
		} else {
			if($showSystemUser === true) {
				return 'System';
			}
		}
	}

	
	public static function getThemaIcon($flag) {
		
		switch($flag) {
			case 1:
				$img = ''; 
				break;
			case 2:
				$img = '';
				break;
			case 3:
				$img = '';
				break;
			default:
				$img = '/images/forum/diskussion.png';
		}
		return $img;	
	}
	
	public static function getThemaCSSClass($flag,$geschlossen = 0) {	
		$class = 'forum_sprite ';
		
		#echo "css: ".$flag;
		#echo "given: ".$flag;
		if($geschlossen != 0) {
			$class.= 'thema_geschlossen';
		} else {
			switch($flag) {
				case 1:
					$class.= 'thema_strittig'; 
					break;
				case 2:
					$class.= 'thema_umfrage';
					break;
				case 3:
					$class.= 'thema_wichtig';
					break;
				case 4:
					$class.= 'thema_geschlossen';
					break;				
				default:
					$class.= 'thema_diskussion';
			}
		}
		return $class;	
	}	
	
	public static function pre($data,$depth=10,$highlight=true) {
        CVarDumper::dump($data,$depth,$highlight);
	}
	
	
	public static function imageStatus($status,$ok=0) {
		
		if($status == $ok) {
			return CHtml::image('/images/icons/sign_tick.png');
		} else {
			return CHtml::image('/images/icons/sign_cancel.png');
		}
		
	}
	
	public static function object2array($obj) {
    	$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    	foreach ($_arr as $key => $val) {
        	$val = (is_array($val) || is_object($val)) ? object2array($val) : $val;
        	$arr[$key] = $val;
    	}
    	return $arr;
	}  	
	
	public static function secondMinute($seconds) {
		$minResult = floor($seconds/60);
		if($minResult < 10){
			$minResult = 0 . $minResult;
		}
		$secResult = ($seconds/60 - $minResult)*60;
		if($secResult < 10) {
			$secResult = 0 . round($secResult);
		} else { 
			$secResult = round($secResult); 
		}
		return $minResult.':'.$secResult;
	}
	
	public static function macheTimeStamp($input) {
		// input = YYYY-MM-DD-HH:II:SS
		
		if(strlen($input) == 10) {
			//$array 	= explode('-',$input);
			//$datum 	= trim($array[0]);
			$datum 	= $input;
			//$zeit	= trim($array[1]);
			$da		= explode('-',$datum);
			//$za		= explode(':',$zeit);
			return mktime(0,0,59,$da[1],$da[2],$da[0]);
		} else {
			echo 10;
		}
	}
	
	public static function min2sec($laenge,$trenner=':') {
	    $ms = explode($trenner,$laenge);
		$sekunden = ($ms[0]*60)+$ms[1];		
		return $sekunden;
	}
	
	public static function sec2min($laenge,$trenner=':') {
		return gmdate('i'.$trenner.'s', $laenge);		
	}	
	
	public static function clearWhiteSpace($string) {
		return preg_replace('/\s\s+/', ' ', $string);
	}
	
	public static function getIpAddress() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                        foreach (explode(',', $_SERVER[$key]) as $ip) {
                                $ip = trim($ip);
                                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                                        return $ip;
                                }
                        }
                }
        }
        return '0';
	}
	
	public static function validateForDB($val) {
		$suche	= array('@<script[^>]*?>.*?</script>@si',
						'/\\\/');
		$ersetze 	= array('',
						'');
		$val = preg_replace($suche,$ersetze,$val);
		/*
		 * War mist
		 * $val = preg_replace("/(.*)\s{3,}/","\n",$val);
		 */
		$val = strip_tags($val);
		$val = addslashes($val);
		$val = trim($val);
		return $val;
	}	
	
	public static function getChannel($channel = false) {
		if($channel === false || empty($channel)) {
			return '-';
		} else {
			if(strpos($channel,'#')===false) {
				$channel = '#'.$channel;
			} 		
			
			$channellink = str_replace('#', '', $channel);
			
			return CHtml::link($channel,'irc://irc.quakenet.org/'.$channellink,array('target'=>'_blank'));
		}
	}
	
	public static function getHomepage($site = false, $flag = false) {
		if($site === false || empty($site)) {
			return '-';
		} else {
			if(strpos($site,'http://')===false) {
				$site = 'http://'.$site;
			}

			$url = parse_url($site);
			if(isset($url['host'])) {				
				
				if($flag !== false) {
					if($flag == 0) {
						$site = 'http://web.archive.org/web/*/'.$site;
					}
				}
				
				
				return CHtml::link($url['host'],$site,array('target'=>'_blank'));
			} else {
				return $site;
			}
		}
	}	
	
	public static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
		if ($length == 0)
			return '';
	
		if (is_callable('mb_strlen')) {
			if (mb_strlen($string) > $length) {
				$length -= min($length, mb_strlen($etc));
				if (!$break_words && !$middle) {
					$string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length + 1));
				}
				if (!$middle) {
					return mb_substr($string, 0, $length) . $etc;
				} else {
					return mb_substr($string, 0, $length / 2) . $etc . mb_substr($string, - $length / 2);
				}
			} else {
				return $string;
			}
		} else {
			if (strlen($string) > $length) {
				$length -= min($length, strlen($etc));
				if (!$break_words && !$middle) {
					$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
				}
				if (!$middle) {
					return substr($string, 0, $length) . $etc;
				} else {
					return substr($string, 0, $length / 2) . $etc . substr($string, - $length / 2);
				}
			} else {
				return $string;
			}
		}
	}


	
	/**
	 * Finds path, relative to the given root folder, of all files and directories in the given directory and its sub-directories non recursively.
	* Will return an array of the form
	* array(
			*   'files' => [],
			*   'dirs'  => [],
			* )
	* @author sreekumar
	* @param string $root
	* @result array
	*/
	public static function read_all_files($root = '.',$filename = true){
		$files  = array('files'=>array(), 'dirs'=>array());
		$directories  = array();
		$last_letter  = $root[strlen($root)-1];
		$root  = ($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;
	
		$directories[]  = $root;
	
		while (sizeof($directories)) {
			$dir  = array_pop($directories);
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file == '.' || $file == '..') {
						continue;
					}
					if($filename == true) {
						$files['files'][]  = $file;
					} else {
						$file  = $dir.$file;
						if (is_dir($file)) {
							$directory_path = $file.DIRECTORY_SEPARATOR;
							array_push($directories, $directory_path);
							$files['dirs'][]  = $directory_path;
						} elseif (is_file($file)) {
							$files['files'][]  = $file;
						}
					}
				}
				closedir($handle);
			}
		}
	
		return $files;
	}

	public static function deleteDir($path) {
		$class_func = array(__CLASS__, __FUNCTION__);
		return is_file($path) ?
		@unlink($path) :
		array_map($class_func, glob($path.'/*')) == @rmdir($path);
	}	

	public static function getImageType($int=0) {
		switch($int) {
			case 1:
				$output = 'gif';
				break;
			case 2:
				$output = 'jpg';
				break;
			case 3:
				$output = 'png';
				break;
			case 4:
				$output = 'swf';
				break;
			default:
				$output = false;												
		}	
		return $output;	
	}
	
}
?>