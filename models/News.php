<?php

/**
 * This is the model class for table "news".
 *
 * The followings are the available columns in table 'news':
 * @property integer $id
 * @property string $titel
 * @property string $text
 * @property string $name
 * @property string $email

 * @property integer $kategorie_id
 * @property integer $poster_id
 * @property string $wichtig
 */
class News extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return News the static model class
	 */
	public $url;
	public $alt;
	
	private $_naechsteNews;
	private $_vorherigeNews;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'news';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('text, titel, slidertext', 'required'),
			array('kategorie_id, poster_id, big_image_id, image_id', 'numerical', 'integerOnly'=>true),
			array('titel', 'length', 'max'=>150),
			array('name, email', 'length', 'max'=>100),
			array('datum', 'length', 'max'=>20),
			array('wichtig', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, titel, text, slidertext, big_image_id, image_id, name, email, datum, kategorie_id, poster_id, wichtig', 'safe', 'on'=>'search'),
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
			'links' => array(
					self::HAS_MANY,
					'LinkZuweisung',
					array('fremd_id'=>'id'),
					'on'=>'zuweisung = "news"'
			),
			'bigImage' => array(
					self::HAS_ONE,
					'Files',
					array('file_id'=>'big_image_id'),
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
			'titel' => 'Titel',
			'slidertext' => 'Slidertext',	
			'text' => 'Text',
			'name' => 'Name',
			'email' => 'Email',
			'date' => 'Date',
			'kategorie_id' => 'Kategorie',
			'poster_id' => 'Poster',
			'wichtig' => 'Wichtig',
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
		$criteria->compare('titel',$this->titel,true);
		$criteria->compare('text',$this->text,true);
		$criteria->slidertext('text',$this->slidertext,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('kategorie_id',$this->kategorie_id);
		$criteria->compare('poster_id',$this->poster_id);
		$criteria->compare('wichtig',$this->wichtig,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function holeAlleNews() {
		
		$sort = new CSort();
		$sort->defaultOrder = 'n.datum DESC';
		$sort->attributes = array(
			'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl',
			),
			'titel' => array(
						'asc'=>'n.titel',
						'desc'=>'n.titel DESC',
			),
			'autor' => array(
						'asc'=>'n.name',
						'desc'=>'n.name DESC',
			),
			'datum' => array(
						'asc'=>'n.datum',
						'desc'=>'n.datum DESC',
			),			
        );    
		
		$sql 		= "SELECT COUNT(*) FROM news";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
		
		$sql		= "SELECT n.*,COUNT(k.kommentar_id) AS anzahl FROM news AS n LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = n.id AND k.zuweisung = 'news' GROUP BY n.id";
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

	public function holeAlleRelevantenNews($fremd_id = 0,$zuweisung = '') {
	
		$sort = new CSort();
		$sort->defaultOrder = 'n.datum DESC';
		$sort->attributes = array(
				'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl',
				),
				'titel' => array(
						'asc'=>'n.titel',
						'desc'=>'n.titel DESC',
				),
				'autor' => array(
						'asc'=>'n.name',
						'desc'=>'n.name DESC',
				),
				'datum' => array(
						'asc'=>'n.datum',
						'desc'=>'n.datum DESC',
				),
		);
	
		$sql 		= "SELECT COUNT(*) FROM newszuweisung AS nz INNER JOIN news AS n ON n.id = nz.news_id WHERE nz.fremd_id = ".$fremd_id." AND nz.zuweisung = '".$zuweisung."' GROUP BY n.id";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
	
		$sql		= "SELECT n.*,COUNT(k.kommentar_id) AS anzahl FROM newszuweisung AS nz INNER JOIN news AS n ON n.id = nz.news_id LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = n.id AND k.zuweisung = 'news' WHERE nz.fremd_id = ".$fremd_id." AND nz.zuweisung = '".$zuweisung."' GROUP BY n.id";
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
	
	
	
	public function holeStartseitenNews($limit=7) {
 		$criteria = new CDbCriteria();
        $criteria->order = 'datum DESC';
        $criteria->limit = $limit;
        $news = News::model()->findAll($criteria);
        
        $output = array();
     	foreach($news as $k => $v) {
     		$output[$k] = $v->attributes;
			$output[$k]['bigImage'] = $v->bigImage;
     		$output[$k]['url'] = Yii::app()->createUrl('news/detail',array('id'=>$v->id,'seo'=>GFunctions::normalisiereString($v->titel)));
     		if(strlen($v->text) > 55) {
     			$output[$k]['info'] = array(
     						'title' => $v->titel,
     						'text' => substr($v->text, 0, strpos($v->text, ' ', 60)).' ...'
     			);
     		} else {
     			$output[$k]['info'] = array(
     					'title' => $v->titel,
     					'text' => $v->text
     			);     			
     		}
     		 
     	}	
     	return $output;
	}

	public function holeRelevanteNews($limit=7,$fremd_id = 0, $zuweisung = false) {
		
		$neueZuweisungen = array();
		
		if($fremd_id == 0 && $zuweisung == false) {
		
			$criteria = new CDbCriteria();
			$criteria->condition 	= 'news_id = :news_id';
			$criteria->params = array(':news_id' => $this->id);
			$criteria->limit = $limit;
			
			$zuweisungen = NewsZuweisung::model()->findAll($criteria);
		
			$collector = array();
			$i = 0;
			foreach($zuweisungen as $k => $v) {
				$collector[$i]['zuweisung'] = $v->zuweisung;
				$collector[$i]['fremd_id'] 	= $v->fremd_id;
				$i+=1;
			}
	
			
			
			foreach($collector as $k => $v) {
				
				$criteria = new CDbCriteria();
				$criteria->condition 	= 'news_id != :news_id AND fremd_id = :fremd_id AND zuweisung = :zuweisung';
				$criteria->params = array(':news_id' => $this->id,':fremd_id' => $v['fremd_id'],':zuweisung' => $v['zuweisung']);
				$criteria->limit = $limit;
	
				
				$data = NewsZuweisung::model()->findAll($criteria);
				foreach($data as $key => $var) {
					//$neueZuweisung[$var->news_id] = $var;
					$neueZuweisungen[] = $var->news_id;
				}
			}
		} else {
			$criteria = new CDbCriteria();
			$criteria->condition 	= 'fremd_id = :fremd_id AND zuweisung = :zuweisung';
			$criteria->params = array(':fremd_id' => $fremd_id,':zuweisung' => $zuweisung);
			$criteria->limit = $limit;
			
			
			$data = NewsZuweisung::model()->findAll($criteria);
			foreach($data as $key => $var) {
				//$neueZuweisung[$var->news_id] = $var;
				$neueZuweisungen[] = $var->news_id;
			}			
		}		
		
 		$criteria = new CDbCriteria();
 		$criteria->addInCondition('id', $neueZuweisungen);
 		$criteria->order = 'datum DESC';
 		$criteria->limit = $limit;
 		
        $news = News::model()->findAll($criteria);
		
		
        $output = array();
     	foreach($news as $k => $v) {
     		$output[$k] = $v->attributes;
   			$output[$k]['bigImage'] = $v->bigImage;

     		$output[$k]['url'] = Yii::app()->createUrl('news/detail',array('id'=>$v->id,'seo'=>GFunctions::normalisiereString($v->titel)));
     		if(strlen($v->text) > 55) {
     			$output[$k]['info'] = array(
     						'title' => $v->titel,
     						'text' => substr($v->text, 0, strpos($v->text, ' ', 60)).' ...'
     			);
     		} else {
     			$output[$k]['info'] = array(
     					'title' => $v->titel,
     					'text' => $v->text
     			);     			
     		}
     		 
     	}	
     	
     	return $output;
	}
	
	
	public function setNaechsteNews() {

		$criteria = new CDbCriteria();
		$criteria->condition = 'id != :myID AND datum > :myDatum';
		$criteria->params = array(':myID'=>$this->id,':myDatum'=>$this->datum);
		$criteria->limit = 1;
		
		$this->_naechsteNews = $this->find($criteria);

		if($this->_naechsteNews != null) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getNaechsteNews() {
		return $this->_naechsteNews;
	}

	public function setVorherigeNews() {

		$criteria = new CDbCriteria();
		$criteria->condition = 'id != :myID AND datum < :myDatum';
		$criteria->params = array(':myID'=>$this->id,':myDatum'=>$this->datum);
		$criteria->order = 'datum DESC';		
		$criteria->limit = 1;		
		
		$this->_vorherigeNews = $this->find($criteria);
		
		if($this->_vorherigeNews != null) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getVorherigeNews() {
		return $this->_vorherigeNews;
	}	
	
	
	public function holeAktuelleNews() {
	}

	public function holeNewsMitTopKommentaren() {
			
	}	

	public function holeNewsMitNeuestenKommentaren() {
			
	}	
	
	public function getSliderPosition() {
		
		switch($this->slidertextposition) {
			case 0:
				$position = 'left:0;';
				break;
			case 1:
				$position = 'right:0;';
				break;
			case 2:
				$position = 'left:0;bottom:0;';
				break;
			case 3:
				$position = 'right:0;bottom:0;';
				break;
			default:
				$position = 'left:0;bottom:0;';
		}
		
		return $position;
	}	
	
	public function sliderPositionen() {
		$optionen = array();
	
		$optionen[0] = Yii::t('news','oben-links');
		$optionen[1] = Yii::t('news','oben-rechts');
		$optionen[2] = Yii::t('news','unten-links');
		$optionen[3] = Yii::t('news','unten-rechts');
	
		return $optionen;
	}
	
	public function getHeadline() {
		return CHtml::link($this->titel,Yii::app()->createUrl('news/detail',array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($this->titel))));
	}	
	
	public function getLink() {
		return Yii::app()->createUrl('news/detail',array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($this->titel)));
	}
	
	public function getImage() {
		return $this->bigImage->file_name;
	}	
	
	public function getName() {
		return $this->titel;
	}
	
	protected function afterDelete() {
		parent::afterDelete();
		NewsZuweisung::model()->deleteAll('news_id=' .$this->id);
		
		$attributes['fremd_id'] 	= $this->id;
		$attributes['zuweisung']	= 'news';
		KommentarZuweisung::model()->deleteAllByAttributes($attributes);
		
	}	
	
}