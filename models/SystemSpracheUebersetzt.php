<?php

/**
 * This is the model class for table "system_sprache_uebersetzt".
 *
 * The followings are the available columns in table 'system_sprache_uebersetzt':
 * @property string $id
 * @property string $sprache
 * @property string $sprache_quelle_id
 * @property string $value
 * @property string $version
 * @property integer $aktiv
 * @property string $create_time
 * @property string $last_modified_time
 * @property string $last_modified_user_id
 */
class SystemSpracheUebersetzt extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SystemSpracheUebersetzt the static model class
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
		return 'system_sprache_uebersetzt';
	}

	public function scopes() {
		return array(
			'aktiv' => array(
				'condition' => 'aktiv = 1',
			),
			'leereUebersetzung' => array(
				'condition' => '(value IS NULL OR value = "") AND aktiv = 1',
			),
			'vorhandeneUebersetzung' => array(
				'condition' => 'value IS NOT NULL',
			)
		);
	}

	public function sprache($sprache = 'de') {
		$this->getDbCriteria()->mergeWith(array(
			'condition' => 'sprache = :sprache',
			'params' => array(
				':sprache' => $sprache,
			)
		));
		return $this;
	}

	public function behaviors() {
		return array(
			'LazySaverBehavior',
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sprache, sprache_quelle_id, version, aktiv, create_time, last_modified_time, last_modified_user_id', 'required'),
			array('aktiv', 'numerical', 'integerOnly'=>true),
			array('sprache', 'length', 'max'=>6),
			array('sprache_quelle_id, version, last_modified_user_id', 'length', 'max'=>11),
			array('value', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sprache, sprache_quelle_id, value, version, aktiv, create_time, last_modified_time, last_modified_user_id', 'safe', 'on'=>'search'),
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
			'sprache' => 'Sprache',
			'sprache_quelle_id' => 'Sprache Quelle',
			'value' => 'Value',
			'version' => 'Version',
			'aktiv' => 'Aktiv',
			'create_time' => 'Create Time',
			'last_modified_time' => 'Last Modified Time',
			'last_modified_user_id' => 'Last Modified User',
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
		$criteria->compare('sprache',$this->sprache,true);
		$criteria->compare('sprache_quelle_id',$this->sprache_quelle_id,true);
		$criteria->compare('value',$this->value,true);
		$criteria->compare('version',$this->version,true);
		$criteria->compare('aktiv',$this->aktiv);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_modified_time',$this->last_modified_time,true);
		$criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getSprachen($sprache = NULL) {
		return $sprache === NULL ? self::model()->findAll(array('select' => 'DISTINCT(sprache)')) : self::model()->findAll(array('select' => 'DISTINCT(sprache)', 'condition' => 'sprache = :sprache', 'params' => array(':sprache' => $sprache)));
	}

	/**
	 * @var attributes array Muss die Elemente sprache, sprache_quelle_id und newValue enthalten
	 */
	public static function updateUebersetzung($attributes) {
		if(!isset($attributes['sprache']))
			throw new CException('sprache nicht übergeben');
		if(!isset($attributes['sprache_quelle_id']))
			throw new CException('sprache_quelle_id nicht übergeben');
		if(empty($attributes['newValue']))
			$attributes['newValue'] = NULL;

		$aktiveAttributes['sprache'] = $attributes['sprache'];
		$aktiveAttributes['aktiv'] = 1;
		$aktiveAttributes['sprache_quelle_id'] = $attributes['sprache_quelle_id'];
		$aktiveUebersetzung = SystemSpracheUebersetzt::model()->findByAttributes($aktiveAttributes);
		if(strcmp($aktiveUebersetzung->value, $attributes['newValue']) != 0) {
			$aktiveUebersetzung->aktiv = 0;
			$alteVersion = $aktiveUebersetzung->version;
			if($aktiveUebersetzung->save()) {
				$gleicheAttributes = $aktiveAttributes;
				$gleicheAttributes['aktiv'] = 0;
				$gleicheAttributes['value'] = $attributes['newValue'];
				$neueUebersetzung = SystemSpracheUebersetzt::model()->findByAttributes($gleicheAttributes);
				if(!is_object($neueUebersetzung)) {
					$neueUebersetzung = new SystemSpracheUebersetzt;
					$attributes['version'] = $alteVersion+1;
					$attributes['sprache'] = $attributes['sprache'];
					$attributes['sprache_quelle_id'] = $attributes['sprache_quelle_id'];
					$attributes['value'] = $attributes['newValue'];
					$attributes['create_time'] = date('Y-m-d H:i:s');
				}
				$attributes['aktiv'] = 1;
				$attributes['last_modified_time'] = date('Y-m-d H:i:s');
				$attributes['last_modified_user_id'] = Yii::app()->user->getId();
				$neueUebersetzung->attributes = $attributes;
				if(!$neueUebersetzung->save()) {
					echo "Fehler beim speichern der neuen Übersetzung - Vorherige Übersetzung aktiviert";
					$aktiveUebersetzung->aktiv = 1;
					$aktiveUebersetzung->save();
					return false;
				}
			} else {
				echo "Fehler beim deaktivieren der alten Übersetzung";
				return false;
			}
		}
		return true;
	}

	public static function getListDataUebersetzungen($quelle_id, $sprache) {
		$uebersetzungen = SystemSpracheUebersetzt::model()->findAllByAttributes(array('sprache_quelle_id' => $quelle_id, 'sprache' => $sprache));
		foreach($uebersetzungen as $zeile => $uebersetzung) {
			if(!is_null($uebersetzung->value)) continue;
			$uebersetzungen[$zeile]->value = '[KEINE ÜBERSETZUNG]';
		}
		return CHtml::listData($uebersetzungen, 'version', 'value');
	}

	public static function changeAktiveUebersetzung($attributes) {
		SystemSpracheUebersetzt::model()->updateAll(array('aktiv' => '0'), 'sprache_quelle_id = :quelle_id AND sprache = :sprache', array(':quelle_id' => $attributes['sprache_quelle_id'], ':sprache' => $attributes['sprache']));
		$new = SystemSpracheUebersetzt::model()->findByAttributes($attributes);
		$new->aktiv = 1;
		if($new->save()) {
			return true;
		} else {
			return false;
		}
	}

	public static function getAnzahlVersionen($sprache_quelle_id, $sprache) {
		$attr[':sprache_quelle_id'] = $sprache_quelle_id;
		$attr[':sprache'] = $sprache;

		$ar = SystemSpracheUebersetzt::model()->find(array('select' => 'MAX(version) AS version', 'condition' => 'sprache_quelle_id = :sprache_quelle_id AND sprache = :sprache', 'params' => $attr));
		return $ar['version'];
	}
}