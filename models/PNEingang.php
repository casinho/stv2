<?php

/**
 * This is the model class for table "pn_eingang".
 *
 * The followings are the available columns in table 'pn_eingang':
 * @property string $pn_id
 * @property string $titel
 * @property integer $nachricht_id
 * @property datetime $pn_datum
 * @property integer $absender_id
 * @property integer $empfaenger_id
 * @property integer $weitergeleitet_flag
 * @property integer $gelesen
 * @property integer $update_user_id
 * @property datetime $update_datum
 * @property datetime $gelesen_datum
 * @property integer $alarm_id
 * @property integer $alarm_erledigt
 */
class PNEingang extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PNEingang the static model class
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
		return 'pn_eingang';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('titel, nachricht_id, absender_id, empfaenger_id', 'required'),
			array('weitergeleitet_flag, alarm_id', 'numerical', 'integerOnly'=>true),
			array('titel', 'length', 'max'=>250),
			array('absender_id, empfaenger_id, update_user_id', 'length', 'max'=>6),
			array('gelesen', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('pn_id, titel, nachricht_id, pn_datum, update_datum, gelesen_datum, absender_id, empfaenger_id, gelesen, alarm_id, alarm_erledigt, weitergeleitet_flag', 'safe', 'on'=>'search'),
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
			'alarmMeldung' => array (
					self::HAS_ONE,
					'pn_alarm',
					array (
							'alarm_id' => 'alarm_id'
					),
					'joinType' => 'INNER JOIN'
			),
		);
	}

	/**
	 * @param integer $user_id
	 * @param string $status (ungelesen|alle)
	 * @return integer
	 */
	public function getAnzahl($user_id, $status = 'ungelesen') {
		switch ($status) {
			case 'alle':
				$nachrichtenStatus = '';
				break;
			
			case 'ungelesen':
			default:
				$nachrichtenStatus = "AND gelesen = 0";
				break;
		}
		$sql = "SELECT COUNT(pn_id) FROM ".$this->tableName()." WHERE empfaenger_id = ".$user_id." ".$nachrichtenStatus;
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'pn_id' => 'Pn',
			'titel' => 'Titel',
			'nachricht_id' => 'Nachricht ID',
			'pn_datum' => 'Pn Datum',
			'update_datum' => 'Letztes Update Datum',
			'gelesen_datum' => 'Zuletzt Gelesen Datum',
			'absender_id' => 'Absender',
			'empfaenger_id' => 'Empfaenger',
			'update_user_id' => 'User ID des zuletzt Aktiven',
			'gelesen' => 'Gelesen',
			'alarm_id' => 'Alarm Id',
			'alarm_erledigt' => 'Alarm erledigt',
			'weitergeleitet_flag' => 'Weitergeleitet Flag',
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

		$criteria->compare('pn_id',$this->pn_id,true);
		$criteria->compare('titel',$this->titel,true);
		$criteria->compare('nachricht_id',$this->nachricht_id,true);
		$criteria->compare('pn_datum',$this->pn_datum,true);
		$criteria->compare('update_datum',$this->update_datum,true);
		$criteria->compare('gelesen_datum',$this->gelesen_datum,true);
		$criteria->compare('update_user_id',$this->update_user_id);
		$criteria->compare('absender_id',$this->absender_id,true);
		$criteria->compare('empfaenger_id',$this->empfaenger_id,true);
		$criteria->compare('gelesen',$this->gelesen,true);
		$criteria->compare('alarm_id',$this->alarm_id,true);
		$criteria->compare('alarm_erledigt',$this->alarm_erledigt,true);
		$criteria->compare('weitergeleitet_flag',$this->weitergeleitet_flag);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 
	 * diese Methode wird immer am PNEingang-Datensatz ausgeführt, welcher für den Absender sichtbar ist.
	 * @param integer $user_id
	 * @param string $nachricht
	 */
	public function saveAntwort($user_id, $nachricht) {

	}
	
	public function findByAbsenderId($absender_id) {
		$criteria=new CDbCriteria;
	    $criteria->condition = 'empfaenger_id='.Yii::app()->user->getId();
	    $criteria->compare('absender_id',$absender_id,true);
	    $criteria->order = 'update_datum DESC, pn_datum DESC';
	
	    return new CActiveDataProvider($this, array(
	        'criteria'=>$criteria,
	        'pagination'=>array(
	            'pageSize'=>15,
	        ),
	    ));
	}
	
	public function getHtmlLink($anzeige=false, $absender_id = null) {
		$anzeige = $this->getGekuerztenTitel();
		return CHtml::link($anzeige,$this->getUrl(), array('title' => $this->titel));
	}
	
	/**
	 *  @param page erwartet ein array mit key = pagename("PNEingang_page" oder "PNArchiv_page") und als value einen integer.
	 * 				Ist das array leer, wird keine page an den link angefügt.
	 */
	public function getUrl($absender_id = null, $page = array()) {
		$params['id'] = $this->pn_id;
		$params['seo'] = (strlen($this->titel) <= 1) ? Yii::t('pn', 'ohne_betreff') : GFunctions::normalisiereString(substr($this->titel, 0, 30));
		if (count($page) == 1) {
			$params = array_merge($params, $page);
		}
		if (!is_null($absender_id)) {
			$params['absender_id'] = $absender_id;
		}
		return Yii::app()->createUrl('pn/index', $params);
	}
	
	public function getGekuerztenTitel() {
		return self::titelKuerzen($this->titel);
	}
	
	public function getAlarmHtmlLink($anzeige=false) {
		$anzeige = self::titelKuerzen($this->titel);
		$params['id'] = $this->pn_id;
		$params['seo'] = GFunctions::normalisiereString(substr($this->titel, 0, 30));
		return CHtml::link($anzeige,Yii::app()->createUrl('privateNachrichten/alarmierteNachrichten', $params), array('title' => $this->titel));
	}
	
	public static function titelKuerzen($titel) {
		if (strlen($titel) > 50) {
			$betreff = explode("\n", wordwrap($titel, 50, "\n"));
			$anzeige = substr($betreff[0], 0, 50) . '...';
		} else {
			$anzeige = $titel;
		}
		return $anzeige;
	}
		
	/**
	 * 0   = Systemnachricht
	 * 693 = Administrator-Nachricht
	 */
	public function hasSystemAbsender() {
		// return ($this->absender_id == 0 || $this->absender_id == 693);
		return ($this->absender_id == 0);
	}
	
	public function aktiveAlarmmeldung() {
		return $this->alarm_id > 0 && $this->alarm_erledigt == 0; 
	}
	
	public function getAnzahlOffeneAlarmierungen() {
		$sql = "SELECT COUNT(alarm_id) FROM pn_eingang WHERE alarm_id > 0 AND alarm_erledigt = 0";
		return Yii::app()->db->createCommand($sql)->queryScalar();
	}
	
	/**
	 *  Adminfunktion
	 */
	public static function alarmAlsErledigtMarkieren($alarm_id) {
		$command = Yii::app()->dbMaster->createCommand();
		return $command->update('pn_eingang', array(
		    'alarm_erledigt'=> 1, 
		), 'alarm_id=:id', array(':id'=>$alarm_id));
	}
}