<?php

//require_once(MAIN_DIR.'classes/openGraphNode/OpenGraphNode.php');

class StringParser extends CController {

	public $string;
	public $parse_flag;
	public $beitrag_flag;
	
	private $_language;
	private $_verboteneQuellen;
	
	
	public function __construct() {
		$this->_language		= Yii::app()->language;
		$this_verboteneQuellen 	= Yii::app()->params['verboteneQuelle'];
	}

	
	public function makeModerationsHinweis() {
		
		$newString = '<b style="font-size:16px;"><span class="icons_sprite mod-icon"></span> '.Yii::t('global','moderationshinweis_titel').'</b><br />';
		$newString.= '<div class="alert-box alert">';
		$newString.= $this->string;
		$newString.= '</div>';
		
		$this->string = $newString;
		
	}
	
	public function parseString() {
		if($this->parse_flag == 0) {
			$this->string = stripslashes($this->string);
			$this->string = nl2br($this->string);
			$this->string = $this->bbCode($this->string);
			$this->string = $this->replace_uri($this->string);
			
			
			if(strpos($this->string,'<b r />')!==false) {
				$this->string = str_replace('<b r />','<br />',$string);
			}			
			
		}
		
		$this->string = $this->comunioModifier($this->string);
		$this->string = $this->removeTags($this->string,'4-4-2');
		$this->string = $this->badWords($this->string);
		$this->string = $this->wordwrapText($this->string);
		$this->string = $this->spriteSmilies($this->string);
		
		if($this->parse_flag == 0) {
			$this->string = $this->replace_uri($this->string);
		}
		
		//TMFunctions::pre('StringParser->parse_flag: '.$this->parse_flag);
		
	} 
	
	public static function parseQuelle($quelle) {
		$url = parse_url($quelle);
		if(isset($url['scheme'],$url['host'])) {
			return CHtml::link($url['host'],$quelle,array('target'=>'_blank'));
		} else {
			return false; 
		}
		
	}
	
	/*
	 * Verweist auf die Posts des Users
	 */
	public static function parseVergehen($vergehen) {
		if(is_object($vergehen)) {
			$vergehen = $vergehen->attributes;
		}
		$url = parse_url($vergehen['direktlink']);
		if(!empty($vergehen['odata'])) {
			$data = unserialize($vergehen['odata']);
			
			$post = ForumPost::model(null,$data['board_id'])->findByPk($data['post_id']);
			
			if($post != null) {
				$link = ForumThread::getThread($data['board_id'],$data['thread_id'],$data['post_id']);
			} else {
				return false;
			}

		} elseif(isset($url['scheme'],$url['host'])) {
			/*
			 * TODO: hier muss ich den alten Link auseinander nehmen, da wir unterschiedliche Mappings haben
			*/
			if(isset($url['fragment']) && !empty($url['fragment'])) {
				$anzeige = $url['host'].' | '.$url['fragment'];
			} else {
				$anzeige = $url['host'];
			}
			$link = CHtml::link($anzeige,$vergehen['direktlink'],array('target'=>'_blank'));
		} else {
			return false;
		}
		return $link;
	
	}	
	
