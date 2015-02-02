<?php
class adminTitelAnlegenForm extends CFormModel {
	public $bezeichnung_de;
	public $land_id;
	public $spieler_flag;
	public $trainer_flag;
	public $schiri_flag;
	public $wettbewerb_require;
	public $verein_require;
	public $land_require;
	public $tor_flag;
	public $jahr;
	
	public function rules() {
		return array(
			array('bezeichnung_de, land_id', 'required'),
			array('spieler_flag, trainer_flag, schiri_flag, wettbewerb_require, verein_require, land_require, tor_flag, jahr', 'boolean'),
		);
	}
}
?>
