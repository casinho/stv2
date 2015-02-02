<?php
class PNWidget extends CWidget {
	
	public $type;
	public $anzahlUngeleseneNachrichten;
	public $anzahlPosteingang;
	public $anzahlPostausgang;
	
	public function init() {
		$methode = $this->type;
		$this->$methode();
	}
	
    public function run() {
    	$this->render($this->type);
    }
    
    protected function ungeleseneNachrichten() {
		$this->anzahlUngeleseneNachrichten = PNEingang::model()->getAnzahl(Yii::app()->user->getId());
    }
    
    protected function postfachStatistik() {
		$this->anzahlUngeleseneNachrichten = PNEingang::model()->getAnzahl(Yii::app()->user->getId());
		$this->anzahlPosteingang = PNEingang::model()->getAnzahl(Yii::app()->user->getId(), 'alle');
		$this->anzahlPostausgang = PNAusgang::model()->getAnzahl(Yii::app()->user->getId());
    }
}
?>