	public static function bbCode($input) {
		$neuertext = $input;
		if(strpos($neuertext,'[zitat')!==false || strpos($neuertext,'[quote')!==false || strpos($neuertext,'[source')!==false) {
			
			$neuertext = $input;
			$zitat = Yii::t('forum','zitat_von_user',array('{user}'=>''));
/*		
			if($var_lang == 'de') {
				$zitat = 'Zitat von ';
			} else {
				$zitat = 'Quote by ';
			}
*/			
			$regex = "/\[zitat=(.*)(link=(.*))?\](.*)/isU";
			$replace = "<span class=\"quote expand_me\"><a href=\"$3\" class=\"jaxbox dib mr5\"><img src=\"http://static.transfermarkt.net/bilder/board_icons/icon_post_reply.gif\" /></a><b><u>$zitat $1:</u></b><br /> $4";
		
			/*
			$search = "/\[zitat=(.*)(link=(.*))?\](.*)/isU";
			preg_match_all($regex, $search, $test);
			
			TMFunctions::pre($test);	
			*/
			
			$neuertext = preg_replace($regex, $replace, $neuertext);
			$neuertext = str_replace("[zitat]","<span  class=\"quote\"><u>Zitat</u></b><br />",$neuertext);
			$neuertext = str_replace("[/zitat]","</span>",$neuertext);
			$neuertext = preg_replace("/\[quote=(.*)\](.*)/isU", "<span class=\"quote expand_me\"><b><u>$zitat $1:</u></b><br />$2", $neuertext);
			$neuertext = str_replace("[quote]","<span  class=\"quote expand_me\"><u>Quote</u></b><br />",$neuertext);
			$neuertext = str_replace("[/quote]","</span>",$neuertext);
			$neuertext = preg_replace("/\[source=(.*)\](.*)/isU", "<span class=\"quote_source db mt5 mb5 p5 s10\"><b class=\"s10\">$1:</b><br />$2", $neuertext);
			$neuertext = str_replace("[source]","<span class=\"quote_source db mt5 mb5 p5 s10\"><br />",$neuertext);
			$neuertext = str_replace("[/source]","</span>",$neuertext);
			$neuertext = str_replace("<a href=\"\" class=\"jaxbox dib mr5\"><img src=\"http://static.transfermarkt.net/bilder/board_icons/icon_post_reply.gif\" /></a>","",$neuertext);
		}
		
		
		return $neuertext;
	}	
	
	public function replace_uri($str,$video=false) {
		$pattern = '#(^|[^\"=]{1})(http://|https://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
		$verwertung = preg_replace($pattern,"\\1<a href=\"\\2\\3\" class=\"intforum\" target=\"_blank\" style=\"background-color:transparent;\">\\2\\3</a>\\4",$str);
		return $this->urlCheck($verwertung,$video);
	}
	
	private function urlCheck($input,$video=false) {
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
	
		$arr_videos = array('youtube.com/watch','metacafe.com/watch','video.google.com/videoplay?','rutube.ru/tracks','video.mail.ru','en.sevenload.com/shows','www.revver.com/video','veoh.com/watch','vimeo.com/','smotri.com/video','vkontakte.ru/vid','video.qip.ru/video','myvideo.de/watch','www.collegehumor.com/video','transfermarkt.tv');
	
		if(preg_match_all("/$regexp/siU", $input, $matches, PREG_SET_ORDER)) {
				
			foreach($matches as $match) {
				// $match[2] = link address
				// $match[3] = link text
	
				if(strpos($match[2],'transfermarkt.')===false) {
					$find_me 	= 'href="'.$match[2].'" class="intforum"';
					if($video!==false) {
						foreach($arr_videos as $key => $value) {
							if(strpos($match[2],$value)!==false) {
								$replace	= 'href="'.$match[2].'" class="intforum track_me vidbox"';
								break;
							} else {
								$replace	= 'href="'.$match[2].'" class="intforum track_me"';
							}
						}
					} else {
						if(strpos($match[2],'goal.com')!==false) {
							$find_me 	= $match[0];
							$replace	= $match[2];
							//							$input		= str_replace($find_me,$replace,$input);
						} else {
							$replace	= 'href="'.$match[2].'" class="intforum track_me"';
						}
					}
	
					$input	= str_replace($find_me,$replace,$input);
				} else {
					if(strpos($match[2],'transfermarkt.tv')!==false && strpos($match[2],'anzeigen/video')!==false) {
						$find_me 	= 'href="'.$match[2].'" class="intforum"';
						$replace	= 'href="'.$match[2].'" class="intforum vidbox"';
						$input		= str_replace($find_me,$replace,$input);
					}
				}
			}
				
		}
		return $input;
	}

	public static function comunioModifier($input) {
		$output = str_replace("href=\"http://www.comunio.de\"","href=\"http://www.comunio.de/index.phtml?partnerid=46\"",$input);
		return $output;
	}
	
