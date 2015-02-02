<?php

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $coment
 * @property string $size
 * @property integer $hits
 * @property string $kat
 * @property string $bild
 * @property string $typ
 * @property string $poster_id
 * @property string $date
 * @property string $show_it
 */
class File extends CActiveRecord
{
	
	public $bild_h;
	public $bildurl;
	/**
	 * Returns the static model of the specified AR class.
	 * @return File the static model class
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
		return 'file';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required', 'on'=>'insert, update'),
			array('name', 'required', 'on'=>'form'),
			array('hits', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
			array('url, bild', 'length', 'max'=>255),
			array('size', 'length', 'max'=>12),
			array('kat', 'length', 'max'=>40),
			array('typ', 'length', 'max'=>3),
			array('poster_id', 'length', 'max'=>5),
			array('date', 'length', 'max'=>20),
			array('show_it', 'length', 'max'=>2),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, url, coment, size, hits, kat, bild, typ, poster_id, date, show_it', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'name' => 'Name',
			'url' => 'Url',
			'coment' => 'Coment',
			'size' => 'Size',
			'hits' => 'Hits',
			'kat' => 'Kat',
			'bild' => 'Bild',
			'typ' => 'Typ',
			'poster_id' => 'Poster',
			'date' => 'Date',
			'show_it' => 'Show It',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('coment',$this->coment,true);
		$criteria->compare('size',$this->size,true);
		$criteria->compare('hits',$this->hits);
		$criteria->compare('kat',$this->kat,true);
		$criteria->compare('bild',$this->bild,true);
		$criteria->compare('typ',$this->typ,true);
		$criteria->compare('poster_id',$this->poster_id,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('show_it',$this->show_it,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	protected function afterFind (){
        $this->bild = str_replace('maps/', '', $this->bild);
		if($this->bild=='http://') {
			$this->bild = '';
		}
        parent::afterFind ();
    }	
	
    public function holeAlleMaps() {
    
    	$sort = new CSort();
    	$sort->defaultOrder = 'f.id DESC';
    	$sort->attributes = array(
    			'kommentare' => array(
    					'asc'=>'anzahl',
    					'desc'=>'anzahl DESC',
    			),
    			'mapname' => array(
    					'asc'=>'f.name',
    					'desc'=>'f.name DESC',
    			),
    			'bild' => array(
    					'asc'=>'f.bild',
    					'desc'=>'f.bild DESC',
    			),    			
    			'user' => array(
    					'asc'=>'u.user_nick',
    					'desc'=>'u.user_nick DESC',
    			),
    			'show' => array(
    					'asc'=>'f.show_it',
    					'desc'=>'f.show_it DESC',
    			),
    			'gespiel' => array(
    					'asc'=>'anzahl',
    					'desc'=>'anzahl DESC',
    			),    			
    	);
    
    	$sql 		= "SELECT COUNT(*) FROM file WHERE typ = 1";
    	$anzahl 	= Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
    
    	$sql		= "SELECT f.*, u.user_nick, u.user_id,COUNT(auto_id) AS anzahl FROM file AS f LEFT JOIN user AS u ON u.user_id = f.poster_id LEFT JOIN map2clanwar AS m2c ON map_id = f.id WHERE f.typ = 1 GROUP BY f.id";
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
    
    public function getHeadline() {
    	return CHtml::link($this->name,Yii::app()->createUrl('maps/detail',array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($this->name))));
    }
    
    public function getLink($view = '') {
    	//return Yii::app()->createUrl('maps/'.$view,array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($this->name)));
    	if($view != '') {
    		return Yii::app()->createUrl($view,array('id'=>$this->id,'seo'=>GFunctions::normalisiereString($this->name)));
    	}
    	return Yii::app()->createUrl('maps/verwalten');
    }
    
    
}


