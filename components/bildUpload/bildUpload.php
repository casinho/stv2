<?php
class BildUpload extends CWidget {
	
	public $form;
	public $bild;
	public $useXUpload;
	public $xupload;
	
	public function run() {
		$this->render('bildUpload');
	}
}
?>