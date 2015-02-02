<?php
class SearchController extends Controller
{
    /**
     * @var string index dir as alias path from <b>application.</b>  , default to <b>runtime.search</b>
     */
    private $_indexFiles = 'runtime.search';
    /**
     * (non-PHPdoc)
     * @see CController::init()
     */
    
    private $_highlightning = false;

    
	public function accessRules() {
		return array (
			array('allow',
					'actions'=>array('seach'),
					'users'=>array('*'),
			),
			array('allow',
					'actions'=>array('create'),
					'roles'=>array('Superadmin'),
			),
		);
	}    
    
    public function init(){
        Yii::import('application.vendors.*');
        require_once('Zend/Search/Lucene.php');
        parent::init(); 
    }
 
    
	public function actionSearch() {

		setlocale(LC_CTYPE, 'de_DE.utf8');
		//setlocale(LC_CTYPE, 'de_DE.iso-8859-1');
		
		Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(
			new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive ()
		);
		
        if (($term = Yii::app()->getRequest()->getParam('q', null)) !== null) {
        	
        	//$searchString = mb_convert_encoding($term, 'UTF-8');
        	
        	//GFunctions::pre($searchString);
        	
        	if($this->_highlightning === true) {
        	
	        	$signs = array('?','*','/');
	
	        	$highlight = true;
	        	
	        	if($high)
	        	
	        	foreach($signs as $k => $v) {
	        		if(strpos($term,$v)!==false) {
			        	$highlight = false;
		    	    }
	        	}
        	} else {
        		$highlight = $this->_highlightning;
        	} 
        	
        	        	
        	
	        $index 		= new Zend_Search_Lucene(Yii::getPathOfAlias('application.' . $this->_indexFiles));
	        
	        $results 	= $index->find($term);
	        $query 		= Zend_Search_Lucene_Search_QueryParser::parse($term);       
	        
	        #GFunctions::pre($index);
	        #GFunctions::pre($query);
	        /*
	        $userQuery = Zend_Search_Lucene_Search_QueryParser::parse($searchString);
	        
	        $pathTerm  = new Zend_Search_Lucene_Index_Term(
	        		Yii::getPathOfAlias('application.' . $this->_indexFiles),'path'
	        );
	        $pathQuery = new Zend_Search_Lucene_Search_Query_Term($pathTerm);
	        
	        $query = new Zend_Search_Lucene_Search_Query_Boolean();
	        $query->addSubquery($userQuery, true);
	        $query->addSubquery($pathQuery, true);
			
	        $results = $index->find($query);
	        */
	            
	        $dataProvider = new CArrayDataProvider($results,array(
	        		'pagination' => array(
	        			'pageSize' => 10,
	        		),
	        ));
	        
			/*
        	Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('UTF-8');
        	$analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();
        	
        	$searchTerm = mb_strtolower($term, 'UTF-8');
        	$query     = new Zend_Search_Lucene_Search_Query_Boolean();
        	$user_query= Zend_Search_Lucene_Search_QueryParser::parse('content:'.$searchTerm);
        	$query->addSubquery($user_query,true);        	
        	
        	GFunctions::pre($query);
        	$results = array();
        	*/
        	
        	//GFunctions::pre($query);
        	
	        $this->render('search', compact('results', 'term', 'query', 'highlight', 'dataProvider'));
        }
    }
    
    private function sanitize($input){
		return htmlentities(strip_tags($input));
	}    
}