<?php

/**
 * This is the model class for table "land".
 *
 * The followings are the available columns in table 'land':
 * @property string $land_id
 * @property string $land
 * @property string $land_int
 * @property string $land_de
 * @property string $land_en
 * @property string $land_fr
 * @property string $land_it
 * @property string $land_tr
 * @property string $land_pt
 * @property string $kurz
 * @property string $iso3166
 * @property string $eu
 * @property integer $kontinent_id
 * @property string $wettbewerb_zyklus
 * @property string $em
 * @property string $gruppe
 * @property string $olympia
 * @property string $mwr_faktor
 * @property string $transferfenster_winter_von
 * @property string $transferfenster_winter_bis
 * @property string $transferfenster_winter_info
 * @property string $tld
 * @property string $land_dk
 * @property string $land_be
 * @property string $land_sk
 * @property string $land_si
 * @property string $land_ro
 * @property string $land_pl
 * @property string $land_gr
 * @property string $land_es
 * @property string $land_cz
 * @property string $land_hu
 * @property string $transferfenster_sommer_von
 * @property string $transferfenster_sommer_bis
 * @property string $transferfenster_sommer_info
 * @property integer $em_teilnahmen
 * @property integer $em_lostopf
 * @property string $land_bg
 * @property string $land_hr
 * @property string $land_lt
 * @property string $land_lv
 * @property string $land_sr
 * @property string $land_nl
 * @property string $transferfenster_check
 */
