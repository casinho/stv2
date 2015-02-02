<?php

/**
 * This is the model class for table "map2clanwar".
 *
 * The followings are the available columns in table 'map2clanwar':
 * @property string $auto_id
 * @property string $clanwar_id
 * @property string $map_id
 * @property integer $map_nr
 * @property integer $score_st
 * @property integer $score_enemy
 * @property string $enemy_id
 * @property integer $wertung
 * @property string $report
 */
class Map2Clanwar extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Map2clanwar the static model class
	 */
	
	public $loeschen = false;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'map2clanwar';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('clanwar_id, map_id, enemy_id', 'required'),
			array('map_nr, score_st, score_enemy, wertung', 'numerical', 'integerOnly'=>true),
			array('clanwar_id, map_id, enemy_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('auto_id, clanwar_id, map_id, map_nr, score_st, score_enemy, enemy_id, wertung, report', 'safe', 'on'=>'search'),
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
			'map' => array(
					self::HAS_ONE,
					'File',
					array('id'=>'map_id'),
			),		
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'auto_id' => 'Auto',
			'clanwar_id' => 'Clanwar',
			'map_id' => 'Map',
			'map_nr' => 'Map Nr',
			'score_st' => 'Score St',
			'score_enemy' => 'Score Enemy',
			'enemy_id' => 'Enemy',
			'wertung' => 'Wertung',
			'report' => 'Report',
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

		$criteria->compare('auto_id',$this->auto_id,true);
		$criteria->compare('clanwar_id',$this->clanwar_id,true);
		$criteria->compare('map_id',$this->map_id,true);
		$criteria->compare('map_nr',$this->map_nr);
		$criteria->compare('score_st',$this->score_st);
		$criteria->compare('score_enemy',$this->score_enemy);
		$criteria->compare('enemy_id',$this->enemy_id,true);
		$criteria->compare('wertung',$this->wertung);
		$criteria->compare('report',$this->report,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function beforeSave() {

		if($this->score_st > $this->score_enemy) {
			$this->wertung = 3;
		} elseif($this->score_st < $this->score_enemy) {
			$this->wertung = 2;
		} else {
			$this->wertung = 1;
		}
		
		
		return parent::beforeSave();
	}	
	
}