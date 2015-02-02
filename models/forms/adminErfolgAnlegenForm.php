<?php
class adminErfolgAnlegenForm extends CFormModel {
	public $bezeichnung_de;
	public $land_id;
	public $haupterfolg;
	public $kategorie;
	public $nm_flag;
	public $turnier;
	
	public function rules() {
		return array(
			// name, email, subject and body are required
			array('bezeichnung_de, land_id, haupterfolg, kategorie', 'required'),
			array('nm_flag, turnier', 'boolean')
		);
	}
}
?>
