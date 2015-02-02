<?php

/**
 * This is the model class for table "pn_archiv".
 *
 * The followings are the available columns in table 'pn_archiv':
 * @property string $pn_id
 * @property string $titel
 * @property integer $nachricht_id
 * @property string $pn_datum
 * @property string $absender_id
 * @property string $empfaenger_id
 * @property integer $beantwortet_flag
 * @property integer $weitergeleitet_flag
 * @property integer $update_user_id
 * @property integer $update_datum
 */
class PNArchiv extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PNArchiv the static model class
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
		return 'pn_archiv';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('titel, nachricht_id, pn_datum, update_datum, absender_id, empfaenger_id', 'required'),
			array('nachricht_id, beantwortet_flag, weitergeleitet_flag', 'numerical', 'integerOnly'=>true),
			array('titel', 'length', 'max'=>250),
			array('absender_id, empfaenger_id, update_user_id', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('pn_id, titel, nachricht_id, pn_datum, absender_id, empfaenger_id, beantwortet_flag, weitergeleitet_flag, update_datum, update_user_id', 'safe', 'on'=>'search'),
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
			'pn_id' => 'Pn',
			'titel' => 'Titel',
			'nachricht_id' => 'Nachricht',
			'pn_datum' => 'Pn Datum',
			'absender_id' => 'Absender',
			'empfaenger_id' => 'Empfaenger',
			'beantwortet_flag' => 'Beantwortet Flag',
			'weitergeleitet_flag' => 'Weitergeleitet Flag',
			'update_datum' => 'Datum letzter Änderung',
			'update_user_id' => 'Update User-Id',
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
		$criteria->compare('nachricht_id',$this->nachricht_id);
		$criteria->compare('pn_datum',$this->pn_datum,true);
		$criteria->compare('absender_id',$this->absender_id,true);
		$criteria->compare('empfaenger_id',$this->empfaenger_id,true);
		$criteria->compare('beantwortet_flag',$this->beantwortet_flag);
		$criteria->compare('weitergeleitet_flag',$this->weitergeleitet_flag);
		$criteria->compare('update_datum',$this->update_datum, true);
		$criteria->compare('update_user_id',$this->update_user_id, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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

	public function getGekuerztenTitel() {
		return PNEingang::titelKuerzen($this->titel);
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
		$params['seo'] = GFunctions::normalisiereString(substr($this->titel, 0, 30));
		if (count($page) == 1) {
			$params = array_merge($params, $page);
		}
		if (!is_null($absender_id)) {
			$params['absender_id'] = $absender_id;
		}
		return Yii::app()->createUrl('pn/archiv', $params);
	}
}