	public static function removeTags($input,$remove) {
		if(strpos($input,$remove)!==false) {
			$input = preg_replace('_\s*<a.+?href=".*?'.$remove.'.*?".*?>.*?</a>\s*_im', ' ', $input);
		}
		return $input;
	}
	
	public static function getVerboteneQuelle() {
		return excplode(', ',$this->_verboteneQuellen);
	}
	
	public static function badWords($input) {
		$string = $input;
		#4-4-2 das nächste mal hinzufügen
		// blogspot auf userwunsch raus ( 11. Juni 2012 ), ct
		$badwords = explode(',',Yii::t('global','badwords'));
		$count_words = count($badwords);
		$a = 0;
		while ($a < $count_words) {
			$string = str_ireplace($badwords[$a], ' ***** ', $string);
			$a++;
		}
		return $string;
	}
	
	public static function wordwrapText($input, $max = 80, $break = ' ') {
		$arr = explode('<a', $input);
		$arr[0] = preg_replace('/([^\s]{'.$max.'})/i',"$1$break",$arr[0]);
		for($i = 1; $i < count($arr); $i++) {
			$arr2 = explode('</a>', $arr[$i]);
			if(isset($arr2[1])) {
				$arr2[1] = preg_replace('/([^\s]{'.$max.'})/i',"$1$break",$arr2[1]);
			}
			$arr[$i] = join('</a>', $arr2);
			
		}
		return join('<a', $arr);
	}
	
	public static function spriteSmilies($input) {
		$output = $input;
		$output = str_replace(':)','<span class="smilie sprite_smile" title=":)">&nbsp;</span>',$output);
		$output = str_replace(':-)','<span class="smilie sprite_smile" title=":)">&nbsp;</span>',$output);
		$output = str_replace(':cool','<span class="smilie sprite_cool" title=":cool">&nbsp;</span>',$output);
		$output = str_replace(';)','<span class="smilie sprite_zwinker" title=";)">&nbsp;</span>',$output);
		$output = str_replace(';-)','<span class="smilie sprite_zwinker" title=":)">&nbsp;</span>',$output);
		$output = str_replace(':o','<span class="smilie sprite_oops" title=":o">&nbsp;</span>',$output);
		$output = str_replace(':rolleyes','<span class="smilie sprite_rolleyes" title=":rolleyes">&nbsp;</span>',$output);
		$output = str_replace(':grrr','<span class="smilie sprite_grrr" title=":grrr">&nbsp;</span>',$output);
		$output = str_replace(':D','<span class="smilie sprite_grins" title=":D">&nbsp;</span>',$output);
		$output = str_replace(':ugly','<span class="smilie sprite_ugly" title=":ugly">&nbsp;</span>',$output);
		$output = str_replace(':p','<span class="smilie sprite_tung" title=":p">&nbsp;</span>',$output);
		$output = str_replace(':P','<span class="smilie sprite_tung" title=":P">&nbsp;</span>',$output);
		$output = str_replace(':angry','<span class="smilie sprite_angry" title=":angry">&nbsp;</span>',$output);
		$output = str_replace(':(','<span class="smilie sprite_sad" title=":(">&nbsp;</span>',$output);
		$output = str_replace(':-(','<span class="smilie sprite_sad" title=":(">&nbsp;</span>',$output);
		$output = str_replace(':/:','<span class="smilie sprite_schief" title=":/:">&nbsp;</span>',$output);
		return $output;
	}
	
