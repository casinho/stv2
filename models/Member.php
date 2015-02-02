<?php

/**
 * This is the model class for table "member".
 *
 * The followings are the available columns in table 'member':
 * @property integer $user_id
 * @property string $nick
 * @property string $realname
 * @property string $wohnort
 * @property string $wohnort_link
 * @property string $bundesland
 * @property string $flaggen
 * @property string $birthday
 * @property string $email
 * @property string $icq
 * @property string $status
 * @property string $aktiv
 * @property string $aufgaben
 * @property string $position
 * @property string $member_since
 * @property string $laston
 * @property string $fav_maps
 * @property string $hate_maps
 * @property string $fav_weapons
 * @property string $other_clans1
 * @property string $other_clans_link1
 * @property string $other_clans2
 * @property string $other_clans_link2
 * @property string $clanhistory
 * @property string $hobbies
 * @property string $fav_musik
 * @property string $fav_filme
 * @property string $web_tip1
 * @property string $web_tip_link1
 * @property string $web_tip2
 * @property string $web_tip_link2
 * @property string $web_tip3
 * @property string $web_tip_link3
 * @property string $idle_tip1
 * @property string $idle_tip_link1
 * @property string $idle_tip2
 * @property string $idle_tip_link2
 * @property string $idle_tip3
 * @property string $idle_tip_link3
 * @property string $cpu
 * @property string $ram
 * @property string $graka
 * @property string $soka
 * @property string $maus
 * @property string $moni
 * @property string $provi
 * @property string $urlprovi
 * @property string $konn
 * @property string $beruf
 * @property string $telefon
 * @property string $strasse
 * @property string $postleitzahl
 * @property string $content
 * @property string $membertype
 * @property integer $admin
 * @property string $pass
 * @property string $avatar
 * @property string $nt_job
 * @property integer $contest_n4su
 * @property integer $freigeschaltet_flag
 * @property integer $sperr_flag
 * @property string $datum_registriert
 * @property string $datum_validiert
 * @property string $sprache
 * @property string $letzte_ip
 * @property string $letzter_login
 */
class Member extends CActiveRecord
{
	
