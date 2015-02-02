<?php
class ProjectWebUser extends CWebUser {
	public $loginUrl = array('profil/login');
	private $_model;
	
	protected function loadUser($id=null) {
		if($this->_model === null) {
			if($id !== null) {
				$this->_model = User::model()->findByPk($id);
			}
		}
		return $this->_model;
	}
	
	protected function beforeLogin($id,$states,$fromCookie) {
		$user = $this->loadUser($id);
		if(!is_object($user) || !$user->checkBanned()) {
			$this->logout();
			return false;
		} else {
			return true;
		}
	}
	
 	protected function afterLogin($fromCookie) {
 		$user = $this->loadUser($this->getId());
 		if(is_object($user)) {
 			$user->updateByPk($this->getId(),array('letzter_login'=>date('Y-m-d H:i:s')));
 		}
 		parent::afterLogin($fromCookie);
 	}
}
?>