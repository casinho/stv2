<?php
class UploadController extends Controller {

	public function accessRules() {
		return array(
				array('allow',  // allow all users to perform 'index' and 'view' actions
						'actions'=>array('*'),
						'users'=>array('@'),
				),
		);
	}
	
	
	
	
}
?>