	public $tmpUserId;
	public $ungeleseneNachrichten;
	public $rememberMe;	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Member the static model class
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
		return 'member';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('aufgaben, clanhistory, content, freigeschaltet_flag, sperr_flag, datum_registriert, datum_validiert, sprache, letzte_ip, letzter_login', 'required'),
			array('admin, contest_n4su, freigeschaltet_flag, sperr_flag', 'numerical', 'integerOnly'=>true),
			array('nick, wohnort, wohnort_link, flaggen, email, letzte_ip', 'length', 'max'=>50),
			array('realname, bundesland, other_clans1, other_clans_link1, other_clans2, other_clans_link2, beruf', 'length', 'max'=>100),
			array('birthday, laston', 'length', 'max'=>40),
			array('icq, status, aktiv, member_since', 'length', 'max'=>20),
			array('position, fav_maps, hate_maps, hobbies, fav_musik, fav_filme, web_tip1, web_tip_link1, web_tip2, web_tip_link2, web_tip3, web_tip_link3, maus, moni, membertype, avatar, nt_job', 'length', 'max'=>255),
			array('fav_weapons, idle_tip1, idle_tip_link1, idle_tip2, idle_tip_link2, idle_tip3, idle_tip_link3, urlprovi, telefon, strasse', 'length', 'max'=>150),
			array('cpu, ram, graka, soka, provi, konn', 'length', 'max'=>200),
			array('postleitzahl', 'length', 'max'=>10),
			array('pass', 'length', 'max'=>30),
			array('sprache', 'length', 'max'=>4),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, nick, realname, wohnort, wohnort_link, bundesland, flaggen, birthday, email, icq, status, aktiv, aufgaben, position, member_since, laston, fav_maps, hate_maps, fav_weapons, other_clans1, other_clans_link1, other_clans2, other_clans_link2, clanhistory, hobbies, fav_musik, fav_filme, web_tip1, web_tip_link1, web_tip2, web_tip_link2, web_tip3, web_tip_link3, idle_tip1, idle_tip_link1, idle_tip2, idle_tip_link2, idle_tip3, idle_tip_link3, cpu, ram, graka, soka, maus, moni, provi, urlprovi, konn, beruf, telefon, strasse, postleitzahl, content, membertype, admin, pass, avatar, nt_job, contest_n4su, freigeschaltet_flag, sperr_flag, datum_registriert, datum_validiert, sprache, letzte_ip, letzter_login', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'nick' => 'Nick',
			'realname' => 'Realname',
			'wohnort' => 'Wohnort',
			'wohnort_link' => 'Wohnort Link',
			'bundesland' => 'Bundesland',
			'flaggen' => 'Flaggen',
			'birthday' => 'Birthday',
			'email' => 'Email',
			'icq' => 'Icq',
			'status' => 'Status',
			'aktiv' => 'Aktiv',
			'aufgaben' => 'Aufgaben',
			'position' => 'Position',
			'member_since' => 'Member Since',
			'laston' => 'Laston',
			'fav_maps' => 'Fav Maps',
			'hate_maps' => 'Hate Maps',
			'fav_weapons' => 'Fav Weapons',
			'other_clans1' => 'Other Clans1',
			'other_clans_link1' => 'Other Clans Link1',
			'other_clans2' => 'Other Clans2',
			'other_clans_link2' => 'Other Clans Link2',
			'clanhistory' => 'Clanhistory',
			'hobbies' => 'Hobbies',
			'fav_musik' => 'Fav Musik',
			'fav_filme' => 'Fav Filme',
			'web_tip1' => 'Web Tip1',
			'web_tip_link1' => 'Web Tip Link1',
			'web_tip2' => 'Web Tip2',
			'web_tip_link2' => 'Web Tip Link2',
			'web_tip3' => 'Web Tip3',
			'web_tip_link3' => 'Web Tip Link3',
			'idle_tip1' => 'Idle Tip1',
			'idle_tip_link1' => 'Idle Tip Link1',
			'idle_tip2' => 'Idle Tip2',
			'idle_tip_link2' => 'Idle Tip Link2',
			'idle_tip3' => 'Idle Tip3',
			'idle_tip_link3' => 'Idle Tip Link3',
			'cpu' => 'Cpu',
			'ram' => 'Ram',
			'graka' => 'Graka',
			'soka' => 'Soka',
			'maus' => 'Maus',
			'moni' => 'Moni',
			'provi' => 'Provi',
			'urlprovi' => 'Urlprovi',
			'konn' => 'Konn',
			'beruf' => 'Beruf',
			'telefon' => 'Telefon',
			'strasse' => 'Strasse',
			'postleitzahl' => 'Postleitzahl',
			'content' => 'Content',
			'membertype' => 'Membertype',
			'admin' => 'Admin',
			'pass' => 'Pass',
			'avatar' => 'Avatar',
			'nt_job' => 'Nt Job',
			'contest_n4su' => 'Contest N4su',
			'freigeschaltet_flag' => 'Freigeschaltet Flag',
			'sperr_flag' => 'Sperr Flag',
			'datum_registriert' => 'Datum Registriert',
			'datum_validiert' => 'Datum Validiert',
			'sprache' => 'Sprache',
			'letzte_ip' => 'Letzte Ip',
			'letzter_login' => 'Letzter Login',
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

		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('nick',$this->nick,true);
		$criteria->compare('realname',$this->realname,true);
		$criteria->compare('wohnort',$this->wohnort,true);
		$criteria->compare('wohnort_link',$this->wohnort_link,true);
		$criteria->compare('bundesland',$this->bundesland,true);
		$criteria->compare('flaggen',$this->flaggen,true);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('icq',$this->icq,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('aktiv',$this->aktiv,true);
		$criteria->compare('aufgaben',$this->aufgaben,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('member_since',$this->member_since,true);
		$criteria->compare('laston',$this->laston,true);
		$criteria->compare('fav_maps',$this->fav_maps,true);
		$criteria->compare('hate_maps',$this->hate_maps,true);
		$criteria->compare('fav_weapons',$this->fav_weapons,true);
		$criteria->compare('other_clans1',$this->other_clans1,true);
		$criteria->compare('other_clans_link1',$this->other_clans_link1,true);
		$criteria->compare('other_clans2',$this->other_clans2,true);
		$criteria->compare('other_clans_link2',$this->other_clans_link2,true);
		$criteria->compare('clanhistory',$this->clanhistory,true);
		$criteria->compare('hobbies',$this->hobbies,true);
		$criteria->compare('fav_musik',$this->fav_musik,true);
		$criteria->compare('fav_filme',$this->fav_filme,true);
		$criteria->compare('web_tip1',$this->web_tip1,true);
		$criteria->compare('web_tip_link1',$this->web_tip_link1,true);
		$criteria->compare('web_tip2',$this->web_tip2,true);
		$criteria->compare('web_tip_link2',$this->web_tip_link2,true);
		$criteria->compare('web_tip3',$this->web_tip3,true);
		$criteria->compare('web_tip_link3',$this->web_tip_link3,true);
		$criteria->compare('idle_tip1',$this->idle_tip1,true);
		$criteria->compare('idle_tip_link1',$this->idle_tip_link1,true);
		$criteria->compare('idle_tip2',$this->idle_tip2,true);
		$criteria->compare('idle_tip_link2',$this->idle_tip_link2,true);
		$criteria->compare('idle_tip3',$this->idle_tip3,true);
		$criteria->compare('idle_tip_link3',$this->idle_tip_link3,true);
		$criteria->compare('cpu',$this->cpu,true);
		$criteria->compare('ram',$this->ram,true);
		$criteria->compare('graka',$this->graka,true);
		$criteria->compare('soka',$this->soka,true);
		$criteria->compare('maus',$this->maus,true);
		$criteria->compare('moni',$this->moni,true);
		$criteria->compare('provi',$this->provi,true);
		$criteria->compare('urlprovi',$this->urlprovi,true);
		$criteria->compare('konn',$this->konn,true);
		$criteria->compare('beruf',$this->beruf,true);
		$criteria->compare('telefon',$this->telefon,true);
		$criteria->compare('strasse',$this->strasse,true);
		$criteria->compare('postleitzahl',$this->postleitzahl,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('membertype',$this->membertype,true);
		$criteria->compare('admin',$this->admin);
		$criteria->compare('pass',$this->pass,true);
		$criteria->compare('avatar',$this->avatar,true);
		$criteria->compare('nt_job',$this->nt_job,true);
		$criteria->compare('contest_n4su',$this->contest_n4su);
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
		
		$criteria->compare('nick', $_userGet['rbamName'], true);
		
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

	protected function afterDelete() {
		parent::afterDelete();
		/*
		$attributes['user_id'] = $this->tmpUserId;
		
		// alle Userrelevanten Daten löschen
		User2Daten::Model()->deleteAllByAttributes($attributes);
		User2Posts::Model()->deleteAllByAttributes($attributes);
		User2Validierung::Model()->deleteAllByAttributes($attributes);
		Favoriten::Model()->deleteAllByAttributes($attributes);

		unset($attributes);
		
		//$attributes['user_id_beitrag'] = $this->tmpUserId;
		ForumAbos::Model()->deleteAllByAttributes($attributes);
		*/
		$attributes['fremd_id'] 	= $this->user_id;
		$attributes['zuweisung']	= 'member';
		KommentarZuweisung::model()->deleteAllByAttributes($attributes);		
	}	
	
	protected function afterSave() {
		
		parent::afterSave();
		if($this->isNewRecord) {
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
			
			$this->isNewRecord = false;
		} 
	}	
	
	/*
	 * @return boolean validate user
	*/
	public function validatePassword($password){
		return $this->hashPassword($password) === $this->pass;
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
}