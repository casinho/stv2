<?php

/**
 * This is the model class for table "kommentarzuweisung".
 *
 * The followings are the available columns in table 'kommentarzuweisung':
 * @property string $kommentar_id
 * @property string $kommentar
 * @property string $fremd_id
 * @property string $zuweisung
 * @property string $poster_id
 * @property string $poster_ip
 * @property string $name
 * @property string $email
 * @property string $datum
 * @property string $url
 * @property string $irc
 */
class KommentarZuweisung extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return KommentarZuweisung the static model class
	 */
	
	public $typ;
	public $modeldata;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'kommentarzuweisung';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('kommentar, fremd_id, zuweisung, poster_id, poster_ip, name', 'required'),
			array('fremd_id, poster_id', 'length', 'max'=>10),
			array('zuweisung, poster_ip', 'length', 'max'=>40),
			array('name, irc', 'length', 'max'=>50),
			array('email, url', 'length', 'max'=>150),
			array('datum', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('kommentar_id, kommentar, fremd_id, zuweisung, poster_id, poster_ip, name, email, datum, url, irc', 'safe', 'on'=>'search'),
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
				'user' => array(
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
			'kommentar_id' => 'Kommentar',
			'kommentar' => 'Kommentar',
			'fremd_id' => 'Fremd',
			'zuweisung' => 'Zuweisung',
			'poster_id' => 'Poster',
			'poster_ip' => 'Poster Ip',
			'name' => 'Name',
			'email' => 'Email',
			'datum' => 'Datum',
			'url' => 'Url',
			'irc' => 'Irc',
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

		$criteria->compare('kommentar_id',$this->kommentar_id,true);
		$criteria->compare('kommentar',$this->kommentar,true);
		$criteria->compare('fremd_id',$this->fremd_id,true);
		$criteria->compare('zuweisung',$this->zuweisung,true);
		$criteria->compare('poster_id',$this->poster_id,true);
		$criteria->compare('poster_ip',$this->poster_ip,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('datum',$this->datum,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('irc',$this->irc,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function holeAnzahlKommentare($zuweisung,$fremd_id) {
		$sql = "SELECT COUNT(*) FROM kommentarzuweisung WHERE fremd_id = ".$fremd_id." AND zuweisung = '".$zuweisung."'";
		$anzahl = Yii::app()->db->cache(60)->createCommand($sql)->queryScalar();
		return $anzahl;
	}
	
	public function holeLetzteKommentare($limit=10,$zuweisung=false) {
		$criteria = new CDbCriteria();
		//$criteria->group = 'zuweisung, fremd_id';
		if($zuweisung === false) {
			$criteria->addInCondition('zuweisung', array ('news','clanwars','clans','member','potm','maps','utserver'));
		} else {
			$criteria->addInCondition('zuweisung', array ($zuweisung));
		}
		$criteria->order = 't.datum DESC';
		$criteria->offset = 0;
		$criteria->limit = $limit;
		
		$result = KommentarZuweisung::model()->with('user')->findAll($criteria);
		
		foreach($result as $k => $v) {

			$v['typ'] 	= $v['zuweisung'];
			if($v['zuweisung']=='member') {
				$v['modeldata'] = User::model()->findByPk($v['fremd_id']);
			} elseif($v['zuweisung']=='maps') {
					$v['modeldata'] = File::model()->findByPk($v['fremd_id']);
			
			} else {
				$model = ucfirst($v['zuweisung']);
				$v['modeldata'] = $model::model()->findByPk($v['fremd_id']);
			}
			
		}
		
		
	
		$output = new CArrayDataProvider($result, array(
				'keyField' => 'kommentar_id',
				'pagination'=>array(
						'pageSize'=>10,
				),
		));
		
		
		return $output;
		
		
	}	
	
	private function getModel() {
		
	}
	
	
}