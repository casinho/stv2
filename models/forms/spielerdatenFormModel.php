<?php
class spielerdatenFormModel extends CFormModel {
	public $position;
	public $hauptposition;
	public $nebenposition1;
	public $nebenposition2;
	public $marktwertVon;
	public $marktwertBis;
	public $fuss;
	
	public function rules() {
		return array(
			array('position, hauptposition, nebenposition1, nebenposition2, marktwertVon, marktwertBis, fuss', 'numerical'),
		);
	}
}
?>
