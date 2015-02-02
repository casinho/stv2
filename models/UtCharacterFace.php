<?php

/**
 * This is the model class for table "ut_character_face".
 *
 * The followings are the available columns in table 'ut_character_face':
 * @property integer $face_id
 * @property string $face
 * @property string $image
 * @property integer $skin_id
 */
class UtCharacterFace extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ut_character_face';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('face, image, skin_id', 'required'),
			array('skin_id', 'numerical', 'integerOnly'=>true),
			array('face', 'length', 'max'=>40),
			array('image', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('face_id, face, image, skin_id', 'safe', 'on'=>'search'),
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
			'skin'=>array(self::BELONGS_TO, 'UtCharacterSkin', 'skin_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'face_id' => 'Face',
			'face' => 'Face',
			'image' => 'Image',
			'skin_id' => 'Skin',
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

		$criteria->compare('face_id',$this->face_id);
		$criteria->compare('face',$this->face,true);
		$criteria->compare('image',$this->image,true);
		$criteria->compare('skin_id',$this->skin_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UtCharacterFace the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
