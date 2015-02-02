<?php

/**
 * This is the model class for table "user2voting".
 *
 * The followings are the available columns in table 'user2voting':
 * @property string $user_id
 * @property string $option_id
 *
 * The followings are the available model relations:
 * @property User $user
 */
class User2voting extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User2voting the static model class
	 */
	public $abstimmung;
	public $teilnehmer;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user2voting';
	}
	
	public function primaryKey() {
		return array('thema_id', 'user_id');
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, option_id, thema_id, forum_id', 'required'),
			array('user_id, option_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('user_id, option_id', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'user_id' => 'User',
			'option_id' => 'Option',
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

		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('option_id',$this->option_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getCommunityAbstimmung($thema_id) {

		$userAbstimmung = Yii::app()->db->createCommand()
		    ->select('COUNT(option_id) AS anzahl_stimmen, option_id')
		    ->from('user2voting')
		    ->where('thema_id = :thema_id', array(':thema_id'=>$thema_id))
		    ->group('option_id')
		    ->queryAll();
  		
		$attributes['thema_id'] = $thema_id;    
		$teilnehmer_gesamt 		= User2voting::Model()->countByAttributes($attributes);    
		
		$abstimmung_neu[1] = array('option_id' => 1, 'anzahl_stimmen' => 0, 'prozent' => 0);
		$abstimmung_neu[2] = array('option_id' => 2, 'anzahl_stimmen' => 0, 'prozent' => 0);
		$abstimmung_neu[3] = array('option_id' => 3, 'anzahl_stimmen' => 0, 'prozent' => 0);


		if($teilnehmer_gesamt > 0) {
			for($i=0; $i<3; $i++) {
				if(isset($userAbstimmung[$i])) {
					$abstimmung_neu[$userAbstimmung[$i]['option_id']]['anzahl_stimmen'] = $userAbstimmung[$i]['anzahl_stimmen'];
					$abstimmung_neu[$userAbstimmung[$i]['option_id']]['prozent'] = str_replace('.', ',', round($userAbstimmung[$i]['anzahl_stimmen']/$teilnehmer_gesamt*100,2));
				}	
			}
		}
		return $abstimmung_neu;
	}
	
	public function getCommunityAbstimmungTeilnehmer($thema_id) {

		$teilnehmer['fehlentscheidung'] = Yii::app()->db->createCommand()
		    ->select('u2v.user_id, u.user_nick, u.verein_id, v.vereinsname')
		    ->from('user2voting u2v')
		    ->join('user u', 'u.user_id = u2v.user_id')
		    ->join('verein v', 'v.verein_id = u.verein_id')
		    ->where('u2v.thema_id = :thema_id AND option_id = 2', array(':thema_id'=>$thema_id))
		    ->order('v.vereinsname ASC')
		    ->queryAll();

		$teilnehmer['richtig'] = Yii::app()->db->createCommand()
		    ->select('u2v.user_id, u.user_nick, u.verein_id, v.vereinsname')
		    ->from('user2voting u2v')
		    ->join('user u', 'u.user_id = u2v.user_id')
		    ->join('verein v', 'v.verein_id = u.verein_id')
		    ->where('u2v.thema_id = :thema_id AND option_id = 1', array(':thema_id'=>$thema_id))
		    ->order('v.vereinsname ASC')
		    ->queryAll();
		    
		$teilnehmer['neutral'] = Yii::app()->db->createCommand()
		    ->select('u2v.user_id, u.user_nick, u.verein_id, v.vereinsname')
		    ->from('user2voting u2v')
		    ->join('user u', 'u.user_id = u2v.user_id')
		    ->join('verein v', 'v.verein_id = u.verein_id')
		    ->where('u2v.thema_id = :thema_id AND option_id = 3', array(':thema_id'=>$thema_id))
		    ->order('v.vereinsname ASC')
		    ->queryAll();    
		
		return $teilnehmer;
	}
}