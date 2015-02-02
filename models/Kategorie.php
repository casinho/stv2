<?php

/**
 * This is the model class for table "kategorie".
 *
 * The followings are the available columns in table 'kategorie':
 * @property integer $id
 * @property string $name
 * @property string $tag
 * @property string $pic
 * @property string $url
 * @property string $status
 * @property string $history
 * @property string $try
 * @property string $trytext
 * @property string $warscript
 * @property integer $newsscript
 */
class Kategorie extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'kategorie';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('history, trytext, newsscript', 'required'),
			array('newsscript', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>20),
			array('tag', 'length', 'max'=>10),
			array('pic, url', 'length', 'max'=>100),
			array('status', 'length', 'max'=>2),
			array('try', 'length', 'max'=>5),
			array('warscript', 'length', 'max'=>1),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, tag, pic, url, status, history, try, trytext, warscript, newsscript', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'tag' => 'Tag',
			'pic' => 'Pic',
			'url' => 'Url',
			'status' => 'Status',
			'history' => 'History',
			'try' => 'Try',
			'trytext' => 'Trytext',
			'warscript' => 'Warscript',
			'newsscript' => 'Newsscript',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('pic',$this->pic,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('try',$this->try,true);
		$criteria->compare('trytext',$this->trytext,true);
		$criteria->compare('warscript',$this->warscript,true);
		$criteria->compare('newsscript',$this->newsscript);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Kategorie the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
