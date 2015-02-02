<?php

/**
 * This is the model class for table "utserver".
 *
 * The followings are the available columns in table 'utserver':
 * @property integer $serverid
 * @property integer $categoryid
 * @property string $ip
 * @property string $port
 * @property string $name
 * @property string $quick
 * @property string $poster_id
 */
class Utserver extends CActiveRecord {
	
	public $server_udp;//"udp://".$row["ip"]."";
	public $qport; // $row["port"]+1;
	public $timed_out = false;
	public $serverdata;
	public $serverdatalen = 0;
	public $waittime = 200;
	
	public $serverData = array();
	
	public $server_ip;
	
	public $xml_file;
	
	public $conn_id;
	
	private $_MinutenLimit = 2;
	
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'utserver';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('categoryid', 'numerical', 'integerOnly'=>true),
			array('ip', 'length', 'max'=>15),
			array('port', 'length', 'max'=>10),
			array('name', 'length', 'max'=>255),
			array('quick, poster_id', 'length', 'max'=>5),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('serverid, categoryid, ip, port, name, quick, poster_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'serverid' => 'Serverid',
			'categoryid' => 'Categoryid',
			'ip' => 'Ip',
			'port' => 'Port',
			'name' => 'Name',
			'quick' => 'Quick',
			'poster_id' => 'Poster',
		);
	}
	
	public function holeAlleServer() {
	
		$sort = new CSort();
		$sort->defaultOrder = 's.name ASC';
		$sort->attributes = array(
				'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl DESC',
				),

				'name' => array(
						'asc'=>'s.name',
						'desc'=>'s.name DESC',
				),
				'ip' => array(
						'asc'=>'s.ip',
						'desc'=>'s.ip DESC',
				),
				'quick' => array(
						'asc'=>'s.quick',
						'desc'=>'s.quicl DESC',
				),
				'user' => array(
						'asc'=>'u.user_nick',
						'desc'=>'u.user_nick DESC',
				),
				
		);
	
		$sql 		= "SELECT COUNT(*) FROM utserver";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
	
		$sql		= "SELECT s.*,CONCAT(s.ip,':',s.port) AS server_ip, COUNT(k.kommentar_id) AS anzahl, u.user_nick, u.user_id FROM utserver AS s LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = s.serverid AND k.zuweisung = 'utserver' LEFT JOIN user AS u ON u.user_id = s.poster_id GROUP BY s.serverid";
		$output 	= new CSqlDataProvider($sql,array(
				'keyField' => 'serverid',
				'totalItemCount' => $anzahl,
				'sort' => $sort,
				'pagination' => array(
						'pageSize' => 20
				)
		)
		);
		return $output;
	}
	

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search() {
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('serverid',$this->serverid);
		$criteria->compare('categoryid',$this->categoryid);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('port',$this->port,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('quick',$this->quick,true);
		$criteria->compare('poster_id',$this->poster_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


	public function getServerData() {
		
		$this->server_udp 	= "udp://".$this->ip;
		$this->qport		= $this->port+1;
		$this->timed_out 	= false;
		
		if ($this->waittime< 500) {
			$this->waittime = 5000;
		}
		if ($this->waittime>2000) {
			$this->waittime=20000;
		}
		$this->waittime=doubleval($this->waittime/1000.0);
		

		#print_r($this->ip);
		#echo PHP_EOL;
		#print_r($this->port);
		#echo PHP_EOL;
		#echo PHP_EOL;
		
		$fp = fsockopen($this->server_udp, $this->qport, $errno, $errstr, 30);
		if (!$fp) {
			//GFunctions::pre(array($errno=>$errstr));
		} else {
			
		
			socket_set_blocking($fp,true);
			socket_set_timeout($fp,0,500000);
			$command = "\\status\\";
			
			fwrite($fp,$command,strlen($command));
			// Mark
			$starttime = time();
			
			$serverdatalen = 0;
			
		
			do {
				$this->serverdata.=utf8_encode(fgetc($fp));
				$serverdatalen++;
				$socketstatus=socket_get_status($fp);
				
				$this->serverdata = str_replace("/!\\",'###',$this->serverdata);
				
				if (time()>($starttime+$this->waittime)) {
					$this->timed_out = true;
					break;
				}
			} while (substr($this->serverdata,strlen($this->serverdata)-7)!="\\final\\");
				fclose($fp);
				$s = $this->serverdata;
				$splitted = explode("\\", $s);
				#GFunctions::pre($splitted);
				#die();
				for ($i=1; $i < count($splitted); $i=$i+2) {
					if(isset($splitted[$i],$splitted[$i+1])) {
						$_gameinfo["$splitted[$i]"]=str_replace("###",'a',$splitted[$i+1]);
					}
				}
	
				#GFunctions::pre($_gameinfo);
				
				if (!$this->timed_out) {
					$this->setBasisData($_gameinfo);
					$this->setMatchData($_gameinfo);
				}
		}
	}
	
	
	
	private function setBasisData($_gameinfo) {
		
		$fields = array('gamever','hostname','hostport','AdminName','AdminEMail');
		
		foreach($fields as $k => $v) {
			if(isset($_gameinfo[$v])) {
				$this->serverData['server'][$v] = $_gameinfo[$v]; 
			} else {
				$this->serverData['server'][$v] = false;
			}
		}
		$this->serverData['server']['ip'] = $this->ip;
		$this->serverData['server']['serverid'] = $this->serverid;
		
	}

	private function setMatchData($_gameinfo) {
	
		$fields = array('mapname','gametype','numplayers','maxplayers','gamemode','timelimit','fraglimit','minplayers','mutators','map');
	
		foreach($fields as $k => $v) {
			if(isset($_gameinfo[$v])) {
				$this->serverData['match'][$v] = $_gameinfo[$v];
			} else {
				$this->serverData['match'][$v] = false;
			}
		}
		
		if($this->serverData['match']['numplayers'] > 0) {
			$this->setPlayerData($_gameinfo);
		}
	
	}
	
	private function setPlayerData($_gameinfo) {
		
		$anz = $_gameinfo['numplayers'];
		
		$this->serverData['players']['teams'][1] = array();

		for($i = 0; $i < $anz; $i++) {
			if(isset($_gameinfo['player_'.$i])) {
				$this->serverData['players']['teams'][1][] = array('nick' => $_gameinfo['player_'.$i], 'ping' => $_gameinfo['ping_'.$i], 'punkte' => $_gameinfo['frags_'.$i]);
			}
		}
			
	}
	
	public function test() {
		//echo "ok";
	}
	
	
	public function checkXML($server = false) {
		
		$this->xml_file = Yii::getPathOfAlias('application').'/../downloads/server.xml';

		if($server !== false) {

			$array = array();
			
			try {
			
				if($serverlist = @file_get_contents('http://www.santitan.de/downloads/serverips.txt')) {
					$serverips = explode(',',$serverlist);
					
					foreach($serverips as $k => $v) {
						$atts = explode(':',$v);
						if(isset($atts[0]) && !empty($atts[0])) {
							$array[] = array('ip'=>$atts[0],'port'=>(int)$atts[1],'qport'=>(int)$atts[1]+1,'serverid'=>(int)$atts[2]);
						}
					}				
				}
			} catch(Exception  $e) {
				//print_r($e);
			}
			
			//print_r($array);
			
			$this->buildXML($array);
			return true;
		}

		if (is_file($this->xml_file)) {
			
			if($xml = simplexml_load_file($this->xml_file)) {
				
				if($attribute = $xml->attributes()) {
					if(isset($attribute->time)) {
						$limit = time()-60*$this->_MinutenLimit;
						if($limit > $attribute->time) {
							return false;
							//$this->buildXML_FTP();
						}
						return $xml;							
					} else {
						return false;
					} 
				} else {
					return false;
				}
				
			} 
			
		} else {
			return false;
		}
		
	}

	public function pushData() {
		
		$ftp_server = 'www.arnoldt.de';
		$ftp_user_name = '20900f9458u6';
		$ftp_user_pass = 'futnap69';
			
		// Variablen definieren
		$server_file = '/downloads/server.xml';
		$local_file = Yii::getPathOfAlias('application').'/../downloads/server.xml';
			
		// Verbindung aufbauen
		$conn_id = ftp_connect($ftp_server);
		
		if($conn_id == false) {
			$msg = array('Konnte nicht zum FTP verbinden', 'errors', 'models.utserver -> getFtpData()');
			GFunctions::pre($msg);
			Yii::log($msg);
			return false;
		}
			
		// Login mit Benutzername und Passwort
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		if($login_result == false) {
			$msg = array('Konnte nicht auf FTP einloggen', 'errors', 'models.utserver -> getFtpData()');
			GFunctions::pre($msg);
			Yii::log($msg);
		
			return false;
		}
			
		// Versuche $server_file hochzuladen und in $local_file zu speichern
		if (ftp_put($conn_id, $server_file, $local_file, FTP_BINARY)) {
			$output = $local_file;
		
		} else {
			$output = false;
			$msg = array('server.xml konnte nicht gelesen werden', 'errors', 'models.utserver -> getFtpData()');
			GFunctions::pre($msg);
			Yii::log($msg);
		}
		// Verbindung schließen
		ftp_close($conn_id);
		return $output;		
	}
	
	private function getFtpData() {
	
		if(Yii::app()->getBaseUrl(true) == 'http://st.carsten-tetzlaff.de') {
			$this->buildXML();
			return false;
		}
		
		
		$ftp_server = '87.106.40.246';
		$ftp_user_name = 'st352acc79ess';
		$ftp_user_pass = '34xc_Wsp98cHjx';
		 
		// Variablen definieren
		$server_file = 'server.xml';
		$local_file = Yii::getPathOfAlias('application').'/../downloads/server.xml';
		 
		// Verbindung aufbauen
		$conn_id = ftp_connect($ftp_server);

		if($conn_id == false) {
			$msg = array('Konnte nicht zum FTP verbinden', 'errors', 'models.utserver -> getFtpData()');
			#GFunctions::pre($msg);
			Yii::log($msg);
			return false;			
		}
		 
		// Login mit Benutzername und Passwort
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		if($login_result == false) {
			$msg = array('Konnte nicht auf FTP einloggen', 'errors', 'models.utserver -> getFtpData()');
			#GFunctions::pre($msg);
			Yii::log($msg);
				
			return false;			
		}
		 
		// Versuche $server_file herunterzuladen und in $local_file zu speichern
		if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
			$output = $local_file;

		} else {
			$output = false;
			$msg = array('server.xml konnte nicht gelesen werden', 'errors', 'models.utserver -> getFtpData()');
			#GFunctions::pre($msg);
			Yii::log($msg);			
		}
		// Verbindung schließen
		ftp_close($conn_id);
		return $output;
	}	
	
	
	

	public function buildXML($server = false) {
		
		if($server == false && empty($server)) {
			$criteria = new CDbCriteria();
			#$criteria->limit = 5;
		
			$server = Utserver::model()->findAll($criteria);
			$array = array();
			
			foreach($server as $k => $v) {
				$v->getServerData();
				$array[] = $v->serverData;
			}			
			
		} else {
			
			foreach($server as $k => $v) {
				$model = new Utserver();
				$model->serverid= $v['serverid'];
				$model->ip 		= $v['ip'];
				$model->port 	= $v['port'];
				$model->qport 	= $v['qport'];
				$model->getServerData();
				$array[] = $model->serverData;
			}
			
		}

	
		
		shuffle($array);
		
		$creator = new Utserver();
		$creator->createXML($array);
	}	

	public function buildXML_FTP() {
		$file = $this->getFtpData();
		return $file;
	}	
	
	public function createXML($array) {
		
		$doc = new DomDocument('1.0');
		$doc->encoding = 'UTF-8';
		$doc->formatOutput = true;
		$doc->preserveWhiteSpace = false;	

		$root = $doc->createElement("serverlist");
		$valid_attr = $doc->createAttribute('time');
		$valid_attr->value = trim(time());
		$root->appendChild($valid_attr);
		
		$doc->appendChild($root);


		if (!empty($array)) {
		
			foreach($array as $k) {

				$server = $doc->createElement("server");
				
				
				foreach($k as $key => $value) {
					
					if($key == 'server') {
						
						$valid_attr = $doc->createAttribute('serverid');
						$valid_attr->value = trim($value['serverid']);
						$server->appendChild($valid_attr);						

						$serverdata = $doc->createElement("serverdata");
						
						$servername = $doc->createElement("data");
						$servername->appendChild($doc->createTextNode($value['hostname']));
						$serverdata->appendChild($servername);
						
						$serverip = $doc->createElement("ip");
						$serverip->appendChild($doc->createTextNode($value['ip']));
						$serverdata->appendChild($serverip);						

						$serverid = $doc->createElement("serverid");
						$serverid->appendChild($doc->createTextNode($value['serverid']));
						$serverdata->appendChild($serverid);						
						
						$serverport = $doc->createElement("port");
						$serverport->appendChild($doc->createTextNode($value['hostport']));
						$serverdata->appendChild($serverport);

						$serveradmin = $doc->createElement("admin");
						$serveradmin->appendChild($doc->createTextNode($value['AdminName']));
						$serverdata->appendChild($serveradmin);

						$servermail = $doc->createElement("adminmail");
						$servermail->appendChild($doc->createTextNode($value['AdminEMail']));
						$serverdata->appendChild($servermail);						
						
						$server->appendChild($serverdata);
						
					} elseif($key == 'match') {
						
						$match = $doc->createElement("match");
							
						$map = $doc->createElement("mapname");
						$map->appendChild($doc->createTextNode($value['mapname']));
						$match->appendChild($map);

						$file = File::model()->findByAttributes(array('name'=>$value['mapname']));
						if($file != null) {
							$map_bild = $file->bild;
						} else {
							$map_bild = false;
						}
						$model = $doc->createElement("map_bild");
						$model->appendChild($doc->createTextNode($map_bild));
						$match->appendChild($model);							
												
						$gametype = $doc->createElement("gametype");
						$gametype->appendChild($doc->createTextNode($value['gametype']));
						$match->appendChild($gametype);

						$numplayers = $doc->createElement("numplayers");
						$numplayers->appendChild($doc->createTextNode($value['numplayers']));
						$match->appendChild($numplayers);						
						
						$maxplayers = $doc->createElement("maxplayers");
						$maxplayers->appendChild($doc->createTextNode($value['maxplayers']));
						$match->appendChild($maxplayers);

						$gamemode = $doc->createElement("gamemode");
						$gamemode->appendChild($doc->createTextNode($value['gamemode']));
						$match->appendChild($gamemode);

						$timelimit = $doc->createElement("timelimit");
						$timelimit->appendChild($doc->createTextNode($value['timelimit']));
						$match->appendChild($timelimit);

						$fraglimit = $doc->createElement("fraglimit");
						$fraglimit->appendChild($doc->createTextNode($value['fraglimit']));
						$match->appendChild($fraglimit);						

						$mutators = $doc->createElement("mutators");
						$mutators->appendChild($doc->createTextNode(htmlspecialchars($value['mutators'], ENT_QUOTES, 'UTF-8')));
						$match->appendChild($mutators);						
						
						$server->appendChild($match);
						
					} elseif($key == 'players') {
						
						$teams = $doc->createElement("teams");
						
						foreach($value['teams'] as $kk) {
							
							$team = $doc->createElement("team");
							
							foreach($kk as $tk => $player) {
								$spieler = $doc->createElement("player");
								$spieler->appendChild($doc->createTextNode($player['nick']));

								$valid_attr = $doc->createAttribute('ping');
								$valid_attr->value = trim($player['ping']);
								$spieler->appendChild($valid_attr);			

								$valid_attr = $doc->createAttribute('punkte');
								$valid_attr->value = $player['punkte'];
								$spieler->appendChild($valid_attr);

								$team->appendChild($spieler);
							}
							
							$teams->appendChild($team);
							
							
						}
						
						$server->appendChild($teams);
					}
				}
				
				$root->appendChild($server);
				
			}
		}

		$this->xml_file = Yii::getPathOfAlias('application').'/../downloads/server.xml';		
		
		chmod($this->xml_file, octdec(0755));
		$doc->save($this->xml_file);	

		
		
	}
	
	

	protected function afterFind() 	{
		// convert to display format
		$this->server_ip = $this->ip.':'.$this->port;
		parent::afterFind ();
	}
	
	protected function afterSave() {
		
		$server = Utserver::model()->findAll();
		
		$file = Yii::getPathOfAlias('application').'/../downloads/serverips.txt';
		
		//$myfile = fopen($file, "w") or die("Unable to open file!");
		$myfile = fopen($file, "w") or die("Unable to open file!");
		$array = array();
		foreach($server as $k => $v) {
			
			$array[] = $v->server_ip.':'.$v->serverid;
		}
			
		$txt = implode(',',$array);
		fwrite($myfile, $txt);
		fclose($myfile);

		parent::afterFind ();
	}
	
	public function getHeadline() {
		return CHtml::link($this->name,Yii::app()->createUrl('utserver/detail',array('id'=>$this->serverid,'seo'=>GFunctions::normalisiereString($this->name))));
	}
	
	public function getLink($view = 'detail') {
		return Yii::app()->createUrl('utserver/'.$view,array('id'=>$this->serverid,'seo'=>GFunctions::normalisiereString($this->name)));
	}	
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Utserver the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
