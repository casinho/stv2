<?php
class ConsoleUrlManager extends CUrlManager {
    public function createUrl($route,$params=array(),$ampersand='&') {
/*
    	if (!isset($params['language'])) {
            if (Yii::app()->user->hasState('language')) {
                Yii::app()->language = Yii::app()->user->getState('language');
            } elseif(isset(Yii::app()->request->cookies['language'])) {
                Yii::app()->language = Yii::app()->request->cookies['language']->value;
            }
            $params['lang']=Yii::app()->language;
        }
*/        
        $params['lang'] = 'de';
        return parent::createUrl($route, $params, $ampersand);
    }
    public function createAbsoluteUrl($route,$params=array(),$ampersand='&') {
/*
    	if (!isset($params['language'])) {
    		if (Yii::app()->user->hasState('language')) {
    			Yii::app()->language = Yii::app()->user->getState('language');
    		} elseif(isset(Yii::app()->request->cookies['language'])) {
    			Yii::app()->language = Yii::app()->request->cookies['language']->value;
    		}
    		$params['lang']=Yii::app()->language;
    	}
*/    	
    	$params['lang'] = 'de';
    	return parent::createAbsoluteUrl($route, $params, $ampersand);
    }
    
}
?>
