<?php 
Yii::import('ext.tinymce4.*');

class TinyMceController extends CController {
	public function actions() {
		return array(
				'compressor' => array(
						'class' => 'TinyMceCompressorAction',
						'settings' => array(
								'compress' => true,
								'disk_cache' => true,
						)
				),
				'spellchecker' => array(
						'class' => 'TinyMceSpellcheckerAction',
				),
		);
	}
}
?>