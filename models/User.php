<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $user_id
 * @property string $old_user_id
 * @property string $user_nick
 * @property string $user_pwd
 * @property string $realname
 * @property string $str
 * @property string $ort
 * @property string $plz
 * @property string $handy
 * @property integer $flaggen_id
 * @property string $geburtsdatum
 * @property integer $geburtstag
 * @property integer $geburtmonat
 * @property string $email
 * @property string $status
 * @property string $avatar
 * @property string $skin 
 * @property string $aufgaben
 * @property string $member_since
 * @property string $fav_maps
 * @property string $hate_maps
 * @property string $fav_weapons
 * @property string $clanhistory
 * @property string $hobbies
 * @property string $fav_musik
 * @property string $fav_filme
 * @property string $motto
 * @property string $membertype
 * @property integer $member_flag
 * @property integer $admin_flag
 * @property integer $freigeschaltet_flag
 * @property integer $sperr_flag
 * @property string $datum_registriert
 * @property string $datum_validiert
 * @property string $sprache
 * @property string $letzte_ip
 * @property string $letzter_login
 */
class User extends CActiveRecord
{
	
	public $tmpUserId;
	public $ungeleseneNachrichten;
	public $rememberMe;	
	public $squads;
	
	public $user_pwd2;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_nick, user_pwd, email', 'required', 'on'=>'insert, update'),
			array('user_nick, user_pwd, user_pwd2', 'required', 'on'=>'admin-create'),
			array('user_nick', 'required', 'on'=>'admin-update'),
			array('user_pwd', 'length', 'min'=> 5, 'on'=>'admin-create'),
			array('user_pwd2', 'compare', 'compareAttribute'=>'user_pwd', 'message' => 'Die Passwörter stimmen nicht überein', 'on'=>'admin-create'),
			array('flaggen_id, geburtstag, geburtmonat, member_flag, admin_flag, freigeschaltet_flag, sperr_flag', 'numerical', 'integerOnly'=>true),
			array('user_nick', 'length', 'min'=>2, 'max'=>20),
			array('email', 'email'),
			array('user_nick', 'unique', 'className' => 'User', 'attributeName'=>'user_nick'),
			array('email', 'unique', 'className' => 'User', 'attributeName'=>'email'),
			array('old_user_id, plz', 'length', 'max'=>10),
			array('user_nick, user_pwd', 'length', 'max'=>30),
			array('realname, str, ort, email', 'length', 'max'=>255),
			array('handy', 'length', 'max'=>40),
			array('geburtsdatum', 'length', 'max'=>20),
			array('status, avatar', 'length', 'max'=>150),
			array('sprache', 'length', 'max'=>4),
			array('letzte_ip', 'length', 'max'=>50),
			array('member_since', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, old_user_id, user_nick, user_pwd, realname, str, ort, plz, handy, flaggen_id, geburtsdatum, geburtstag, geburtmonat, email, status, avatar, skin, aufgaben, member_since, fav_maps, hate_maps, fav_weapons, clanhistory, hobbies, fav_musik, fav_filme, motto, membertype, member_flag, admin_flag, freigeschaltet_flag, sperr_flag, datum_registriert, datum_validiert, sprache, letzte_ip, letzter_login', 'safe', 'on'=>'search'),
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
				'land' => array(
						self::HAS_ONE,
						'Flaggen',
						array('id'=>'flaggen_id'),
				),
				'user2squad' => array(
					self::HAS_MANY,
					'User2Squad',
					array('user_id'=>'user_id'),
				),	
				'squad' => array(
					self::HAS_MANY, 
					'Squad', 
					array('squad_id'=>'squad_id'), 
					'through'=>'user2squad',
					'order'=>'squad.squad_order ASC'
				),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'UserId',
			'old_user_id' => 'Old-Id',
			'user_nick' => Yii::t('user','usernick'),
			'user_pwd' => Yii::t('user','passwort'),
			'realname' => Yii::t('user','realname'),
			'str' => Yii::t('user','str'),
			'ort' => Yii::t('user','ort'),
			'plz' => Yii::t('user','plz'),
			'handy' => Yii::t('user','handy'),
			'flaggen_id' => Yii::t('user','flaggen_id'),
			'geburtsdatum' => Yii::t('user','geburtsdatum'),
			'geburtstag' => Yii::t('user','geburtstag'),
			'geburtmonat' => Yii::t('user','geburtsmonat'),
			'email' => Yii::t('user','email'),
			'status' => Yii::t('user','status'),
			'avatar' => Yii::t('user','avatar'),
			'skin' => Yii::t('user','skin'),
			'aufgaben' => Yii::t('user','aufgaben'),
			'member_since' => Yii::t('user','member_since'),
			'fav_maps' => Yii::t('user','fav_maps'),
			'hate_maps' => Yii::t('user','hate_maps'),
			'fav_weapons' => Yii::t('user','fav_weapons'),
			'clanhistory' => Yii::t('user','clanhistory'),
			'hobbies' => Yii::t('user','hobbies'),
			'fav_musik' => Yii::t('user','fav_musik'),
			'fav_filme' => Yii::t('user','fav_filme'),
			'motto' => Yii::t('user','motto'),
			'membertype' => Yii::t('user','membertype'),
			'member_flag' => Yii::t('user','member_flag'),
			'admin_flag' => Yii::t('user','admin_flag'),
			'freigeschaltet_flag' => Yii::t('user','freigeschaltet_flag'),
			'sperr_flag' => Yii::t('user','sperr_flag'),
			'datum_registriert' => Yii::t('user','datum_registriert'),
			'datum_validiert' => Yii::t('user','datum_validiert'),
			'sprache' => Yii::t('user','sprache'),
			'letzte_ip' => Yii::t('user','letzte_ip'),
			'letzter_login' => Yii::t('user','letzter_login'),
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

		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('old_user_id',$this->old_user_id,true);
		$criteria->compare('user_nick',$this->user_nick,true);
		$criteria->compare('user_pwd',$this->user_pwd,true);
		$criteria->compare('realname',$this->realname,true);
		$criteria->compare('str',$this->str,true);
		$criteria->compare('ort',$this->ort,true);
		$criteria->compare('plz',$this->plz,true);
		$criteria->compare('handy',$this->handy,true);
		$criteria->compare('flaggen_id',$this->flaggen_id);
		$criteria->compare('geburtsdatum',$this->geburtsdatum,true);
		$criteria->compare('geburtstag',$this->geburtstag);
		$criteria->compare('geburtmonat',$this->geburtmonat);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('avatar',$this->avatar,true);
		$criteria->compare('skin',$this->skin,true);
		$criteria->compare('aufgaben',$this->aufgaben,true);
		$criteria->compare('member_since',$this->member_since,true);
		$criteria->compare('fav_maps',$this->fav_maps,true);
		$criteria->compare('hate_maps',$this->hate_maps,true);
		$criteria->compare('fav_weapons',$this->fav_weapons,true);
		$criteria->compare('clanhistory',$this->clanhistory,true);
		$criteria->compare('hobbies',$this->hobbies,true);
		$criteria->compare('fav_musik',$this->fav_musik,true);
		$criteria->compare('fav_filme',$this->fav_filme,true);
		$criteria->compare('motto',$this->motto,true);
		$criteria->compare('membertype',$this->membertype,true);
		$criteria->compare('member_flag',$this->member_flag);
		$criteria->compare('admin_flag',$this->admin_flag);
		$criteria->compare('freigeschaltet_flag',$this->freigeschaltet_flag);
		$criteria->compare('sperr_flag',$this->sperr_flag);
		$criteria->compare('datum_registriert',$this->datum_registriert,true);
		$criteria->compare('datum_validiert',$this->datum_validiert,true);
		$criteria->compare('sprache',$this->sprache,true);
		$criteria->compare('letzte_ip',$this->letzte_ip,true);
		$criteria->compare('letzter_login',$this->letzter_login,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function rbamSearch($_criteria = null) {
		if($_criteria !== null) {
			$criteria = new CDbCriteria($_criteria);
		} else {
			$criteria = new CDbCriteria;
		}
	
		$_userGet = Yii::app()->request->getParam('User');
	
		if(!empty($_userGet)) {
		
		foreach($_userGet as $key => $v) {

			if(!empty($v)) {
				if($key == 'user_id') {
					$criteria->condition = $key." = ".$v;
				} else {
					if($key == 'rbamName') {
						$criteria->compare('user_nick', $v, true);
					} else {
						$criteria->compare($key, $v, true);
					}
				}
			}
		}
		}
		
		//$criteria->compare('user_nick', $_userGet['rbamName'], true);
	
		return $criteria;
	}
	
	public function holeAnzahlUserFreischaltung($freigeschaltet_flag = 1,$tld = '') {
		$sql = "SELECT COUNT(user_id) FROM ".$this->tableName()." WHERE freigeschaltet_flag = ".$freigeschaltet_flag."";
		$anzahl = Yii::app()->db->createCommand($sql)->queryScalar();
		return $anzahl;
	}
	
	public function holeUserFreischaltung($freigeschaltet_flag=1,$sprache='') {
		$sql = "SELECT
					nick,
					user_id,
					user_id AS primaryKey,
					realname,
					email,
					datum_registriert,
					letzte_ip,
					sprache,
					user_nick AS bg
				FROM
					".$this->tableName()."
				WHERE
					freigeschaltet_flag = ".$freigeschaltet_flag."";
	
	
			
		$count_query = "SELECT COUNT(*) FROM ".$this->tableName()." WHERE freigeschaltet_flag = ".$freigeschaltet_flag."";
	
		if(!empty($sprache)) {
			$sql = $sql." AND tld_registriert = '".$sprache."'";
				
			$count_query = $count_query." AND tld_registriert = '".$sprache."'";
		}
	
		$item_count = Yii::app()->db->createCommand($count_query)->queryScalar();
	
		$sort = new CSort();
		$sort->attributes = array(
				'nick' 				=> array('nick' => 'asc'),
				'realname' 			=> array('default' => 'asc'),
				'letzte_ip' 		=> array('default' => 'asc'),
				'datum_registriert' => array('default' => 'asc'),
				'email' 			=> array('default' => 'asc')
		);
	
		$dataProvider = new CSqlDataProvider($sql, array(
				'keyField'		=> 'user_id',
				'totalItemCount'=> $item_count,
				'sort' 			=> $sort,
				'pagination'=>array(
						//'pageSize'=>Yii::app()->params['page_size'],
						'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['page_size']),
				),
		));
	
		$dataProvider->sort->defaultOrder='datum_registriert DESC';
	
		return $dataProvider;
	}
	
	public function beforeDelete() {
		$this->tmpUserId = $this->user_id;
		return parent::beforeDelete();
	}
	
	protected function afterFind ()
	{
		// convert to display format
		if($this->member_since == '0000-00-00') {
			$this->member_since = null;
		}
		
		if(!empty($this->member_since)) {
			$this->member_since = strtotime ($this->member_since);
			$this->member_since = date('d.m.Y', $this->member_since);
		}

		if(!empty($this->geburtsdatum)) {
			$this->geburtsdatum = strtotime ($this->geburtsdatum);
			$this->geburtsdatum = date('d.m.Y', $this->geburtsdatum);
		}		
		
		$this->squads = $this->getMemberSquads();
		
		parent::afterFind ();
	}
	
	protected function beforeValidate()
	{
		// convert to storage format
		if(!empty($this->member_since)) {
			$this->member_since = strtotime($this->member_since);
			$this->member_since = date ('Y-m-d', $this->member_since);
		}

		if(!empty($this->geburtsdatum)) {
			$this->geburtsdatum = strtotime ($this->geburtsdatum);
			$this->geburtsdatum = date('Y-m-d', $this->geburtsdatum);
		}		
		
		return parent::beforeValidate ();
	}	

	protected function afterDelete() {
		parent::afterDelete();
	
		// alle Userrelevanten Daten löschen
		/*
		User2Daten::Model()->deleteAllByAttributes($attributes);
		User2Posts::Model()->deleteAllByAttributes($attributes);
		User2Validierung::Model()->deleteAllByAttributes($attributes);
		Favoriten::Model()->deleteAllByAttributes($attributes);
	
		unset($attributes);
		*/	
		$attributes['fremd_id'] 	= $this->tmpUserId;
		$attributes['zuweisung']	= 'member';
		KommentarZuweisung::model()->deleteAllByAttributes($attributes);		
		
		//$attributes['user_id_beitrag'] = $this->tmpUserId;
		//ForumAbos::Model()->deleteAllByAttributes($attributes);
	}
	
	protected function afterSave() {
	
		$this->member_since = strtotime ($this->member_since);
		$this->member_since = date('d.m.Y', $this->member_since);		
		
		parent::afterSave();
		if($this->isNewRecord) {
			/*
			$user_id = $this->getPrimaryKey();
	
			//$typen = array('vorname','nachname','mail');
			$typen = array('mail');
				
			$datentyp = Datentyp::model()->findAllByAttributes(array('datentyp'=>$typen));
				
			// Userreleavante Datensätze anlegen
				
			foreach($datentyp as $k => $v) {
				$User2Daten = new User2Daten;
				$User2Daten->user_id = $user_id;
				$User2Daten->datentyp_id 	= $v->datentyp_id;
				if(isset($this->attributes[$v->datentyp])) {
					$User2Daten->datenwert 	= $this->attributes[$v->datentyp];
				} else {
					$User2Daten->datenwert 	= $this->attributes['user_mail'];
				}
				$User2Daten->daten_anzeige	= 0;
				$User2Daten->save(false);
				unset($User2Daten);
			}
				
				
			$User2Posts = new User2Posts;
			$User2Posts->user_id = $user_id;
			$User2Posts->save(false);
				
			unset($User2Posts);
				*/
			$this->isNewRecord = false;
		}
	}
	
	/*
	 * @return boolean validate user
	*/
	public function validatePassword($password){
		return $this->hashPassword($password) === $this->user_pwd;
	}
	
	/**
	 * @return hashed value
	 */
	
	public function getUserFoto() {
		if($this->user_id == 14165 || $this->user_nick == 'thrawn') {
			return '14165_1349790468.jpg';
		}
		return false;
	
	}
	
	public function hashPassword($password){
		$salt = base64_encode($password);
		return $salt;
	}
	
	public function checkActive() {
		if($this->freigeschaltet_flag == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public function checkBanned() {
		if($this->sperr_flag > 0) {
			return false;
		} else {
			return true;
		}
	}
	
	public function holeAlleMember($chosenLetter='alle') {
	
		$sort = new CSort();
		$sort->defaultOrder = 'user_nick ASC';
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
				'membertype' => array(
						'asc'=>'u.membertype',
						'desc'=>'u.membertype DESC',
				),
				'status' => array(
						'asc'=>'u.status',
						'desc'=>'u.status DESC',
				),
		);

		if($chosenLetter == 'num' || empty($chosenLetter)) {
			$sqlWhere = "AND first_letter REGEXP '^[^A-Za-z]'";
			//$sqlWhere = "AND user_nick REGEXP '^[^A-Za-z]%'";
		} elseif($chosenLetter == 'alle') {
			$sqlWhere = '';
		} else {
			$sqlWhere = "AND first_letter = :chosenLetter";
			//$sqlWhere = "AND user_nick LIKE '".$chosenLetter."%'";
		}		
		
		
		$sql 		= "SELECT COUNT(DISTINCT(u.user_id)) FROM user AS u INNER JOIN user2squad AS u2s ON u2s.user_id = u.user_id INNER JOIN squad AS s ON s.squad_id = u2s.squad_id WHERE u.member_flag = 1 AND s.st_flag = 1 ".$sqlWhere;
		$command 	= Yii::app()->db->cache(60)->createCommand($sql);
		$command->bindValue(':chosenLetter',$chosenLetter);
		$anzahl 	= $command->queryScalar();
	
		
		$sql		= "SELECT u.*,COUNT(DISTINCT w.id) AS einsaetze,f.flaggenname,f.nationalname FROM user AS u INNER JOIN user2squad AS u2s ON u2s.user_id = u.user_id INNER JOIN squad AS s ON s.squad_id = u2s.squad_id LEFT JOIN user2clanwar AS u2w ON u2w.user_id = u.user_id LEFT JOIN clanwars AS w ON u2w.clanwar_id = w.id AND w.wertung > 0 LEFT JOIN flaggen AS f ON f.id = u.flaggen_id WHERE u.member_flag = 1 AND u2s.user_id IS NOT NULL AND s.st_flag = 1 ".$sqlWhere." GROUP BY u.user_id";

		
		$output 	= new CSqlDataProvider($sql,array(
				'params' => array(':chosenLetter' => $chosenLetter),
				'keyField' => 'user_id',
				'totalItemCount' => $anzahl,
				//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
				'sort' => $sort,
				'pagination' => array(
						'pageSize' => 20
				),
		)
		);
		return $output;
	}
	
	public function holeEhemaligeMember($chosenLetter='alle') {
	
		$sort = new CSort();
		$sort->defaultOrder = 'user_nick ASC';
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
				'membertype' => array(
						'asc'=>'u.membertype',
						'desc'=>'u.membertype DESC',
				),
				'status' => array(
						'asc'=>'m.status',
						'desc'=>'m.status DESC',
				),
		);
	
		if($chosenLetter == 'num' || empty($chosenLetter)) {
			$sqlWhere = "AND first_letter REGEXP '^[^A-Za-z]'";
			//$sqlWhere = "AND user_nick REGEXP '^[^A-Za-z]%'";
		} elseif($chosenLetter == 'alle') {
			$sqlWhere = '';
		} else {
			$sqlWhere = "AND first_letter = :chosenLetter";
			//$sqlWhere = "AND user_nick LIKE '".$chosenLetter."%'";
		}
	
	
		$sql 		= "SELECT COUNT(DISTINCT(u.user_id)) FROM user AS u INNER JOIN user2squad AS u2s ON u2s.user_id = u.user_id INNER JOIN squad AS s ON s.squad_id = u2s.squad_id WHERE u.member_flag = 2 AND s.st_flag = 1 ".$sqlWhere;
		$command 	= Yii::app()->db->cache(60)->createCommand($sql);
		$command->bindValue(':chosenLetter',$chosenLetter);
		$anzahl 	= $command->queryScalar();
	
	
		$sql		= "SELECT u.*,COUNT(DISTINCT w.id) AS einsaetze,f.flaggenname,f.nationalname FROM user AS u INNER JOIN user2squad AS u2s ON u2s.user_id = u.user_id INNER JOIN squad AS s ON s.squad_id = u2s.squad_id LEFT JOIN user2clanwar AS u2w ON u2w.user_id = u.user_id LEFT JOIN clanwars AS w ON u2w.clanwar_id = w.id AND w.wertung > 0 LEFT JOIN flaggen AS f ON f.id = u.flaggen_id WHERE u.member_flag = 2 AND u2s.user_id IS NOT NULL AND s.st_flag = 1 ".$sqlWhere." GROUP BY u.user_id";
	
	
		$output 	= new CSqlDataProvider($sql,array(
				'params' => array(':chosenLetter' => $chosenLetter),
				'keyField' => 'user_id',
				'totalItemCount' => $anzahl,
				//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
				'sort' => $sort,
				'pagination' => array(
						'pageSize' => 20
				),
		)
		);
		return $output;
	}	
	
	public function holeAlleEinsaetze() {
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
						'asc'=>'liga',
						'desc'=>'liga_tag DESC',
				),				
		);
		
		$sql 		= "SELECT COUNT(*) FROM clanwars AS w INNER JOIN user2clanwar AS u2w ON u2w.clanwar_id = w.id WHERE w.wertung > 0 AND u2w.user_id = ".$this->user_id."";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
		
		$sql		= "SELECT w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,f.flaggenname,f.nationalname,l.id AS liga_id,l.text AS liga,l.tag AS liga_tag FROM clanwars AS w INNER JOIN user2clanwar AS u2w ON u2w.clanwar_id = w.id LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = w.id AND k.zuweisung = 'clanwars' LEFT JOIN clans AS c ON c.clan_id = w.enemy_id LEFT JOIN flaggen AS f ON f.id = c.land_id LEFT JOIN squad AS s ON s.squad_id = w.squad_id LEFT JOIN link AS l ON l.id = w.liga_id  WHERE u2w.user_id = ".$this->user_id." GROUP BY w.id";
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
	
	public function getHtmlLink($seite='startseite',$anzeige='user_nick',$seo='user_nick',$htmlOptions = array()) {
		echo CHtml::link($this->$anzeige,Yii::app()->createUrl('profil/'.$seite, array('id' => $this->user_id, 'seo' => TMFunctions::normalisiereString($this->$seo))),$htmlOptions);
	}
	
	public function getAbsoluteHtmlLink($seite='startseite',$anzeige='user_nick',$seo='user_nick',$htmlOptions = array()) {
		echo CHtml::link($this->$anzeige,Yii::app()->createAbsoluteUrl('profil/'.$seite, array('id' => $this->user_id, 'seo' => TMFunctions::normalisiereString($this->$seo))),$htmlOptions);
	}
	
	public function holeGesperrteIpAdressen() {
		$sql  = "SELECT user_ip FROM tm_user WHERE user_is_banned = 1";
		$data = Yii::app()->db->createCommand($sql)->queryAll();
		return $data;
	}

	public static function holeForenDaten($user_id) {
		$result['user2count'] 	= User2Count::model()->findByAttributes(array('user_id'=>$user_id,'zuweisung'=>'forum'));
		$result['member']		= User::model()->findByPk($user_id);
		//$result['signatur']		= User::holeUserDatenwert($user_id,22);
		$result['signatur']		= '';
		
		return $result;
	}
	
	
	public static function getStaticHtmlLink($array,$anzeige='user_nick',$seo='user_nick',$htmlOptions = array(),$user_id = false) {
		if($user_id != false) {
			isset($array[$user_id]) ? $user_id = $array[$user_id] : $user_id = 0;
		} else {
			isset($array['user_id']) ? $user_id = $array['user_id'] : $user_id = 0;
		}
	
		if($user_id > 0) {
			//echo CHtml::link($array[$anzeige],Yii::app()->createUrl('profil/startseite', array('id' => $array['user_id'], 'seo' => TMFunctions::normalisiereString($array[$seo]))),$htmlOptions);
			if('user_nick'==$anzeige) {
				$text = $array[$anzeige];
			} else {
				$text = $anzeige;
			}
				
			if(strlen($text) > 18) {
				$htmlOptions = array('class'=>'forum-user-small');
			}
				
			return CHtml::link($text,Yii::app()->createUrl('member/detail', array('id' => $user_id, 'seo' => GFunctions::normalisiereString($array[$seo]))),$htmlOptions);
		} else {
			return CHtml::tag('span', $htmlOptions, $array[$anzeige]);
		}
	}	
	
	public function getHeadline() {
		return CHtml::link($this->user_nick,Yii::app()->createUrl('member/detail',array('id'=>$this->user_id,'seo'=>GFunctions::normalisiereString($this->user_nick))));
	}
	
	public function getLink() {
		return Yii::app()->createUrl('member/detail',array('id'=>$this->user_id,'seo'=>GFunctions::normalisiereString($this->user_nick)));
	}	
	
	public function getEinsaetze($squad_id) {
		$sql = "SELECT COUNT(*) FROM user2clanwar AS u2c INNER JOIN clanwars AS c ON  c.id = u2c.clanwar_id WHERE u2c.user_id = ".$this->user_id." AND c.squad_id = ".$squad_id."";
		$anzahl = Yii::app()->db->createCommand($sql)->queryScalar();
		return $anzahl;
	}

	public function getImage() {
		return $this->skin;
	}	
	
	public function getName() {
		return $this->user_nick;
	}	
	
	public function getClanwarStatistik($squad_id,$wertung=0) {
		$sql = "SELECT COUNT(*) FROM user2clanwar AS u2c INNER JOIN clanwars AS c ON c.id = u2c.clanwar_id WHERE u2c.user_id = ".$this->user_id." AND c.squad_id = ".$squad_id." AND c.wertung = ".$wertung."";
		$anzahl = Yii::app()->db->createCommand($sql)->queryScalar();
		return $anzahl;
	}
	
	public function getQuote($squad_id) {
		$siege = $this->getClanwarStatistik($squad_id,3);
		if($siege>0) {
			return round($siege/$this->getEinsaetze($squad_id)*100,1);
		} else {
			return '-';
		}
	}
	
	public function getUserFlag() {
		if($this->member_flag == 0) {
			$output = Yii::t('member','registrierter_nutzer');
		} elseif($member_flag == 1) {
			$output = Yii::t('member','member');
		} elseif($member_flag == 2) {
			$output = Yii::t('member','ex_member');
		}
		return $output;
	}	
	
	public static function getUserFlags() {
		$output[0] = array('value'=>0,'text'=>Yii::t('member','registrierter_nutzer'));
		$output[1] = array('value'=>1,'text'=>Yii::t('member','member'));
		$output[2] = array('value'=>2,'text'=>Yii::t('member','ex_member'));
	
		return $output;
	}	
	
	public static function getUserActivities() {
		$output[0] = array('value'=>'Aktiv','text'=>Yii::t('member','Aktiv'));
		$output[1] = array('value'=>'InAktiv','text'=>Yii::t('member','InAktiv'));
		$output[2] = array('value'=>'SemiAktiv','text'=>Yii::t('member','SemiAktiv'));
		
		return $output;		
	}
	
	public function getMemberSeit() {
		if(empty($this->member_since)) {
			if($this->member_flag == 1) {
				return 'undefined';
			} else {
				return 'no member';
			}
		} else {
			return Yii::app()->dateFormatter->formatDateTime($this->member_since,"medium",false);
		}
	}
	
	public function getMemberSquads() {
		$squads = $this->getSquads();
		
		$out = array();
		
		$tag = array();
		$ids = array();
		if($squads != null) {
			foreach($squads as $k => $v) {
				$tag[] = $v->squad->squad_tag;
				$ids[] = $v->squad_id; 
				$out[] = array('id'=>$v->squad_id,'text'=>$v->squad->squad_tag);
			}
		}

		#GFunctions::pre($tag);
		#GFunctions::pre($ids);
		
		$str_tag = implode(', ',$tag);
		$str_ids = implode(',',$ids);
		
		$output = CHtml::link($str_tag,'#',array('data-pk'=>$this->user_id,'class'=>'editable editable-click','data-value'=>$str_ids, 'rel'=>'User_squads', 'id'=>'User_squads_'.$this->user_id));
		//$output = '<a href="#" data-pk="'.$this->user_id.'" class="editable editable-click" data-value="'.$str_ids.'">'.$str_tag.'</a>';
		
		return $output;
		return $str_ids;
	}

	public function getSquads() {
		$criteria = new CDbCriteria();
		$criteria->condition = 'user_id =:user_id';
		$criteria->params = array(':user_id'=>$this->user_id);
		
		$squads = User2Squad::model()->with('squad')->findAll($criteria);
		return $squads;		
	} 
	
}