<?php
class SubNaviWidget extends CWidget {
	public $subnavi;

    public function run() {
        $this->render('subNaviWidget', array('subnavi' => $this->subnavi));
    }
}
?>