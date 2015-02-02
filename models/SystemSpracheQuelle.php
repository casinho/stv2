<?php

/**
 * This is the model class for table "system_sprache_quelle".
 *
 * The followings are the available columns in table 'system_sprache_quelle':
 * @property string $id
 * @property string $kategorie
 * @property string $key
 * @property integer $vorkommen
 */
class SystemSpracheQuelle extends CActiveRecord
{
	public $value;
	public $value_de;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SystemSpracheQuelle the static model class
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
		return 'system_sprache_quelle';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('kategorie, key, aktualisiert', 'required'),
			array('kategorie, key', 'required'),
			array('vorkommen', 'numerical', 'integerOnly'=>true),
			array('kategorie', 'length', 'max'=>255),
			array('key', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('id, kategorie, key, vorkommen, aktualisiert', 'safe', 'on'=>'search'),
			array('id, kategorie, key, vorkommen', 'safe', 'on'=>'search'),
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
			'aktiveUebersetzung' => array(
				self::HAS_ONE,
				'SystemSpracheUebersetzt',
				array('sprache_quelle_id' => 'id'),
				'joinType' => 'INNER JOIN',
				'condition' => 'aktiv = 1',
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
			'kategorie' => 'Kategorie',
			'key' => 'Key',
			'vorkommen' => 'Vorkommen',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('kategorie',$this->kategorie,true);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('vorkommen',$this->vorkommen);
		//$criteria->compare('aktualisiert',$this->aktualisiert);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function deStringAusgeben() {
		$sql = "SELECT value 
				FROM system_sprache_uebersetzt
				WHERE sprache_quelle_id = ".$this->id." AND aktiv = 1 AND sprache = 'de'";
		$result = Yii::app()->db->createCommand($sql)->queryScalar();
		echo htmlspecialchars($this->key, ENT_QUOTES, "UTF-8");
		echo "<br><br>".htmlspecialchars($result, ENT_QUOTES, "UTF-8");
	}

	public static function getKategorien() {
		return self::model()->findAll(array('select' => 'DISTINCT(kategorie)'));
	}
	
	public static function getAktiveUebersetzungen($kategorie, $sprache) {
		return self::model()->with('aktiveUebersetzung')->findAll(array('condition' => 't.kategorie = :kategorie AND aktiveUebersetzung.sprache = :sprache', 'params' => array(':kategorie' => $kategorie, ':sprache' => $sprache)));
	}
	
	public static function getAnzahlFehlendeUebersetzungen($sprache) {
		$sql = "SELECT count(q.id)
				FROM system_sprache_quelle AS q
				INNER JOIN system_sprache_uebersetzt AS u ON q.id = u.sprache_quelle_id
				WHERE (u.value IS NULL OR u.value = '') AND u.aktiv = 1 AND q.kategorie != 'oldLang' AND sprache = '".$sprache."'";
		
		return Yii::app()->db->cache(CACHETIME_S)->createCommand($sql)->queryScalar();
		
		/*
		return self::model()->with(array(
			'aktiveUebersetzung' => array(
				'scopes' => array(
					'leereUebersetzung',
					'sprache' => $sprache
				),
			),
		))->findAll(array('condition' => 'kategorie != "oldLang"'));
		 */
	}
}