<?php
namespace MYOBJ\appscms\src;
use yii;
use uClasses;




abstract class AbsSiteController extends \CController {
	public $isMobile;

	const NAME_PARAM_PAGE_TITLE = 'page_title';
	const NAME_PARAM_PAGE_DESCRIPTION = 'page_description';
	const NAME_PARAM_PAGE_KEYWORDS = 'page_keywords';

	public $pageDescription;
	public $pageKeywords;


	protected static function check_mobie() {
		return false;
	}

	protected function setCookie($name, $val, $expire=false, $domain=false) {
		$cookie = new \CHttpCookie($name, $val);
		$cookie->expire = $expire ?: time() + 60 * 60 * 24 * 360; //year
		$cookie->domain = $domain ?: COOKIE_DOMAIN;
		Yii::app()->request->cookies[$name] = $cookie;
	}
	protected function getCookie($name) {
		return (Yii::app()->request->cookies[$name])?Yii::app()->request->cookies[$name]->value:false;
	}

	public function init() {
		$request=Yii::app()->request;

		//set mobile version
		$getTopMobailDomain = function() {
			if($pos = strpos($_SERVER['HTTP_HOST'], 'm.')!==false and $pos==2) {
				$this->isMobile=1;
				return substr($_SERVER['HTTP_HOST'], $pos+1);
			}
			else {
				return $_SERVER['HTTP_HOST'];
			}
		};

		$modMobileDomain = function($setMobile) use($getTopMobailDomain) {
			if(strpos($_SERVER['HTTP_HOST'], 'm.')!==false) {
				if($setMobile) {
					return false;
				}
				else {
					$_SERVER['HTTP_HOST'] = $getTopMobailDomain();
				}
			}
			else {
				if($setMobile) {
					$_SERVER['HTTP_HOST'] = 'm.'.$_SERVER['HTTP_HOST'];
				}
				else {
					return false;
				}
			}
			return $_SERVER['HTTP_HOST'];
		};

		if($request->getQuery('not_mobile')) {
			$this->setCookie('not_mobile', 1);
			if($modMobileDomain(false)) {
				unset($_GET['not_mobile']);
				$this->redirect($this->createUrl($request->pathInfo, $_GET));
			}
		}
		elseif($request->getQuery('force_mobile') || (static::check_mobie() && !$this->getCookie('not_mobile'))) { //1 проверка что это телефон
			if($modMobileDomain(true)) {
				unset($_GET['force_mobile']);
				$this->redirect($this->createUrl($request->pathInfo, $_GET));
			}
		}
		$getTopMobailDomain();
		//

		//set language
		$lang = 'ru';

		if($request->getQuery('setLanguage')) {
			$lang = $request->getQuery('setLanguage');
			$this->setCookie('language_space', $lang);
			unset($_GET['setLanguage']);
			$this->redirect($this->createUrl($request->pathInfo, $_GET));
		}
		elseif($this->getCookie('language_space')) {
			$lang = $this->getCookie('language_space');
		}
		
		Yii::app()->language = $lang;
	}

	public $layout=false;

	protected $thisObjNav = null;
	protected $varsRender = array();

	private $_params=null;

	public function redirect($url,$terminate=true,$statusCode=302) {
		if(Yii::app()->request->isAjaxRequest) {
			Yii::app()->session['urlRedirectAfterAJAX'] = $url;
			return;
		}
		else {
			parent::redirect($url, $terminate, $statusCode);
		}
	}

	public function getParams() {
		if($this->_params===null && $this->thisObjNav) {
			$this->_params = array();
			foreach($this->thisObjNav->params as $objParam) {
				$this->_params[$objParam->name] = $objParam->content;
			}
		}
		return $this->_params;
	}

	/**
	 * @var null
	 * Если необходима особая логия шаблон всегда можно изменить в контроллере.
	 * По умолчанию берется шаблон по умолчанию в навигации
	 */
	public $template_id=null;