	public static function smilies($input) {
		$output = $input;
		$output = str_replace(':)','<img src="http://www.transfermarkt.de/img/smilies/smile.gif" border="0" width="15" height="15"  class="vm"  alt=":)" title=":)" />',$output);
		$output = str_replace(':cool','<img src="http://www.transfermarkt.de/img/smilies/cool.gif" border="0" width="15" height="15"   class="vm"  alt=":cool" title=":cool" />',$output);
		$output = str_replace(';)','<img src="http://www.transfermarkt.de/img/smilies/zwinker.gif" border="0" width="15" height="15"   class="vm"  alt=";)" title=";)" />',$output);
		$output = str_replace(':o','<img src="http://www.transfermarkt.de/img/smilies/oops.gif" border="0" width="15" height="15"  class="vm"  alt=":o" title=":o" />',$output);
		$output = str_replace(':rolleyes','<img src="http://www.transfermarkt.de/img/smilies/rolleyes.gif" border="0" width="15" height="15"  class="vm"  alt=":rolleyes" title=":rolleyes" />',$output);
		$output = str_replace(':grrr','<img src="http://www.transfermarkt.de/img/smilies/grrr.gif" border="0" width="15" height="15"  class="vm"  alt=":grrr" title=":grrr" />',$output);
		$output = str_replace(':D','<img src="http://www.transfermarkt.de/img/smilies/grins.gif" border="0" width="15" height="15"  class="vm"  alt=":D" title=":D" />',$output);
		$output = str_replace(':ugly','<img src="http://www.transfermarkt.de/img/smilies/ugly.gif" border="0" width="15" height="15"  class="vm"  alt=":ugly" title=":ugly" />',$output);
		$output = str_replace(':p','<img src="http://www.transfermarkt.de/img/smilies/tung.gif" border="0" width="15" height="15"  class="vm"  alt=":p" title=":p" />',$output);
		$output = str_replace(':P','<img src="http://www.transfermarkt.de/img/smilies/tung.gif" border="0" width="15" height="15"  class="vm"  alt=":p" title=":p" />',$output);
		$output = str_replace(':angry','<img src="http://www.transfermarkt.de/img/smilies/angry.gif" border="0" width="15" height="15"  class="vm"  alt=":angry" title=":angry" />',$output);
		$output = str_replace(':(','<img src="http://www.transfermarkt.de/img/smilies/sad.gif" border="0" width="15" height="15"  class="vm"  alt=":()" title=":(" />',$output);
		$output = str_replace(':/:','<img src="http://www.transfermarkt.de/img/smilies/schief.gif" border="0" width="15" height="15"  class="vm"   alt=":/" title=":/" />',$output);
		return $output;
	}
	
	public static function smiliesTicker($input) {
		$output = $input;
		$output = str_replace(':)','<img src="http://static.transfermarkt.net/img/smilies/liveticker/smile.gif" border="0" width="12" height="12"  class="vm"  alt=":)" title=":)" />',$output);
		$output = str_replace(';)','<img src="http://static.transfermarkt.net/img/smilies/liveticker/zwinker.gif" border="0" width="12" height="12"   class="vm"  alt=";)" title=";)" />',$output);
		$output = str_replace(':o','<img src="http://static.transfermarkt.net/img/smilies/liveticker/oops.gif" border="0" width="12" height="12"  class="vm"  alt=":o" title=":o" />',$output);
		$output = str_replace(':O','<img src="http://static.transfermarkt.net/img/smilies/liveticker/oops.gif" border="0" width="12" height="12"  class="vm"  alt=":o" title=":o" />',$output);
		$output = str_replace(':D','<img src="http://static.transfermarkt.net/img/smilies/liveticker/grins.gif" border="0" width="12" height="12"  class="vm"  alt=":D" title=":D" />',$output);
		$output = str_replace(':ugly','<img src="http://static.transfermarkt.net/img/smilies/liveticker/ugly.gif" border="0" width="12" height="12"  class="vm"  alt=":ugly" title=":ugly" />',$output);
		$output = str_replace(':p','<img src="http://static.transfermarkt.net/img/smilies/liveticker/tung.gif" border="0" width="12" height="12"  class="vm"  alt=":p" title=":p" />',$output);
		$output = str_replace(':P','<img src="http://static.transfermarkt.net/img/smilies/liveticker/tung.gif" border="0" width="12" height="12"  class="vm"  alt=":p" title=":p" />',$output);
		$output = str_replace(':(','<img src="http://static.transfermarkt.net/img/smilies/liveticker/sad.gif" border="0" width="12" height="12"  class="vm"  alt=":()" title=":(" />',$output);
		return $output;
	}	
	
