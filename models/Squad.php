<?php

/**
 * This is the model class for table "squad".
 *
 * The followings are the available columns in table 'squad':
 * @property string $squad_id
 * @property integer $clanwar_flag
 * @property string $kategorie_id
 * @property integer $war_flag
 * @property integer $st_flag
 * @property string $squad_name
 * @property string $squad_tag
 * @property string $bild
 * @property string $info
 * @property string $history
 * @property integer $try_flag
 * @property string $try_info
 */
class Squad extends CActiveRecord
{
	
	public $leader 	= array();
	public $orga 	= array();
	public $zuweisung = array();
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Squad the static model class
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
		return 'squad';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('squad_name, squad_tag', 'required'),
			array('clanwar_flag, war_flag, st_flag, try_flag', 'numerical', 'integerOnly'=>true),
			array('kategorie_id', 'length', 'max'=>10),
			array('squad_name, squad_tag', 'length', 'max'=>20),
			array('bild', 'length', 'max'=>1580),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('squad_id, clanwar_flag, kategorie_id, war_flag, st_flag, squad_name, squad_tag, bild, info, history, try_flag, try_info', 'safe', 'on'=>'search'),
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
			'user2squad' => array(
					self::HAS_MANY,
					'User2Squad',
					array('squad_id'=>'squad_id'),
			),	
			'user' => array(
					self::HAS_MANY, 
					'User', 
					array('user_id'=>'user_id'), 
					'through'=>'user2squad',
					'order'=>'user_nick ASC'
			),
			'clanwars' =>  array(
					self::HAS_MANY,
					'Clanwars',
					array('squad_id'=>'squad_id'),
			),	
			'user2clanwar' => array(
					self::HAS_MANY,
					'User2Clanwar',
					array('id'=>'clanwar_id'),
					'through' => 'clanwars'
			),
			'noMember' => array(
					self::HAS_MANY,
					'User',
					array('user_id'=>'user_id',),
					'through'=>'user2clanwar',
					'condition'=>'member_flag = 2',
					'order'=>'user_nick ASC'
			),
/*				
			'noUser2Squad' => array(
					self::HAS_MANY,
					'User2Clanwar',
					array('id'=>'clanwar_id'),
					'through' => 'clanwars'
			),
			'noSquadMember' => array(
					self::HAS_MANY,
					'User',
					array('user_id'=>'user_id',),
					'through'=>'user2clanwar',
					'condition'=>'member_flag = 2',
					'order'=>'user_nick ASC'
			),
*/															
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'squad_id' => 'Squad',
			'clanwar_flag' => 'Clanwar Flag',
			'kategorie_id' => 'Kategorie',
			'war_flag' => 'War Flag',
			'st_flag' => 'St Flag',
			'squad_name' => 'Squad Name',
			'squad_tag' => 'Squad Tag',
			'bild' => 'Bild',
			'info' => 'Info',
			'history' => 'History',
			'try_flag' => 'Try Flag',
			'try_info' => 'Try Info',
		);
	}
	
	public function holeAlleSquads() {
	
		$sort = new CSort();
		$sort->defaultOrder = 's.squad_order ASC';
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
				'anzahl_member' => array(
						'asc'=>'anzahl_member',
						'desc'=>'anzahl_member DESC',
				),
				'squad' => array(
						'asc'=>'s.squad_name',
						'desc'=>'s.squad_name DESC',
				),
				'tag' => array(
						'asc'=>'s.squad_tag',
						'desc'=>'s.squad_tag DESC',
				),				
				'squad_order' => array(
						'asc'=>'squad_order',
						'desc'=>'squad_order DESC',
				),
		);
	
		$sql 		= "SELECT COUNT(*) FROM squad";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
	
		$sql		= "SELECT s.*,COUNT(k.kommentar_id) AS anzahl, COUNT(u2s.user_id) AS anzahl_member FROM squad AS s LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = s.squad_id AND k.zuweisung = 'squad' LEFT JOIN user2squad AS u2s ON u2s.squad_id = s.squad_id GROUP BY s.squad_id";
		$output 	= new CSqlDataProvider($sql,array(
				'keyField' => 'squad_id',
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
	

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('squad_id',$this->squad_id,true);
		$criteria->compare('clanwar_flag',$this->clanwar_flag);
		$criteria->compare('kategorie_id',$this->kategorie_id,true);
		$criteria->compare('war_flag',$this->war_flag);
		$criteria->compare('st_flag',$this->st_flag);
		$criteria->compare('squad_name',$this->squad_name,true);
		$criteria->compare('squad_tag',$this->squad_tag,true);
		$criteria->compare('bild',$this->bild,true);
		$criteria->compare('info',$this->info,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('try_flag',$this->try_flag);
		$criteria->compare('try_info',$this->try_info,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function holeSquadLeader() {
		$sql = "SELECT u.user_nick,u.user_id FROM user2squad AS u2s INNER JOIN user AS u ON u.user_id = u2s.user_id WHERE u2s.squad_id =".$this->squad_id." AND leader_flag = 1";
		$result	= Yii::app()->db->cache(600)->createCommand($sql)->queryAll();
		return $result;
	}
	
	public function holeSquadOrga() {
		$sql = "SELECT u.user_nick,u.user_id FROM user2squad AS u2s INNER JOIN user AS u ON u.user_id = u2s.user_id WHERE u2s.squad_id =".$this->squad_id." AND orga_flag = 1";
		$result	= Yii::app()->db->cache(600)->createCommand($sql)->queryAll();
		return $result;
	}	
	
	public function setLeaderData() {
		
		$leader = $this->holeSquadLeader();
		$orga	= $this->holeSquadOrga();
		
		if(!empty($leader)) {
			$l = array();
			foreach ($leader as $k => $v) {
				$l[] = User::model()->findByPk($v['user_id']);
			}
		}  else {
			$l[] = array();
		}
		if(!empty($orga)) {
			$o = array();
			foreach ($orga as $k => $v) {
				$o[] = User::model()->findByPk($v['user_id']);
			}
		}  else {
			$o[] = array();
		}		
		
		$this->leader = $l;
		$this->orga = $o;
		
		return true;
		
	}
	

	public function getEinsaetze($squad_id) {
		$sql = "SELECT COUNT(*) FROM clanwars AS c  WHERE c.squad_id = ".$squad_id."";
		$anzahl = Yii::app()->db->createCommand($sql)->queryScalar();
		return $anzahl;
	}
	
	public function getClanwarStatistik($squad_id,$wertung=0) {
		$sql = "SELECT COUNT(*) FROM clanwars AS c WHERE c.squad_id = ".$squad_id." AND c.wertung = ".$wertung."";
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
/*	
	public function beforeDelete($event) {
		$sql = "SELECT COUNT(*) FROM clanwars WHERE squad_id = ".$this->squad_id."";
		$numWars = Yii::app()->db->createCommand($sql)->queryScalar();
		if($numWar > 0 || $numWars == 0) {
			throw new CDbException('Möööp, Squad hat Clanwars ausgetragen! Löschung nicht möglich.');
		}
		return parent::beforeDelete($event);
	}	
*/	
	protected function afterDelete() {
		parent::afterDelete();
	
		$attributes['squad_id'] = $this->squad_id;
	
		// alle Userrelevanten Daten löschen
		User2Squad::model()->deleteAllByAttributes($attributes);
		Clanwars::model()->deleteAllByAttributes($attributes);
	
		unset($attributes);
		
		$attributes['fremd_id'] 	= $this->squad_id;
		$attributes['zuweisung']	= 'squad';
		KommentarZuweisung::model()->deleteAllByAttributes($attributes);		
	}
	

	public function getHeadline() {
		return CHtml::link($this->squad_name,Yii::app()->createUrl('member/squad',array('id'=>$this->squad_id,'seo'=>GFunctions::normalisiereString($this->squad_name))));
	}
	
	public function getLink($view = 'member/squad') {
		return Yii::app()->createUrl($view,array('id'=>$this->squad_id,'seo'=>GFunctions::normalisiereString($this->squad_name)));
	}	
	
	public function getImage() {
		return false;
	}	
	
	public function getName() {
		return $this->squad_name;
	}	
	
}