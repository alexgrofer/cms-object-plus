<?php
namespace MYOBJ\controllers\admin;
use yii;
use uClasses;

abstract class AbsSiteController extends \Controller {
	protected $thisObjNav = null;
	protected $varsRender = array();

	private $_params=null;

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

	/* в представдениях можно передать переменные так:
	public function indexAction() {
		$this->varsRender['name'] = 'var name';
	}
	*/

	/**
	 * @param $navigate codename для поиска элемента навигации
	 * @param null $action не обязателен, у навигации может быть NULL, но он в ходит сосатв первичного ключа для навигации
	 * @throws \CException
	 * @throws \CHttpException
	 */
	final protected function renderNavigateContent($navigate, $action=null) {
		$objNav = \uClasses::getclass('navigation_sys')->objects()->findByAttributes(array('controller'=>$navigate, 'action'=>$action));

		if($objNav) {
			if(!($templateObj = $objNav->templateDefault)) {
				throw new \CException(Yii::t('cms','none object template'));
			}
			$this->thisObjNav = $objNav;

			$this->layout=DIR_TEMPLATES_SITE.$templateObj->path;
			$this->render(DIR_TEMPLATES_SITE.$templateObj->path.'_content');
		}
		else {
			throw new \CHttpException(404,'page not is find');
		}
	}

	private $_tempHandleViews=null;
	/**
	 * выводит в поток рендер представления для установленного рендера навигацмм
	 * @param $name название хендла, используется для поиска, для каждого шаблона должен быть уникален
	 * @param $idHandle необходим для сортировке в панели редиктирования шаблона, не должен быть уникален
	 * @param bool $isContent если true все переменные попадут в контент представления, если false переменные пападут в специальную переменную представления varsPrivateSetHandle
	 * @param bool $isPermit если false не будут использоваться права групп на представления
	 * @return null|string Вернет null если нет прав или поток представления
	 */
	final public function renderHandle($name, $idHandle, $isContent=false, $isPermit=true) {
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
		if($isContent) {
			$vars = $this->varsRender;
		} else {
			$vars['varsPrivateSetHandle'] = $this->varsRender;
		}

		$objView = $this->_tempHandleViews[$name];
		if($isPermit && !$this->isPermitRender($objView)) {
			return null;
		}
		return $this->renderPartial(DIR_VIEWS_SITE.$objView->path, $vars, true);
	}

	protected function afterAction($action) {
		return $this->renderNavigateContent(yii::app()->getController()->getId(), $action->getId());
	}

	final private function isPermitRender($objView) {
		$groupsView = $objView->getobjlinks('groups_sys')->findAll();
		$access = false;

		if($groupsView) {
			$group_handle_ids_top_groups = array();
			foreach($groupsView as $objgroup) {
				$group_handle_ids_top_groups[] = $objgroup->vp1;
			}

			if(\Yii::app()->user->isGuest && in_array('guestsys', $group_handle_ids_top_groups)) {
				$access = true;
			}
			elseif(!\Yii::app()->user->isGuest && in_array('authorizedsys', $group_handle_ids_top_groups)) {
				$access = true;
			}
			elseif(!\Yii::app()->user->isGuest) {
				$groupsuser = \Yii::app()->user->groupsident;
				foreach($group_handle_ids_top_groups as $idsystemgroup) {
					if(in_array($idsystemgroup,$groupsuser)) {
						$access = true;
						break;
					}
				}
			}
		}

		return $access;
	}
}
