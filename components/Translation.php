<?php
class Translation extends CApplicationComponent {
	static $messages = array();
	
	const ID='tmTrans';
	
	public static function missingTranslation($event) {
		if(Yii::app()->User->checkAccess('Superadmin')) {
			$attributes = array('kategorie' => $event->category, 'key' => $event->message);
			/*
			if(($model = SystemSpracheQuelle::model()->findByAttributes($attributes)) !== null) {
				self::$messages[$model->id] = array('sprache_quelle_id' => $model->id, 'sprache' => $event->language); 
			}
			*/
		}
		return $event;
		
		/*
        Yii::import('translate.models.MessageSource');
        $attributes=array('category'=>$event->category,'message'=>$event->message);
        if(($model=MessageSource::model()->find('message=:message AND category=:category',$attributes))===null){
            $model=new MessageSource();
            $model->attributes=$attributes;
            if(!$model->save())
                return Yii::log(TranslateModule::t('Message '.$event->message.' could not be added to messageSource table'));;
        }
        if($model->id){
            if($this->autoTranslate && substr($event->language,0,2)!==substr(Yii::app()->sourceLanguage,0,2)){//&& key_exists($event->language,$this->getGoogleAcceptedLanguages($event->language))
                Yii::import('translate.models.Message');
                $translation=$this->googleTranslate($event->message,$event->language,Yii::app()->sourceLanguage);
                if($translation===false)
                    return false;
                $messageModel=new Message;
                $messageModel->attributes=array('id'=>$model->id,'language'=>$event->language,'translation'=>$translation);
                if($messageModel->save())
                    $event->message=$translation;
                else
                    return Yii::log(TranslateModule::t('Message '.$event->message.' could not be translated with auto-translate'));
            }elseif(substr($event->language,0,2)!==substr(Yii::app()->sourceLanguage,0,2) || Yii::app()->getMessages()->forceTranslation){
                self::$messages[$model->id]=array('language'=>$event->language,'message'=>$event->message,'category'=>$event->category);
            }
        }
        return $event;
        */
    }
    
    public static function translateLink($label='Translate',$type='link'){
        $form=CHtml::form(Yii::app()->getController()->createUrl('adminsprache/translate'));
        if(count(self::$messages))
            foreach(self::$messages as $index=>$message)
                foreach($message as $name=>$value)
                    $form.=CHtml::hiddenField(self::ID."-missing[$index][$name]",$value);
        if($type==='button')
            $form.=CHtml::submitButton($label);
        else
            $form.=CHtml::linkButton($label);
        $form.=CHtml::endForm();
        return $form;
    }
}
?>