class Land extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Land the static model class
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
		return 'land';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('land_de, land_en, land_fr, land_it, land_tr, land_pt, iso3166, kontinent_id, wettbewerb_zyklus, em, gruppe, olympia, mwr_faktor, transferfenster_winter_von, transferfenster_winter_bis, transferfenster_winter_info, tld, land_dk, land_be, land_sk, land_si, land_ro, land_pl, land_gr, land_es, land_cz, land_hu, transferfenster_sommer_von, transferfenster_sommer_bis, transferfenster_sommer_info, em_teilnahmen, em_lostopf, land_bg, land_hr, land_lt, land_lv, land_sr, land_nl, transferfenster_check', 'required'),
			array('kontinent_id, em_teilnahmen, em_lostopf', 'numerical', 'integerOnly'=>true),
			array('land, land_int, land_de, land_en, land_fr', 'length', 'max'=>35),
			array('land_it, land_pt, land_dk, land_be, land_sk, land_si, land_ro, land_pl, land_gr, land_es, land_cz, land_hu, land_bg, land_hr, land_lt, land_lv, land_sr, land_nl', 'length', 'max'=>250),
			array('land_tr', 'length', 'max'=>100),
			array('kurz', 'length', 'max'=>3),
			array('iso3166, gruppe', 'length', 'max'=>2),
			array('eu, wettbewerb_zyklus, em, olympia, transferfenster_check', 'length', 'max'=>1),
			array('mwr_faktor', 'length', 'max'=>6),
			array('tld', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('land_id, land, land_int, land_de, land_en, land_fr, land_it, land_tr, land_pt, kurz, iso3166, eu, kontinent_id, wettbewerb_zyklus, em, gruppe, olympia, mwr_faktor, transferfenster_winter_von, transferfenster_winter_bis, transferfenster_winter_info, tld, land_dk, land_be, land_sk, land_si, land_ro, land_pl, land_gr, land_es, land_cz, land_hu, transferfenster_sommer_von, transferfenster_sommer_bis, transferfenster_sommer_info, em_teilnahmen, em_lostopf, land_bg, land_hr, land_lt, land_lv, land_sr, land_nl, transferfenster_check', 'safe', 'on'=>'search'),
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
			'land_id' => 'Land',
			'land' => 'Land',
			'land_int' => 'Land Int',
			'land_de' => 'Land De',
			'land_en' => 'Land En',
			'land_fr' => 'Land Fr',
			'land_it' => 'Land It',
			'land_tr' => 'Land Tr',
			'land_pt' => 'Land Pt',
			'kurz' => 'Kurz',
			'iso3166' => 'Iso3166',
			'eu' => 'Eu',
			'kontinent_id' => 'Kontinent',
			'wettbewerb_zyklus' => 'Wettbewerb Zyklus',
			'em' => 'Em',
			'gruppe' => 'Gruppe',
			'olympia' => 'Olympia',
			'mwr_faktor' => 'Mwr Faktor',
			'transferfenster_winter_von' => 'Transferfenster Winter Von',
			'transferfenster_winter_bis' => 'Transferfenster Winter Bis',
			'transferfenster_winter_info' => 'Transferfenster Winter Info',
			'tld' => 'Tld',
			'land_dk' => 'Land Dk',
			'land_be' => 'Land Be',
			'land_sk' => 'Land Sk',
			'land_si' => 'Land Si',
			'land_ro' => 'Land Ro',
			'land_pl' => 'Land Pl',
			'land_gr' => 'Land Gr',
			'land_es' => 'Land Es',
			'land_cz' => 'Land Cz',
			'land_hu' => 'Land Hu',
			'transferfenster_sommer_von' => 'Transferfenster Sommer Von',
			'transferfenster_sommer_bis' => 'Transferfenster Sommer Bis',
			'transferfenster_sommer_info' => 'Transferfenster Sommer Info',
			'em_teilnahmen' => 'Em Teilnahmen',
			'em_lostopf' => 'Em Lostopf',
			'land_bg' => 'Land Bg',
			'land_hr' => 'Land Hr',
			'land_lt' => 'Land Lt',
			'land_lv' => 'Land Lv',
			'land_sr' => 'Land Sr',
			'land_nl' => 'Land Nl',
			'transferfenster_check' => 'Transferfenster Check',
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

		$criteria->compare('land_id',$this->land_id,true);
		$criteria->compare('land',$this->land,true);
		$criteria->compare('land_int',$this->land_int,true);
		$criteria->compare('land_de',$this->land_de,true);
		$criteria->compare('land_en',$this->land_en,true);
		$criteria->compare('land_fr',$this->land_fr,true);
		$criteria->compare('land_it',$this->land_it,true);
		$criteria->compare('land_tr',$this->land_tr,true);
		$criteria->compare('land_pt',$this->land_pt,true);
		$criteria->compare('kurz',$this->kurz,true);
		$criteria->compare('iso3166',$this->iso3166,true);
		$criteria->compare('eu',$this->eu,true);
		$criteria->compare('kontinent_id',$this->kontinent_id);
		$criteria->compare('wettbewerb_zyklus',$this->wettbewerb_zyklus,true);
		$criteria->compare('em',$this->em,true);
		$criteria->compare('gruppe',$this->gruppe,true);
		$criteria->compare('olympia',$this->olympia,true);
		$criteria->compare('mwr_faktor',$this->mwr_faktor,true);
		$criteria->compare('transferfenster_winter_von',$this->transferfenster_winter_von,true);
		$criteria->compare('transferfenster_winter_bis',$this->transferfenster_winter_bis,true);
		$criteria->compare('transferfenster_winter_info',$this->transferfenster_winter_info,true);
		$criteria->compare('tld',$this->tld,true);
		$criteria->compare('land_dk',$this->land_dk,true);
		$criteria->compare('land_be',$this->land_be,true);
		$criteria->compare('land_sk',$this->land_sk,true);
		$criteria->compare('land_si',$this->land_si,true);
		$criteria->compare('land_ro',$this->land_ro,true);
		$criteria->compare('land_pl',$this->land_pl,true);
		$criteria->compare('land_gr',$this->land_gr,true);
		$criteria->compare('land_es',$this->land_es,true);
		$criteria->compare('land_cz',$this->land_cz,true);
		$criteria->compare('land_hu',$this->land_hu,true);
		$criteria->compare('transferfenster_sommer_von',$this->transferfenster_sommer_von,true);
		$criteria->compare('transferfenster_sommer_bis',$this->transferfenster_sommer_bis,true);
		$criteria->compare('transferfenster_sommer_info',$this->transferfenster_sommer_info,true);
		$criteria->compare('em_teilnahmen',$this->em_teilnahmen);
		$criteria->compare('em_lostopf',$this->em_lostopf);
		$criteria->compare('land_bg',$this->land_bg,true);
		$criteria->compare('land_hr',$this->land_hr,true);
		$criteria->compare('land_lt',$this->land_lt,true);
		$criteria->compare('land_lv',$this->land_lv,true);
		$criteria->compare('land_sr',$this->land_sr,true);
		$criteria->compare('land_nl',$this->land_nl,true);
		$criteria->compare('transferfenster_check',$this->transferfenster_check,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function landLink($array,$seite='detail') {
		return Chtml::link($array['land'],array('land/'.$seite,'id'=>$array['land_id'],'seo'=>GFunctions::normalisiereString($array['land']))); 
	}
}