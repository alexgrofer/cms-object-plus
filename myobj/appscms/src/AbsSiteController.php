<?php
namespace MYOBJ\appscms\src;
use yii;
use uClasses;

Yii::app()->clientScript->registerMetaTag('text/html;charset=UTF-8', null, 'content-type');

/**
 * Наследовать класс если необходима работат ьчерез хендлы меню системы
 *
 * -при создании файла контроллера необходимо добавить controller и action
 *
 * -что бы не создавать файл контроллера можно использовать /myobj/show/obj/codename/index - где index это колонка codename
 *
 * Class AbsSiteController
 * @package MYOBJ\appscms\src
 */
abstract class AbsSiteController extends \Controller {
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
		if($this->thisObjNav) {
			if(!($templateObj = $this->thisObjNav->templateDefault)) {
				throw new \CException(Yii::t('cms','none object template'));
			}

			$this->layout=DIR_TEMPLATES_SITE.$templateObj->path;
		}
		else {
			throw new \CHttpException(404,'CMS ERROR page not is find');
		}

		return true;
	}

	private $_tempHandleViews=null;
	/**
	 * выводит в поток рендер представления для установленного рендера навигацмм
	 * @param $name название хендла, используется для поиска, для каждого шаблона должен быть уникален
	 * @param $idHandle необходим для сортировке в панели редиктирования шаблона, не должен быть уникален
	 * @return null|string Вернет null если нет прав или поток представления
	 */
	final public function renderHandle($name, $idHandle) {
		if($this->_tempHandleViews === null) {
			$this->_tempHandleViews = array();
			$template_id = $this->template_id ?: $this->thisObjNav->template_default_id;
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
		$this->renderPartial(DIR_VIEWS_HANDLER_SITE.$objView->path, $vars);
	}

	protected function beforeAction($action) {
		$idController = yii::app()->getController()->getId();
		$idAction = $action->id;
		if($idAction=='index') $idAction=null;
		if($idController=='show' && $idAction=='obj') {
			$objNav = \uClasses::getclass('navigation_sys')->objects()->findByAttributes(array('codename' => Yii::app()->request->getParam('codename')));
			//не нужно искать через поиск навигацию которая работает через стандартный контроллер
			if($objNav && $objNav->getAttribute('controller')) $objNav=null;
		}
		else {
			$objNav = \uClasses::getclass('navigation_sys')->objects()->findByAttributes(array('controller' => $idController, 'action' => $idAction));
		}
		$this->thisObjNav = $objNav;

		if($action instanceof \CCaptchaAction || ($this->thisObjNav==false || $this->thisObjNav->is_smart_tmp==false)) {
			return parent::beforeAction($action);
		}

		//is redirect after AJAX request
		if(isset(Yii::app()->session['urlRedirectAfterAJAX']) && $urlRedirect = Yii::app()->session['urlRedirectAfterAJAX']) {
			unset(Yii::app()->session['urlRedirectAfterAJAX']);
			$this->redirect($urlRedirect);
		}

		return $this->checkLayout();
	}
}
