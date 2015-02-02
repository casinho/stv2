<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	//public $layout='application.views.layouts.main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();

	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	public $favoriten = array();
	public $anzahlPns = 0;
	
	public $loginClass;
	
	private $rewriteURLs = array();	
	
	public function init() {
		
		#GFunctions::pre($_GET);
		#GFunctions::pre(Yii::app()->request->requestUri);
		
		// test for query string variable mid_cat_id
		if(strpos(Yii::app()->request->requestUri,'index.php?page')!==false) {

			
			$this->setRewriteUrls();

			if (isset($this->rewriteURLs[$_GET['page']])) {
								
				Yii::app()->request->redirect($this->getRewriteUrl($_GET['page']), true, 301);
			} else {
				//Yii::app()->request->redirect('http://www.santitan.de', true, 404);
				//throw new CHttpException(404,'Diese Seite existiert nicht.');
			}
		}

		$standardSprache = 'de';
		
		if(isset(Yii::app()->request->preferredLanguage)) {
			if(($positionUnderscore = stripos(Yii::app()->request->preferredLanguage, '_')) !== false) {
				$YiiLang = substr(Yii::app()->request->preferredLanguage, 0, $positionUnderscore);
			} else {
				$YiiLang = Yii::app()->request->preferredLanguage;
			}
		} else {
			$YiiLang = $standardSprache;
		}
		
		Yii::app()->params->language = (isset($_GET['lang']) && in_array($_GET['lang'], Yii::app()->params->sprachen)) ? $_GET['lang'] : $YiiLang;
		Yii::app()->setLanguage(Yii::app()->params->language);
		
		/*
		if(!empty(Yii::app()->params->ersterWettbewerb)) {
			$this->getDropdownVereineByWettbewerb();
		}
		*/
		if(!Yii::app()->user->isGuest) {
			//$this->checkFavoriten();
			$this->anzahlPns 	= $this->getAnzahlPns();
		} else {
			$this->loginClass	= ' loginLink';
		}
		
	}

	private function getRewriteUrl($request_page) {
		
		switch($request_page) {
			case 'nt_news':
				return '/de/news';
				break;
			case 'clanwars':
				return '/de/clanwars';
				break;
			case 'kontakt':
				return '/de/impressum';
				break;
			case 'nt_clanwars':
				return '/de/clanwars';
				break;				
			case 'nt_squad':
				return '/de/member/squads';
				break;
			case 'clanwars_show':
				return $this->getClanwarUrl();
				break;
			case 'member':
				return $this->getMemberUrl();
				break;					
			case 'archiv':
				return $this->getNewsUrl('showid');
				break;
			case 'archiv':
				return $this->getNewsUrl('id');
				break;
			default:
				return '/';																						
		}
		
	}
	
	private function setRewriteUrls() {
		$this->rewriteURLs = array(
			'nt_news' => 1,
			'clanwars' => 1,
			'kontakt' => 1,
			'nt_clanwars' => 1,
			'nt_squad' => 1,
			'clanwars_show' => 1,	
			'member' => 1,
			'archiv' => 1,
			'news' => 1,
		);
	}
	
	private function getClanwarUrl() {
		if(isset($_GET['id'])) {
			$model = Clanwars::model()->findByPk($_GET['id']);
			if($model == null) {
				throw new CHttpException(404,'The requested page does not exist.');
			}
			return $model->getLink();
		} else {
			return '/';
		}
	}
	
	private function getMemberUrl() {
		if(isset($_GET['id'])) {
			$model = User::model()->findByPk($_GET['id']);
			if($model == null) {
				throw new CHttpException(404,'The requested page does not exist.');
			}			
			return $model->getLink();
		} elseif(isset($_GET['showid'])) {
			$model = User::model()->findByPk($_GET['showid']);
			if($model == null) {
				throw new CHttpException(404,'The requested page does not exist.');
			}			
			return $model->getLink();			
		}	else {
			return '/de/member';
		}
	}

	private function getNewsUrl($idparam='showid') {
		if(isset($_GET[$idparam])) {
			$model = News::model()->findByPk($_GET[$idparam]);
			if($model == null) {
				throw new CHttpException(404,'The requested page does not exist.');
			}			
			return $model->getLink();
		} else {
			return '/de/news';
		}
	}	
	
	private function checkFavoriten() {
		$attributes['user_id'] = Yii::app()->user->getId();
		$attributes['link'] = Yii::app()->request->hostInfo.Yii::app()->request->requestUri;
		
		$this->favoriten = Favoriten::model()->findByAttributes($attributes);
	}
	
	private function hauptWettbewerbByTld() {
		Yii::app()->params->ersterWettbewerb = 'L1';
	}
	
	private function getAnzahlPns() {
		return 0;
	}
	
	public function beforeRender($view) {
/*
		if($this->showSprungleiste) {
			if(isset($this->breadcrumb_verein_id)) {
				$this->getDropdownKaderByVerein();
			}
			
			if(isset($this->breadcrumb_wettbewerb_id)) {
				$this->getDropdownVereineByWettbewerb();
			}
		}
*/		
		return true;
	}
	
	public function renderDynamicContent($view, $data = null) {
		return $this->renderFile(dirname($this->getLayoutFile(null)).$view, $data, true);
	}
	
	protected function afterRender($view, &$output) {
		parent::afterRender($view,$output);
		//Yii::app()->facebook->addJsCallback($js); // use this if you are registering any $js code you want to run asyc
		Yii::app()->facebook->initJs($output); // this initializes the Facebook JS SDK on all pages
		Yii::app()->facebook->renderOGMetaTags(); // this renders the OG tags
		return true;
	}	
}