	public static function replaceLines($input,$max_lines = 3) {
		$output = preg_replace("/(.*)\s{".$max_lines.",}/","\n",$input);
		#$output = preg_replace("/\n{".$max_lines.",}/","\n\n",$input);
		return $output;
	}	
	
/* old & testing stuff **/	

	public function replaceString($findme,$new_string,$input,$preg=false) {
		$output = str_replace($findme,'http://www.'.$new_string.'.com',$input);
		$output = str_replace($findme,'http://www.'.$new_string.'.ch',$input);
		$output = str_replace($findme,'http://www.'.$new_string.'.com',$input);
		$output = str_replace($findme,'http://www.'.$new_string.'.ch',$input);
/*
		if($preg==false) {
			return str_replace($findme,$new_string,$input);
		} else {
#			echo $input." - ".$findme."<br /><br /><br /><br /><br /><br />";
			if(strpos($input,$findme)!==false) {
				$input = preg_replace("#(http://)?(www.)?(".$findme.".*)?#","",$input);
			}
			return $input;
		}
*/		
	}	
	
	
	private function makeShortLinks($input) {
		$value = $input;
		preg_match_all('/<a (.*?)>(.*?)<\\/a>/i', $value, $out);
		if(strlen($out[2][0]) > 55) {
			$url = substr($out[2][0],0,55)."...";
		}
		return $input;
	}
	
	private function urlModifier($input) {
		$check = "?";
		if(strstr($input,$check)) {
			$output = $input."&amp;partnerid=46";
		} else {
			if(strpos($input,".phtml")!==false) {
				$output = $input."?partnerid=46";
			} else {
				$output = $input."/index.phtml?partnerid=46";
			}
		}
		return $output;
	}
	
	
	
	public function replace_uri_og($str,$video=false) {
  		$pattern = '#(^|[^\"=]{1})(http://|https://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
  		return $this->checkOG(preg_replace($pattern,"\\1<a href=\"\\2\\3\" class=\"intforum\" target=\"_blank\" style=\"background-color:transparent;\">\\2\\3</a>\\4",$str),$video);
	} 	
	
	public function replace_uri_stars($str) {
  		$pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:|www)([^\s<>]+)([\s\n<>]|$)#sm';
		return preg_replace($pattern," *** Links in Signaturen sind nicht gestattet *** ",$str);
		#return '*** Links sind verboten ***';

	} 

	public function checkOG($str) {
		
		try {
		
			$page 	= $str;
			$node 	= new OpenGraphNode($page);
			$all 	= $node->ALL();
			
			//print_r($all);
			
			$output = '<div>';
			
			if(isset($all['image']) && !empty($all['image'])) {
				$output.= '<img src="'.$all['image'].'" class="fl mr5" width="80" />';
			}

			//print_r($all);
			
			if(isset($all['title']) && !empty($all['title'])) {
				if(isset($all['url']) && !empty($all['url'])) {
					$output.= '<a href="'.$all['url'].'" target="_blank" class="fb">'.$all['title'].'</a>';
				} else {
					$output.= '<b>'.$all['title'].'</b>';	
				}
				
				if(isset($all['sitename']) && !empty($all['sitename'])) {
					$host = functions::getDomain($str);
					$output.= '<a href="'.$host.'" target="_blank">'.$all['sitename'].'</a>';
				}	
				$output.= '</div>';		
			//	echo "<b>asldkjfalksdjfasdf</b>";	
			} else {
				//echo "FUCKEC";
				$output = false;
			}			

			return $output;
			
			/*
			
			foreach($all as $key => $value) {
				echo $key.": ".$value."<br />";
			}*/
			
		} catch(Exception $e) {
			//var_dump($e);
		}
	}
}
?>