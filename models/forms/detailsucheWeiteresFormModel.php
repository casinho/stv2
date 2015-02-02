<?php
class detailsucheWeiteresFormModel extends CFormModel {
	public $vertragsende;
	public $rueckennummer;
	public $kapitaen;
	public $nationalitaet;
	public $zweite_nationalitaet;
	public $kontinent;
	public $wettbewerb;
	public $klassen;
	public $nationalspieler;
	
	public function rules() {
		return array(
			array('vertragsende, rueckennummer, kapitaen, nationalitaet, zweite_nationalitaet, kontinent, wettbewerb, klassen, nationalspieler', 'safe'),
		);
	}
}
?>
