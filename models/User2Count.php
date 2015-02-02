<?php

/**
 * This is the model class for table "user2count".
 *
 * The followings are the available columns in table 'user2count':
 * @property string $auto_id
 * @property string $user_id
 * @property string $zuweisung
 * @property integer $freigeschaltet_flag
 * @property string $anzahl
 */
class User2Count extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user2count';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, zuweisung, anzahl', 'required'),
			array('freigeschaltet_flag', 'numerical', 'integerOnly'=>true),
			array('user_id, anzahl', 'length', 'max'=>10),
			array('zuweisung', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('auto_id, user_id, zuweisung, freigeschaltet_flag, anzahl', 'safe', 'on'=>'search'),
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
			'auto_id' => 'Auto',
			'user_id' => 'User',
			'zuweisung' => 'Zuweisung',
			'freigeschaltet_flag' => 'Freigeschaltet Flag',
			'anzahl' => 'Anzahl',
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

		$criteria->compare('auto_id',$this->auto_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('zuweisung',$this->zuweisung,true);
		$criteria->compare('freigeschaltet_flag',$this->freigeschaltet_flag);
		$criteria->compare('anzahl',$this->anzahl,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User2Count the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public static function updateCounts($user_id,$zuweisung,$plus,$anzahl) {
	
		if(empty($zuweisung)) {
			return false;
		}
		
		$model = User2Count::model()->findByAttributes(array('user_id'=>$user_id,'zuweisung'=>$zuweisung));
		
		if($model == null) {
			$model = new User2Count();
			$model->anzahl	= $anzahl; 
		} else {
			if($plus === true) {
				$model->anzahl+=$anzahl;
			} else {
				$model->anzahl-=$anzahl;
			}			
		}
		
		$model->user_id 	= $user_id;
		$model->zuweisung	= $zuweisung;

		$model->save(false);
	}	
	
}
