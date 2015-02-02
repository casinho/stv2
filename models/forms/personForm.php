<?php
class personForm extends CFormModel {
	public $vorname;
	public $nachname;
	public $kuenstlername;
	public $nameImHeimatland;
	public $geburtsort;
	public $geburtsland;
	public $geburtstag;
	public $alterVon;
	public $alterBis;
	public $groesseVon;
	public $groesseBis;
	
	public function rules() {
		return array(
			array('geburtstag', 'date', 'format' => 'd.M.yyyy'),
			array('alterVon, alterBis, groesseVon, groesseBis', 'numerical'),
			array('vorname, nachname, kuenstlername, nameImHeimatland, geburtsort, geburtsland', 'safe'),
		);
	}
}
?>
