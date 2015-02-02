<?php

/**
 * This is the model class for table "statistik_ga".
 *
 * The followings are the available columns in table 'statistik_ga':
 * @property integer $pageviews
 * @property integer $unique_pageviews
 * @property integer $visits
 * @property double $exitrate
 * @property double $avgtimeonpage
 * @property double $entrancebouncerate
 * @property integer $day
 * @property integer $week
 * @property integer $month
 * @property string $year
 * @property string $datum
 */
class StatistikGa extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return StatistikGa the static model class
	 */
	
	public $vorwoche;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'statistik_ga';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pageviews, unique_pageviews, visits, exitrate, avgtimeonpage, entrancebouncerate, day, week, month, year, datum', 'required'),
			array('pageviews, unique_pageviews, visits, day, week, month', 'numerical', 'integerOnly'=>true),
			array('exitrate, avgtimeonpage, entrancebouncerate', 'numerical'),
			array('year', 'length', 'max'=>4),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('pageviews, unique_pageviews, visits, exitrate, avgtimeonpage, entrancebouncerate, day, week, month, year, datum', 'safe', 'on'=>'search'),
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
	
	public static function getVisitors($what='visits',$case=false) {
		switch ($case) {
			case 'all':
				$sql = "SELECT SUM(".$what.") FROM statistik_ga";
				break;
			case 'yesterday':
				$ts = time() - 60*60*24;
				$datum = date('Y-m-d',$ts);
				$sql = "SELECT ".$what." FROM statistik_ga WHERE datum = '".$datum."'";
				//GFunctions::pre($sql);
				break;
			case 'month':
				$sql = "SELECT SUM(".$what.") FROM statistik_ga WHERE month = '".date('m')."'";
				break;
			default:
				$sql = "SELECT SUM(".$what.") FROM statistik_ga";
		}
		$res = Yii::app()->db->createCommand($sql)->queryScalar();
		return $res;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'pageviews' => 'Pageviews',
			'vorwoche' => 'Vorwoche',
			'unique_pageviews' => 'Unique Pageviews',
			'visits' => 'Visits',
			'exitrate' => 'Exitrate',
			'avgtimeonpage' => 'Avgtimeonpage',
			'entrancebouncerate' => 'Entrancebouncerate',
			'day' => 'Day',
			'week' => 'Week',
			'month' => 'Month',
			'year' => 'Year',
			'datum' => 'Datum',
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

		$criteria->compare('pageviews',$this->pageviews);
		$criteria->compare('unique_pageviews',$this->unique_pageviews);
		$criteria->compare('visits',$this->visits);
		$criteria->compare('exitrate',$this->exitrate);
		$criteria->compare('avgtimeonpage',$this->avgtimeonpage);
		$criteria->compare('entrancebouncerate',$this->entrancebouncerate);
		$criteria->compare('day',$this->day);
		$criteria->compare('week',$this->week);
		$criteria->compare('month',$this->month);
		$criteria->compare('year',$this->year,true);
		$criteria->compare('datum',$this->datum,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}