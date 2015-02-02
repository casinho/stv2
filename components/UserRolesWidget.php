<?php
class UserRolesWidget extends CWidget {
	public $dataProvider = array();
	public $columns = array();
	public $user_id;
	
	public function init() {
		$userRoles = Yii::app()->authManager->getRoles($this->user_id);
		$roles = array();
		
		foreach ($userRoles as $k => $v) {
			$roles[] = array('name' => $k); 
		}
		
		
		$this->dataProvider = new CArrayDataProvider($roles,array(
				'keyField' 			=> 'name',
				'totalItemCount' 	=> count($roles),
		));
		$this->columns = array(
					array(
						'name' => 'name',
						'type' => 'raw',
					//	'value'=>'CHtml::image("http://static.transfermarkt.net/static/wappen_small/".$data["verein_id"]."k.png")',
						'htmlOptions'=>array('class'=>'links'),
						'headerHtmlOptions'=>array('class'=>'hide'),
					),
		);
		
	}

    public function run() {
        $this->render('userRolesWidget', array());
    }
}
?>