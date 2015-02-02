<?php
class newsTeaser extends CWidget {
	
	public $news;
	public $relevant = false;
	public $pagination = false;

	public function run() {
		$array = array();
		$dataProvider = new CArrayDataProvider($this->news,$array);
		$this->render('newsTeaser',array('dataProvider'=>$dataProvider));
	}
	
}
?>