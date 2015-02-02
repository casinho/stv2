<?php

/**
 * This is the model class for table "Clans".
 *
 * The followings are the available columns in table 'Clans':
 * @property string $clan_id
 * @property string $clan
 * @property string $tag
 * @property string $image
 * @property string $claninfo
 * @property string $homepage
 * @property string $channel
 * @property integer $land_id
 * @property integer $ctf_flag
 * @property integer $tdm_flag
 * @property integer $as_flag
 * @property integer $soccer_flag
 * @property integer $insta_flag
 */
class Clans extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Clans the static model class
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
		return 'clans';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('clan, tag, land_id', 'required', 'on'=>'insert, update'),
			array('clan, tag, land_id', 'required', 'on'=>'form'),
			array('land_id, ctf_flag, tdm_flag, as_flag, soccer_flag, insta_flag', 'numerical', 'integerOnly'=>true),
			array('clan, image, homepage, channel', 'length', 'max'=>255),
			array('tag', 'length', 'max'=>40),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('clan_id, clan, tag, image, claninfo, homepage, channel, land_id, ctf_flag, tdm_flag, as_flag, soccer_flag, insta_flag', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
				'land' => array(
						self::HAS_ONE,
						'Flaggen',
						array('id'=>'land_id'),
				),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'clan_id' => 'Clan',
			'clan' => 'Clan',
			'tag' => 'Tag',
			'image' => 'Image',
			'claninfo' => 'Claninfo',
			'homepage' => 'Homepage',
			'homepage_flag' => Yii::t('clans','homepage_erreichbar'),
			'channel' => 'Channel',
			'land_id' => 'Land',
			'ctf_flag' => 'Ctf Flag',
			'tdm_flag' => 'Tdm Flag',
			'as_flag' => 'As Flag',
			'soccer_flag' => 'Soccer Flag',
			'insta_flag' => 'Insta Flag',
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

		$criteria->compare('clan_id',$this->clan_id,true);
		$criteria->compare('clan',$this->clan,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('claninfo',$this->claninfo,true);
		$criteria->compare('homepage',$this->homepage,true);
		$criteria->compare('channel',$this->channel,true);
		$criteria->compare('land_id',$this->land_id);
		$criteria->compare('ctf_flag',$this->ctf_flag);
		$criteria->compare('tdm_flag',$this->tdm_flag);
		$criteria->compare('as_flag',$this->as_flag);
		$criteria->compare('soccer_flag',$this->soccer_flag);
		$criteria->compare('insta_flag',$this->insta_flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getHomepageStatus($data) {
		if($data['homepage_flag']==0) {
			$anzeige 	= Yii::t('clanwars','offline');
			$color 		= 'orange'; 
			$url 		= 'http://web.archive.org/web/*/'.$data['homepage'];
		} else {
			$anzeige = Yii::t('clanwars','available');
			$color 		= 'green';
			$url 		= $data['homepage'];
		}
		if(!empty($data['homepage'])) {
			return CHtml::link($anzeige,$url,array('class'=>$color, 'target'=>'_blank'));
		}
	}
	
	public function getHeadline() {
		return CHtml::link($this->clan,Yii::app()->createUrl('clans/detail',array('id'=>$this->clan_id,'seo'=>GFunctions::normalisiereString($this->clan))));
	}	
	
	public function getLink($view='detail') {
		return Yii::app()->createUrl('clans/'.$view,array('id'=>$this->clan_id,'seo'=>GFunctions::normalisiereString($this->clan)));
	}
	
	public function getName() {
		return $this->clan;
	}
	
	public function holeClanwars() {
	
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
	
		$sql 		= "SELECT COUNT(*) FROM clanwars WHERE wertung > 0 AND enemy_id = ".$this->clan_id."";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
	
		$sql		= "SELECT w.*,COUNT(k.kommentar_id) AS anzahl,c.clan,c.clan_id,c.tag,s.squad_name,f.flaggenname,f.nationalname,l.id AS liga_id,l.text AS liga,l.tag AS liga_tag FROM clanwars AS w LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = w.id AND k.zuweisung = 'clanwars' LEFT JOIN clans AS c ON c.clan_id = w.enemy_id LEFT JOIN flaggen AS f ON f.id = c.land_id LEFT JOIN squad AS s ON s.squad_id = w.squad_id LEFT JOIN link AS l ON l.id = w.liga_id WHERE w.enemy_id = ".$this->clan_id." GROUP BY w.id";
		$output 	= new CSqlDataProvider($sql,array(
				'totalItemCount' => $anzahl,
				//'sort'=>array( 'attributes'=>array( 'titel', 'letzte_antwort', 'letzte_beitrag_zeit' )),
				'sort' => $sort,
				//'pagination' => false,
		)
		);
		return $output;
	}
	
	public function holeAlleClans() {
	
		$sort = new CSort();
		$sort->defaultOrder = 'clan ASC';
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
		$attributes['fremd_id'] 	= $this->clan_id;
		$attributes['zuweisung']	= 'clans';
		KommentarZuweisung::model()->deleteAllByAttributes($attributes);
	}	
	
}