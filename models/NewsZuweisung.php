<?php

/**
 * This is the model class for table "newszuweisung".
 *
 * The followings are the available columns in table 'newszuweisung':
 * @property string $news_id
 * @property string $fremd_id
 * @property string $zuweisung
 */
class NewsZuweisung extends CActiveRecord
{
	
	public $typ;
	public $modeldata;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'newszuweisung';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fremd_id, zuweisung', 'required'),
			array('fremd_id', 'length', 'max'=>11),
			array('zuweisung', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('news_id, fremd_id, zuweisung', 'safe', 'on'=>'search'),
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
			'news_id' => 'News',
			'fremd_id' => 'Fremd',
			'zuweisung' => 'Zuweisung',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('news_id',$this->news_id,true);
		$criteria->compare('fremd_id',$this->fremd_id,true);
		$criteria->compare('zuweisung',$this->zuweisung,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewsZuweisung the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
	public function holeNewsVerlinkungen($news_id=0,$limit=10) {
		$criteria = new CDbCriteria();
		//$criteria->group = 'zuweisung, fremd_id';
		$criteria->condition = 'news_id = :news_id';
		$criteria->params = array(':news_id' => $news_id);
		$criteria->limit = $limit;
	
		$result = self::model()->findAll($criteria);
	
		foreach($result as $k => $v) {
	
			$v['typ'] 	= $v['zuweisung'];
			if($v['zuweisung']=='member') {
				$v['modeldata'] = User::model()->findByPk($v['fremd_id']);
			} else {
				$model = ucfirst($v['zuweisung']);
				$v['modeldata'] = $model::model()->findByPk($v['fremd_id']);
			}
				
		}
	
		$output = new CArrayDataProvider($result, array(
				'keyField' => 'zuweisung_id',
				'pagination'=>array(
						'pageSize'=>10,
				),
		));
	
	
		return $output;
	
	
	}	
	
}
