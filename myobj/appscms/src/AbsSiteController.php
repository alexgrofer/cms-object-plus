<?php
namespace MYOBJ\appscms\src;
use yii;
use uClasses;

Yii::app()->clientScript->registerMetaTag('text/html;charset=UTF-8', null, 'content-type');


abstract class AbsSiteController extends \Controller {
	public $isMobile;

	const NAME_PARAM_PAGE_TITLE = 'page_title';
	const NAME_PARAM_PAGE_DESCRIPTION = 'page_description';
	const NAME_PARAM_PAGE_KEYWORDS = 'page_keywords';

	public $pageDescription;
	public $pageKeywords;

	const NAME_TEMPLATE_ERROR_PAGE = 'error';
	const NAME_VIEW_ERROR_PAGE = 'error';
	public function actionError() {
		if($error=Yii::app()->errorHandler->error) {
			$this->layout=DIR_TEMPLATES_SITE.(static::NAME_TEMPLATE_ERROR_PAGE);
			$this->render(DIR_VIEWS_SITE.(static::NAME_VIEW_ERROR_PAGE), $error);
		}
	}

	protected static function check_mobie() {
		return false;
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

		//cookie all is mobile
		Yii::app()->session->setCookieParams(array('domain'=>'.'.$getTopMobailDomain()));
		Yii::app()->user->identityCookie = (array('domain'=>'.'.$getTopMobailDomain()));

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
			$cookie = new \CHttpCookie('not_mobile', 1);
			$cookie->expire = time() + 60 * 60 * 24 * 360; //year
			$cookie->domain = '.'.$getTopMobailDomain();
			$request->cookies['not_mobile'] = $cookie;
			if($modMobileDomain(false)) {
				unset($_GET['not_mobile']);
				$this->redirect($this->createUrl($request->pathInfo, $_GET));
			}
		}
		elseif(static::check_mobie() && !$request->cookies['not_mobile']) { //1 проверка что это телефон
			if($modMobileDomain(true)) {
				$this->redirect($this->createUrl($request->pathInfo, $_GET));
			}
		}
		$getTopMobailDomain();
		//

		//set language
		$lang = 'ru';

		if($request->getQuery('setLanguage')) {
			$lang = $request->getQuery('setLanguage');
			$cookie = new \CHttpCookie('language_space', $lang);
			$cookie->expire = time()+60*60*24*360; //year
			$cookie->domain = '.'.$getTopMobailDomain();
			$request->cookies['language_space'] = $cookie;
			unset($_GET['setLanguage']);
			$this->redirect($this->createUrl($request->pathInfo, $_GET));
		}
		elseif($request->cookies['language_space']) {
			$lang = $request->cookies['language_space']->value;
		}
		
		Yii::app()->language = $lang;
		//

		//set errorHandler
		Yii::app()->errorHandler->errorAction='/myobj/'.yii::app()->getController()->getId().'/error';
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

	protected function beforeAction($action) {
		//разлогирование юзера если он пришел из другой таблицы юзеров из админки, а не из сайта
		if(Yii::app()->user->isGuest==false && Yii::app()->user->getState('isAdmin')==true) {
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
