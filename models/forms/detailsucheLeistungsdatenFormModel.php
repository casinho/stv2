<?php
class detailsucheLeistungsdatenFormModel extends CFormModel {
	public $toreVon;
	public $toreBis;
	public $vorlagenVon;
	public $vorlagenBis;
	public $spieleVon;
	public $spieleBis;
	
	public function rules() {
		return array(
			array('toreVon, toreBis, vorlagenVon, vorlagenBis, spieleVon, spieleBis', 'safe'),
		);
	}
}
?>
