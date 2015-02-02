<?php
class ElfinderController extends Controller {
 
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('*'),
				'users'=>array('@'),
			),
		);
	}
 
    public function actions() {
    	
    	return array(
            // main action for elFinder connector
            'connector' => array(
                'class' => 'ext.elFinder.ElFinderConnectorAction',
                // elFinder connector configuration
                // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
                'settings' => array(
                    'roots' => array(
                        array(
                            'driver' => 'LocalFileSystem',
                            'path' => Yii::getPathOfAlias('webroot') . '/files/',
                            'URL' => Yii::app()->baseUrl . '/files/',
                            'alias' => 'Root Alias',
                            'acceptedName' => '/^[^\.].*$/', // disable creating dotfiles
                            'attributes' => array(
                                array(
                                    'pattern' => '/\/[.].*$/', // hide dotfiles
                                    'read' => false,
                                    'write' => false,
                                    'hidden' => true,
                                ),
                            ),
                        )
                    ),
                )
            ),
            // action for TinyMCE popup with elFinder widget
            'elfinderTinyMce' => array(
                'class' => 'ext.elFinder.TinyMceElFinderPopupAction',
                'connectorRoute' => 'connector', // main connector action id
            ),
            // action for file input popup with elFinder widget
            'elfinderFileInput' => array(
                'class' => 'ext.elFinder.ServerFileInputElFinderPopupAction',
                'connectorRoute' => 'connector', // main connector action id
            ),
        );
    }
 
}
?>