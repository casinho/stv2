<?php
class picOfDaMonth extends CWidget {
	
	public function run() {
		
		$potm = Potm::model()->findByAttributes(array('aktiv'=>1));
		if($potm != null) {
			$this->render('picOfDaMonth',array('potm'=>$potm));
		}
	}
}
?>