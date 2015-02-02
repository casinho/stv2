<?php

/**
 * This is the model class for table "clanwars".
 *
 * The followings are the available columns in table 'clanwars':
 * @property integer $id
 * @property string $gametype
 * @property integer $squad_id
 * @property string $datum
 * @property string $spielerzahl
 * @property integer $enemy_id
 * @property string $enemy_spieler
 * @property integer $liga_id
 * @property string $servername
 * @property integer $scorelimit
 * @property integer $timelimit
 * @property string $sonstiges
 * @property string $anzahl_maps
 * @property string $ringer1
 * @property string $ringer2
 * @property string $spieler
 * @property string $report
 * @property string $wertung
 * @property string $endscore
 * @property string $geg_endscore
 * @property string $poster_id
 * @property string $hits
 * @property string $fazit
 */
class Clanwars extends CActiveRecord
{
	
	private $_naechsterWar;
	private $_vorherigerWar;
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Clanwars the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'clanwars';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('squad_id, datum, enemy_id, liga_id, scorelimit, timelimit', 'required'),
			array('squad_id, enemy_id, liga_id, scorelimit, timelimit', 'numerical', 'integerOnly'=>true),
			array('gametype', 'length', 'max'=>50),
			array('spielerzahl, anzahl_maps, spieler, endscore, geg_endscore, poster_id, hits', 'length', 'max'=>5),
			array('servername', 'length', 'max'=>20),
			array('sonstiges', 'length', 'max'=>255),
			array('ringer1, ringer2', 'length', 'max'=>100),
			array('wertung', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, gametype, squad_id, datum, spielerzahl, enemy_id, enemy_spieler, liga_id, servername, scorelimit, timelimit, sonstiges, anzahl_maps, ringer1, ringer2, spieler, report, wertung, endscore, geg_endscore, poster_id, hits, fazit', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'gegner' => array(
					self::HAS_ONE,
					'Clans',
					array('clan_id'=>'enemy_id'),
			),
			'squad' => array(
					self::HAS_ONE,
					'Squad',
					array('squad_id'=>'squad_id'),
			),
			'liga' => array(
					self::HAS_ONE,
					'Link',
					array('id'=>'liga_id'),
			),
			'autor' => array(
					self::HAS_ONE,
					'User',
					array('user_id'=>'poster_id'),
			),		
		);
		
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'gametype' => 'Gametype',
			'squad_id' => 'Squad',
			'datum' => 'Datum',
			'spielerzahl' => 'Spielerzahl',
			'enemy_id' => 'Enemy',
			'enemy_spieler' => 'Enemy Spieler',
			'liga_id' => 'Liga',
			'servername' => 'Servername',
			'scorelimit' => 'Scorelimit',
			'timelimit' => 'Timelimit',
			'sonstiges' => 'Sonstiges',
			'anzahl_maps' => 'Anzahl Maps',
			'ringer1' => 'Ringer1',
			'ringer2' => 'Ringer2',
			'spieler' => 'Spieler',
			'report' => 'Report',
			'wertung' => 'Wertung',
			'endscore' => 'Endscore',
			'geg_endscore' => 'Geg Endscore',
			'poster_id' => 'Poster',
			'hits' => 'Hits',
			'fazit' => 'Fazit',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('gametype',$this->gametype,true);
		$criteria->compare('squad_id',$this->squad_id);
		$criteria->compare('datum',$this->datum,true);
		$criteria->compare('spielerzahl',$this->spielerzahl,true);
		$criteria->compare('enemy_id',$this->enemy_id);
		$criteria->compare('enemy_spieler',$this->enemy_spieler,true);
		$criteria->compare('liga_id',$this->liga_id);
		$criteria->compare('servername',$this->servername,true);
		$criteria->compare('scorelimit',$this->scorelimit);
		$criteria->compare('timelimit',$this->timelimit);
		$criteria->compare('sonstiges',$this->sonstiges,true);
		$criteria->compare('anzahl_maps',$this->anzahl_maps,true);
		$criteria->compare('ringer1',$this->ringer1,true);
		$criteria->compare('ringer2',$this->ringer2,true);
		$criteria->compare('spieler',$this->spieler,true);
		$criteria->compare('report',$this->report,true);
		$criteria->compare('wertung',$this->wertung,true);
		$criteria->compare('endscore',$this->endscore,true);
		$criteria->compare('geg_endscore',$this->geg_endscore,true);
		$criteria->compare('poster_id',$this->poster_id,true);
		$criteria->compare('hits',$this->hits,true);
		$criteria->compare('fazit',$this->fazit,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function holeMaps() {
		$criteria = new CDbCriteria();
		$criteria->condition = 'clanwar_id = :cw_id';
		$criteria->params = array(':cw_id' => $this->id);
		$criteria->order = 'map_nr ASC';
		
		$maps = Map2Clanwar::model()->with('map')->findAll($criteria);
		return $maps;
	}

	public function holeLineup() {
		$criteria = new CDbCriteria();
		$criteria->condition = 'clanwar_id = :cw_id';
		$criteria->params = array(':cw_id' => $this->id);
		//$criteria->order = 'map_nr ASC';
		
		$maps = User2Clanwar::model()->with('user')->findAll($criteria);
		return $maps;
	}	
	
	public function holeAlleClanwars($id = 0) {
		
		$sort = new CSort();
		$sort->defaultOrder = 'w.datum DESC';
		$sort->attributes = array(
			'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl DESC',
			),
			'gegner' => array(
						'asc'=>'c.clan',
						'desc'=>'c.clan DESC',
			),
			'ergebnis' => array(
						'asc'=>'w.endscore',
						'desc'=>'w.endscore DESC',
			),
			'datum' => array(
						'asc'=>'w.datum',
						'desc'=>'w.datum DESC',
			),
			'wertung' => array(
						'asc'=>'w.wertung',
						'desc'=>'w.wertung DESC',
			),
			'squad' => array(
						'asc'=>'s.squad_tag',
						'desc'=>'s.squad_tag DESC',
			),
			'autor' => array(
					'asc'=>'u.user_nick',
					'desc'=>'u.user_nick',
			),			
			'liga' => array(
					'asc'=>'liga_tag',
					'desc'=>'liga_tag DESC',
			),																
        );    
		
		if($id > 0) {
			$sql1 = "SELECT COUNT(*) FROM clanwars WHERE wertung > 0 AND squad_id = ".$id;
			$sql2 = "SELECT w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,s.squad_tag,f.flaggenname,f.nationalname,u.user_nick,l.id AS liga_id,l.text AS liga,l.tag AS liga_tag FROM clanwars AS w LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = w.id AND k.zuweisung = 'clanwars' LEFT JOIN clans AS c ON c.clan_id = w.enemy_id LEFT JOIN flaggen AS f ON f.id = c.land_id LEFT JOIN squad AS s ON s.squad_id = w.squad_id LEFT JOIN link AS l ON l.id = w.liga_id LEFT JOIN user AS u ON u.user_id = w.poster_id WHERE w.squad_id = ".$id." GROUP BY w.id";
		} else {
			$sql1 = "SELECT COUNT(*) FROM clanwars WHERE wertung > 0";
			$sql2 = "SELECT w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,s.squad_tag,f.flaggenname,f.nationalname,u.user_nick,l.id AS liga_id,l.text AS liga,l.tag AS liga_tag FROM clanwars AS w LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = w.id AND k.zuweisung = 'clanwars' LEFT JOIN clans AS c ON c.clan_id = w.enemy_id LEFT JOIN flaggen AS f ON f.id = c.land_id LEFT JOIN squad AS s ON s.squad_id = w.squad_id LEFT JOIN link AS l ON l.id = w.liga_id LEFT JOIN user AS u ON u.user_id = w.poster_id GROUP BY w.id";
		}
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql1)->queryScalar();
		$output 	= new CSqlDataProvider($sql2,array(
							'totalItemCount' => $anzahl,
			 					//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
	 						'sort' => $sort,
							'pagination' => array(
								'pageSize' => 20
							)
					)
		); 
		return $output;
	}

	public function holeSquadClanwars($squad_id) {
	
		$sort = new CSort();
		$sort->defaultOrder = 'w.datum DESC';
		$sort->attributes = array(
				'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl DESC',
				),
				'gegner' => array(
						'asc'=>'c.clan',
						'desc'=>'c.clan DESC',
				),
				'ergebnis' => array(
						'asc'=>'w.endscore',
						'desc'=>'w.endscore DESC',
				),
				'datum' => array(
						'asc'=>'w.datum',
						'desc'=>'w.datum DESC',
				),
				'wertung' => array(
						'asc'=>'w.wertung',
						'desc'=>'w.wertung DESC',
				),
				'squad' => array(
						'asc'=>'s.squad',
						'desc'=>'s.squad DESC',
				),
				'liga' => array(
						'asc'=>'liga_tag',
						'desc'=>'liga_tag DESC',
				),				
		);
	
		$sql 		= "SELECT COUNT(*) FROM clanwars WHERE wertung > 0 AND squad_id = ".$squad_id;
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
	
		$sql		= "SELECT w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,f.flaggenname,f.nationalname,l.id AS liga_id,l.text AS liga,l.tag AS liga_tag FROM clanwars AS w LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = w.id AND k.zuweisung = 'clanwars' LEFT JOIN clans AS c ON c.clan_id = w.enemy_id LEFT JOIN flaggen AS f ON f.id = c.land_id LEFT JOIN squad AS s ON s.squad_id = w.squad_id LEFT JOIN link AS l ON l.id = w.liga_id WHERE w.squad_id = ".$squad_id." GROUP BY w.id";
		$output 	= new CSqlDataProvider($sql,array(
				'totalItemCount' => $anzahl,
				//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
				'sort' => $sort,
				'pagination' => array(
						'pageSize' => 20
				)
		)
		);
		return $output;
	}
	
	
	
	public function holeWeitereClanwars() {
	
		$sort = new CSort();
		$sort->defaultOrder = 'w.datum DESC';
		$sort->attributes = array(
				'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl DESC',
				),
				'gegner' => array(
						'asc'=>'c.clan',
						'desc'=>'c.clan DESC',
				),
				'ergebnis' => array(
						'asc'=>'w.endscore',
						'desc'=>'w.endscore DESC',
				),
				'datum' => array(
						'asc'=>'w.datum',
						'desc'=>'w.datum DESC',
				),
				'wertung' => array(
						'asc'=>'w.wertung',
						'desc'=>'w.wertung DESC',
				),
				'squad' => array(
						'asc'=>'s.squad_name',
						'desc'=>'s.squad_name DESC',
				),
				'liga' => array(
						'asc'=>'liga_tag',
						'desc'=>'liga_tag DESC',
				),				
		);
	
		$sql 		= "SELECT COUNT(*) FROM clanwars WHERE wertung > 0 AND enemy_id = ".$this->enemy_id." AND id != ".$this->id;
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
	
		$sql		= "SELECT w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,f.flaggenname,f.nationalname,l.id AS liga_id,l.text AS liga,l.tag AS liga_tag FROM clanwars AS w LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = w.id AND k.zuweisung = 'clanwars' LEFT JOIN clans AS c ON c.clan_id = w.enemy_id LEFT JOIN flaggen AS f ON f.id = c.land_id LEFT JOIN squad AS s ON s.squad_id = w.squad_id LEFT JOIN link AS l ON l.id = w.liga_id WHERE w.enemy_id = ".$this->enemy_id." AND w.id != ".$this->id." GROUP BY w.id";
		$output 	= new CSqlDataProvider($sql,array(
				'totalItemCount' => $anzahl,
				//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
				'sort' => $sort,
				//'pagination' => false,
		)
		);
		return $output;
	}	
	
	// hole alle eingesetzten Member
	public function holeAlleMember() {
	
		$sort = new CSort();
		$sort->defaultOrder = 'einsaetze DESC';
		$sort->attributes = array(
				'einsaetze' => array(
						'asc'=>'einsaetze',
						'desc'=>'einsaetze DESC',
				),
				'herkunft' => array(
						'asc'=>'f.nationalname',
						'desc'=>'f.nationalname DESC',
				),
				'ergebnis' => array(
						'asc'=>'w.endscore',
						'desc'=>'w.endscore DESC',
				),
				'nick' => array(
						'asc'=>'u.user_nick',
						'desc'=>'u.user_nick DESC',
				),
				'wertung' => array(
						'asc'=>'w.wertung',
						'desc'=>'w.wertung DESC',
				),
				'status' => array(
						'asc'=>'m.status',
						'desc'=>'m.status DESC',
				),
		);
	
		$sql 		= "SELECT COUNT(*) FROM user AS u LEFT JOIN user2clanwar AS u2w ON u2w.user_id = u.user_id LEFT JOIN clanwars AS w ON u2w.clanwar_id = w.id AND w.wertung > 0 GROUP BY u.user_id";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
		
		$sql		= "SELECT u.*,COUNT(w.id) AS einsaetze,f.flaggenname,f.nationalname FROM user AS u LEFT JOIN user2clanwar AS u2w ON u2w.user_id = u.user_id LEFT JOIN clanwars AS w ON u2w.clanwar_id = w.id AND w.wertung > 0 LEFT JOIN flaggen AS f ON f.id = u.flaggen_id GROUP BY u.user_id";
		$output 	= new CSqlDataProvider($sql,array(
				'keyField' => 'user_id',
				'totalItemCount' => $anzahl,
				//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
				'sort' => $sort,
				'pagination' => array(
						'pageSize' => 20
				)
		)
		);
		return $output;
	}	
	
	public function setNaechstenWar() {

		$criteria = new CDbCriteria();
		$criteria->condition = 'id != :myID AND datum > :myDatum';
		$criteria->params = array(':myID'=>$this->id,':myDatum'=>$this->datum);
		$criteria->limit = 1;
		
		$this->_naechsterWar = $this->find($criteria);

		if($this->_naechsterWar != null) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getNaechstenWar() {
		return $this->_naechsterWar;
	}

	public function setVorherigenWar() {

		$criteria = new CDbCriteria();
		$criteria->condition = 'id != :myID AND datum < :myDatum';
		$criteria->params = array(':myID'=>$this->id,':myDatum'=>$this->datum);
		$criteria->order = 'datum DESC';		
		$criteria->limit = 1;		
		
		$this->_vorherigerWar = $this->find($criteria);
		
		if($this->_vorherigerWar != null) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getVorherigenWar() {
		return $this->_vorherigerWar;
	}	

	public function holeAlleClans() {
		
		$sort = new CSort();
		$sort->defaultOrder = 'wars DESC';
		$sort->attributes = array(
			'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl DESC',
			),
			'clan' => array(
						'asc'=>'c.clan',
						'desc'=>'c.clan DESC',
			),
			'tag' => array(
						'asc'=>'c.tag',
						'desc'=>'c.tag DESC',
			),
			'datum' => array(
						'asc'=>'w.datum',
						'desc'=>'w.datum DESC',
			),
			'homepage' => array(
						'asc'=>'c.homepage_flag ASC, c.homepage ASC',
						'desc'=>'c.homepage_flag DESC, c.homepage DESC',
			),
			'spiele' => array(
						'asc'=>'wars',
						'desc'=>'wars DESC',
			),
        );    
		
		$sql 		= "SELECT COUNT(*) FROM clanwars WHERE wertung > 0";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
		
		$sql		= "SELECT c.*,COUNT(k.kommentar_id) AS anzahl,COUNT(w.id) AS wars,f.flaggenname,f.nationalname FROM clans AS c LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = c.clan_id AND k.zuweisung = 'clans' LEFT JOIN clanwars AS w ON w.enemy_id = c.clan_id LEFT JOIN flaggen AS f ON f.id = c.land_id GROUP BY c.clan_id";
		$output 	= new CSqlDataProvider($sql,array(
							'keyField' => 'clan_id',
							'totalItemCount' => $anzahl,
			 					//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
	 						'sort' => $sort,
							'pagination' => array(
								'pageSize' => 20
							)
					)
		); 
		
		return $output;
	}
	

	public function holeAlleMaps($id=0) {
	
		$sort = new CSort();
		$sort->defaultOrder = 'gespielt DESC';
		$sort->attributes = array(
				'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl DESC',
				),
				'niederlagen' => array(
						'asc'=>'niederlagen DESC',
						'desc'=>'niederlagen ASC',
				),
				'siege' => array(
						'asc'=>'siege DESC',
						'desc'=>'siege ASC',
				),
				'unentschieden' => array(
						'asc'=>'unentschieden DESC',
						'desc'=>'unentschieden ASC',
				),				
				'map' => array(
						'asc'=>'f.name',
						'desc'=>'f.mame DESC',
				),
				'datum' => array(
						'asc'=>'w.datum',
						'desc'=>'w.datum DESC',
				),
				'quote' => array(
						'desc'=>'quote DESC, gespielt DESC',
						'asc'=>'quote ASC, gespielt DESC',
				),
				'gespielt' => array(
						'desc'=>'gespielt DESC, quote DESC',
						'asc'=>'gespielt DESC, quote DESC',
				),				
				'spiele' => array(
						'asc'=>'wars',
						'desc'=>'wars DESC',
				),
		);
	
		if($id > 0) {
			$sql1 = "SELECT COUNT(DISTINCT(f.id)) FROM file AS f INNER JOIN map2clanwar AS m2c ON m2c.map_id = f.id INNER JOIN clanwars AS c ON c.id = m2c.clanwar_id AND c.wertung > 0 WHERE c.squad_id =".$id;
			$sql2 = "SELECT f.*,COUNT(m2c.map_id) AS gespielt,((SUM(IF(m2c.wertung = '3', 1, 0))*100)/COUNT(c.id)) AS quote,SUM(IF(m2c.wertung = '3', 1, 0)) AS siege,SUM(IF(m2c.wertung = '2', 1, 0)) AS niederlagen,SUM(IF(m2c.wertung = '1', 1, 0)) AS unentschieden 
						FROM file AS f 
						INNER JOIN map2clanwar AS m2c ON m2c.map_id = f.id 
						INNER JOIN clanwars AS c ON c.id = m2c.clanwar_id AND c.wertung > 0 
						WHERE c.squad_id = ".$id."
						GROUP BY f.id";
		} else {
			$sql1 = "SELECT COUNT(DISTINCT(f.id)) FROM file AS f INNER JOIN map2clanwar AS m2c ON m2c.map_id = f.id INNER JOIN clanwars AS c ON c.id = m2c.clanwar_id AND c.wertung > 0";
			$sql2 = "SELECT f.*,COUNT(m2c.map_id) AS gespielt,((SUM(IF(m2c.wertung = '3', 1, 0))*100)/COUNT(c.id)) AS quote,SUM(IF(m2c.wertung = '3', 1, 0)) AS siege,SUM(IF(m2c.wertung = '2', 1, 0)) AS niederlagen,SUM(IF(m2c.wertung = '1', 1, 0)) AS unentschieden
						FROM file AS f
						INNER JOIN map2clanwar AS m2c ON m2c.map_id = f.id
						INNER JOIN clanwars AS c ON c.id = m2c.clanwar_id AND c.wertung > 0
						GROUP BY f.id";			
		}
		//$sql		= "SELECT c.*,COUNT(k.kommentar_id) AS anzahl,COUNT(w.id) AS wars,f.flaggenname,f.nationalname FROM clans AS c LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = c.clan_id AND k.zuweisung = 'clans' LEFT JOIN clanwars AS w ON w.enemy_id = c.clan_id LEFT JOIN flaggen AS f ON f.id = c.land_id GROUP BY c.clan_id";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql1)->queryScalar();
		$output 	= new CSqlDataProvider($sql2,array(
				//'keyField' => 'f.id',
				'totalItemCount' => $anzahl,
				//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
				'sort' => $sort,
				'pagination' => array(
						'pageSize' => 20
				)
		)
		);
	
		return $output;
	}	
	
	public static function holeSquadMaps($map_id) {
		$sort = new CSort();
		$sort->defaultOrder = 'w.datum DESC';
		$sort->attributes = array(
				'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl DESC',
				),
				'gegner' => array(
						'asc'=>'c.clan',
						'desc'=>'c.clan DESC',
				),
				'ergebnis' => array(
						'asc'=>'w.endscore',
						'desc'=>'w.endscore DESC',
				),
				'datum' => array(
						'asc'=>'w.datum',
						'desc'=>'w.datum DESC',
				),
				'wertung' => array(
						'asc'=>'w.wertung',
						'desc'=>'w.wertung DESC',
				),
				'squad' => array(
						'asc'=>'s.squad_tag',
						'desc'=>'s.squad_tag DESC',
				),
				'autor' => array(
						'asc'=>'u.user_nick',
						'desc'=>'u.user_nick',
				),
				'liga' => array(
						'asc'=>'liga_tag',
						'desc'=>'liga_tag DESC',
				),
		);
		
		$sql1 = "SELECT COUNT(*) FROM map2clanwar AS m2c INNER JOIN clanwars AS c ON c.id = m2c.clanwar_id WHERE m2c.wertung > 0 AND map_id = ".$map_id;
		$sql2 = "SELECT m2c.score_st,m2c.score_enemy,m2c.wertung AS mapwertung,w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,s.squad_tag,f.flaggenname,f.nationalname,u.user_nick,l.id AS liga_id,l.text AS liga,l.tag AS liga_tag FROM map2clanwar AS m2c INNER JOIN clanwars AS w ON w.id = m2c.clanwar_id LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = w.id AND k.zuweisung = 'clanwars' LEFT JOIN clans AS c ON c.clan_id = w.enemy_id LEFT JOIN flaggen AS f ON f.id = c.land_id LEFT JOIN squad AS s ON s.squad_id = w.squad_id LEFT JOIN link AS l ON l.id = w.liga_id LEFT JOIN user AS u ON u.user_id = w.poster_id WHERE m2c.map_id = ".$map_id." GROUP BY m2c.auto_id";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql1)->queryScalar();
		$output 	= new CSqlDataProvider($sql2,array(
				'totalItemCount' => $anzahl,
				//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
				'sort' => $sort,
				'pagination' => array(
						'pageSize' => 20
				)
		)
		);
		return $output;		
	}	
	
	public static function getScore($data,$map = false) {
		if($map === false) {
			return $data['endscore'].':'.$data['geg_endscore'];
		} else {
			return $data['score_st'].':'.$data['score_enemy'];
		}
	}
	
	public static function getWertung($wertung,$style='',$txt='&nbsp;') {
		
		($style == '') ? $style = 'width:12px;' : $style = $style;
		
		if($wertung == 3) {
			$output = TbHtml::labelTb($txt, array('color' => TbHtml::LABEL_COLOR_SUCCESS, 'class'=>'fn','style'=>$style,'title'=>Yii::t('clanwars','sieg')));
		} elseif($wertung == 2) {
			$output = TbHtml::labelTb($txt, array('color' => TbHtml::LABEL_COLOR_IMPORTANT, 'class'=>'fn','style'=>$style,'title'=>Yii::t('clanwars','niederlage')));
		} else {
			$output = TbHtml::labelTb($txt, array('class'=>'fn','style'=>$style, 'title' => Yii::t('clanwars','unentschieden')));
		}
		return $output;
	}	
	
	public function getWertungen() {
		$arr = array();
		$arr[3] = Yii::t('clanwars','sieg');
		$arr[2] = Yii::t('clanwars','niederlage');
		$arr[1] = Yii::t('clanwars','unentschieden');
		return $arr;
	}
	
	public static function getClanwarStatus($status) {
		if($status == 3) {
			$output = Yii::t('clanwars','sieg');
		} elseif($status == 2) {
			$output = Yii::t('clanwars','niederlage');
		} else {
			$output = Yii::t('clanwars','unentschieden');
		}
		return $output;	
	}

	public static function getSpielerzahlOptionen() {
		
		$array = array('1on1','2on2','3on3','4on4','5on5','6on6','7on7');
		
		$output = array();
		foreach($array as $k => $v) {
			$output[$v] = $v;
		}
		
		return $output;
	}	
	
	public function getHeadline() {
		$titel = Yii::t('clanwars','competition_vs_clan',array('{Clan}'=> $this->gegner->tag, '{liga}'=>$this->liga->tag));
		$linktitel = Yii::t('clanwars','match_vs_clan',array('{squad}' => $this->squad->squad_tag,'{Clan}'=> $this->gegner->clan, '{liga}'=>$this->liga->tag));
		return CHtml::link($titel,Yii::app()->createUrl('clanwars/detail',array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($linktitel))));
	}
	
	public function getLink($view='detail') {
		$linktitel = Yii::t('clanwars','match_vs_clan',array('{squad}' => $this->squad->squad_tag,'{Clan}'=> $this->gegner->clan, '{liga}'=>$this->liga->tag));
		return Yii::app()->createUrl('clanwars/'.$view,array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($linktitel)));
	}
	
	protected function afterDelete() {
		parent::afterDelete();
	
		$attributes['clanwar_id'] = $this->id;
	
		// alle Userrelevanten Daten löschen
		User2Clanwar::model()->deleteAllByAttributes($attributes);
		Map2Clanwar::model()->deleteAllByAttributes($attributes);
	
		unset($attributes);
		
		$attributes['fremd_id'] 	= $this->id;
		$attributes['zuweisung']	= 'clanwars';
		KommentarZuweisung::model()->deleteAllByAttributes($attributes);		
	}	
	
	
}