	/**
	 * @param $navigate codename для поиска элемента навигации
	 * @param null $action не обязателен, у навигации может быть NULL, но он в ходит сосатв первичного ключа для навигации
	 * @throws \CException
	 * @throws \CHttpException
	 */
	final protected function checkLayout() {
		if($this->thisObjNav || $this->thisObjNav->show==false) {
			$templateObj = ($this->isMobile) ? ($this->thisObjNav->templateMobileDefault ?: $this->thisObjNav->templateDefault) :$this->thisObjNav->templateDefault;
			if(!$templateObj) {
				throw new \CException(Yii::t('cms','none object template'));
			}

			$this->layout=DIR_TEMPLATES_SITE.$templateObj->path;
		}
		else {
			throw new \CHttpException(404,Yii::t('cms','Page not found'));
		}

		return true;
	}

	private $_tempHandleViews=null;
	/**
	 * выводит в поток рендер представления для установленного рендера навигацмм
	 * @param $name название хендла, используется для поиска, для каждого шаблона должен быть уникален
	 * @param $idHandle необходим для сортировке в панели редиктирования шаблона, не должен быть уникален
	 * @return null|string
	 */
	final public function renderHandle($name, $idHandle) {
		if($this->thisObjNav==null) {
			return null;
		}

		if($this->_tempHandleViews === null) {
			$this->_tempHandleViews = array();
			$template_id = $this->template_id ?: (($this->isMobile)?$this->thisObjNav->template_mobile_default_id:$this->thisObjNav->template_default_id);
			$handles = $this->thisObjNav->getobjlinks('handle_sys', 'handle')->findAllByAttributes(['template_id'=>$template_id]);

			foreach($handles as $handle) {
				$this->_tempHandleViews[$handle->codename] = $handle->view;
			}
		}

		if(!isset($this->_tempHandleViews[$name])) {
			return null;
		}

		$vars = [];

		$objView = $this->_tempHandleViews[$name];
		$this->renderPartial(DIR_VIEWS_HANDLES_SITE.$objView->path, $vars);
	}

	public $pathRoute;
	protected function beforeAction($action) {
		$this->pathRoute = yii::app()->getController()->getId().'/'.$action->id;

		//разлогирование юзера если он пришел из другой таблицы юзеров из админки, а не из сайта
		if(Yii::app()->user->isGuest==false && Yii::app()->user->getState(NAME_USER_STATE_KEY_IS_ADMIN)) {
			Yii::app()->user->logout();
			$this->redirect(Yii::app()->request->url);
		}

		$idController = yii::app()->getController()->getId();
		$idAction = $action->id;
		if($idAction=='objNav') {
			$objNav = \uClasses::getclass('navigation_sys')->objects()->findByAttributes(array('codename' => Yii::app()->request->getParam('codename')));
			if(!$objNav) {
				throw new \CHttpException(404,Yii::t('cms','Page not found'));
			}
		}
		else {
			$objNav = \uClasses::getclass('navigation_sys')->objects()->findByAttributes(array('controller' => $idController, 'action' => $idAction));
		}
		$this->thisObjNav = $objNav;

		if($action instanceof \CCaptchaAction || $this->thisObjNav==null) {
			return parent::beforeAction($action);
		}

		//is redirect after AJAX request
		if(isset(Yii::app()->session['urlRedirectAfterAJAX']) && $urlRedirect = Yii::app()->session['urlRedirectAfterAJAX']) {
			unset(Yii::app()->session['urlRedirectAfterAJAX']);
			$this->redirect($urlRedirect);
		}

		//word meta
		$this->pageTitle = $this->thisObjNav->name;
		if(isset($this->params[static::NAME_PARAM_PAGE_TITLE])) {
			$this->pageTitle = $this->params[static::NAME_PARAM_PAGE_TITLE];
		}
		if(isset($this->params[static::NAME_PARAM_PAGE_DESCRIPTION])) {
			$this->pageDescription = $this->params[static::NAME_PARAM_PAGE_DESCRIPTION];
		}
		if(isset($this->params[static::NAME_PARAM_PAGE_KEYWORDS])) {
			$this->pageKeywords = $this->params[static::NAME_PARAM_PAGE_KEYWORDS];
		}

		return $this->checkLayout();
	}
}
