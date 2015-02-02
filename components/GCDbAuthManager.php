<?php

class GCDbAuthManager extends CDbAuthManager {

    public function init() {
        parent::init();
    }

    public function revoke($itemName,$userId) {
    	parent::revoke($itemName,$userId);
		if(get_class(Yii::app()) != 'CConsoleApplication') {
			$log = new AuthAssignmentLog();
			$log->itemname				= $itemName;
			$log->userid				= $userId;
			$log->bizrule				= '';
			$log->data					= '';
			$log->user_id_bearbeiter 	= Yii::app()->user->getId();
			$log->datum 				= new CDbExpression('NOW()');
			$log->save(false);
		}
    }

    public function assign($itemName,$userId,$bizRule=null,$data=null) {
    	parent::assign($itemName,$userId,$bizRule,$data);
		if(get_class(Yii::app()) != 'CConsoleApplication') {
    		$log = new AuthAssignmentLog();
    		$log->itemname				= $itemName;
    		$log->userid				= $userId;
    		$log->bizrule				= $bizRule;
    		$log->data					= serialize($data);
    		$log->user_id_bearbeiter 	= Yii::app()->user->getId();
    		$log->datum 				= new CDbExpression('NOW()');
    		$log->save(false);
		}
    }


}
?>
