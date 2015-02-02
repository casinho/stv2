<?php

/**
 * This is the model class for table "potm".
 *
 * The followings are the available columns in table 'potm':
 * @property integer $id
 * @property string $url
 * @property string $name
 * @property string $text
 * @property integer $aktiv
 */
class Potm extends CActiveRecord
{
	
	private $_naechstesPotm;
	private $_vorherigesPotm;	
	
	public $url_h;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Potm the static model class
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
		return 'potm';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,url', 'required'),
			array('aktiv', 'numerical', 'integerOnly'=>true),
			array('url, name', 'length', 'max'=>150),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, url, name, text, aktiv, datum, user_id', 'safe', 'on'=>'search'),
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
				'autor' => array(
						self::HAS_ONE,
						'User',
						array('user_id'=>'user_id'),
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
			'url' => 'Url',
			'name' => 'Name',
			'text' => 'Text',
			'aktiv' => 'Aktiv',
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
		$criteria->compare('url',$this->url,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('aktiv',$this->aktiv);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function setNaechstesPotm() {
	
		$criteria = new CDbCriteria();
		$criteria->condition = 'id != :myID AND id > :myID';
		$criteria->params = array(':myID'=>$this->id);
		$criteria->limit = 1;
	
		$this->_naechstesPotm = $this->find($criteria);
	
		if($this->_naechstesPotm != null) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getNaechstesPotm() {
		return $this->_naechstesPotm;
	}
	
	public function setVorherigesPotm() {
	
		$criteria = new CDbCriteria();
		$criteria->condition = 'id != :myID AND id < :myID';
		$criteria->params = array(':myID'=>$this->id);
		$criteria->order = 'id DESC';
		$criteria->limit = 1;
	
		$this->_vorherigesPotm = $this->find($criteria);
	
		if($this->_vorherigesPotm != null) {
			return true;
		} else {
			return false;
		}
	}
	
	public function holeAllePics() {
	
		$sort = new CSort();
		$sort->defaultOrder = 'p.id DESC';
		$sort->attributes = array(
				'kommentare' => array(
						'asc'=>'anzahl',
						'desc'=>'anzahl DESC',
				),
				'datum' => array(
						'asc'=>'w.datum',
						'desc'=>'w.datum DESC',
				),
				'user' => array(
						'asc'=>'u.user_nick',
						'desc'=>'u.user_nick DESC',
				),
				'name' => array(
						'asc'=>'p.name',
						'desc'=>'p.name DESC',
				),
				'tag' => array(
						'asc'=>'s.squad_tag',
						'desc'=>'s.squad_tag DESC',
				),
				'aktiv' => array(
						'asc'=>'p.aktiv',
						'desc'=>'p.aktiv DESC',
				),				
		);
	
		$sql 		= "SELECT COUNT(*) FROM potm";
		$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
	
		$sql		= "SELECT p.*,COUNT(k.kommentar_id) AS anzahl, u.user_nick, u.user_id FROM potm AS p LEFT JOIN kommentarzuweisung AS k ON k.fremd_id = p.id AND k.zuweisung = 'potm' LEFT JOIN user AS u ON u.user_id = p.user_id GROUP BY p.id";
		$output 	= new CSqlDataProvider($sql,array(
				'keyField' => 'id',
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
	
	
	public function afterSave(){
		parent::afterSave();
		if($this->aktiv == 1) {
			Potm::model()->updateAll(array('aktiv'=>0), 'id != '.$this->id);
		}
	}	

	protected function afterFind() 	{
		// convert to display format
		$this->url_h = $this->url;
		parent::afterFind ();
	}	
	
	public function getVorherigesPotm() {
		return $this->_vorherigesPotm;
	}	
	
	public function getHeadline() {
		return CHtml::link($this->name,Yii::app()->createUrl('potm/detail',array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($this->name))));
	}
	
	public function getLink($view = 'detail') {
		return Yii::app()->createUrl('potm/'.$view,array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($this->name)));
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
		$attributes['fremd_id'] 	= $this->id;
		$attributes['zuweisung']	= 'potm';
		KommentarZuweisung::model()->deleteAllByAttributes($attributes);
	}	
	
}