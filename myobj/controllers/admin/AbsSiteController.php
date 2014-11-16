<?php
namespace MYOBJ\controllers\admin;
use yii;
use uClasses;

abstract class AbsSiteController extends \Controller {
	public $thisObjNav = null;
	public $varsRender = array();

	/* в представдение можно передать переменные
	public function indexAction() {
		$this->varsRender['name'] = 'var name';
	}
	*/

	final protected function renderNavigateContent($navigate, $action) {
		$findParamNameNav = is_int($navigate)?'id':'vp2';
		$findParamNameAction = 'vp3';
		$objNav = \uClasses::getclass('navigation_sys')->objects()->findByAttributes(array($findParamNameNav=>$navigate, $findParamNameAction=>$action));

		if($objNav) {
			if(!($templateObj = $objNav->getobjlinks('templates_sys')->find())) {
				throw new \CException(Yii::t('cms','none object template'));
			}
			$this->thisObjNav = $objNav;

			$this->layout=DIR_TEMPLATES_SITE.$templateObj->vp1;
			$this->render(DIR_TEMPLATES_SITE.$templateObj->vp1.'_content');
		}
		else {
			throw new \CHttpException(404,'page not is find');
		}
	}

	private $_tempHandleViews=null;
	/**
	 * выводит в поток рендер представления для установленного рендера навигацмм
	 * @param $name название хендла, нужен только для юзера что бы определить его в настройке шаблона
	 * @param $idHandle необходим для сортировке для упрощения поиска в настройке шаблона, должен быть уникален для шаблона в навигации
	 * @param bool $isContent если true экшен сможет передать все переменные в представление, если false переменные пападут в специальную переменную представления varsPrivateSetHandle
	 * @param bool $isPermit если false не будут использоваться права групп на представления
	 * @return null|string Вернет null если нет прав или рендер представления
	 */
	final public function renderHandle($name, $idHandle, $isContent=false, $isPermit=true) {
		if($this->_tempHandleViews==null) {
			$this->_tempHandleViews = array();
			$handles = $this->thisObjNav->getobjlinks('handle_sys')->findAll(); //vp1 = view ID, sort = handle ID
			$idsView = array();
			foreach($handles as $handle) {
				$idsView[] = $handle->vp1;
			}
			$CRITERIA = new \CDbCriteria();
			$CRITERIA->addInCondition('t.id', $idsView);
			$headerModel = uClasses::getclass('views_sys')->objects();
			$listView = $headerModel->findAll($CRITERIA);
			$elemsView = [];
			foreach($listView as $view) {
				$elemsView[$view->primaryKey] = $view; //task сделать утилиту которая вернет массив ключами которого будут первичные ключи объектов
			}
			foreach($handles as $handle) {
				$this->_tempHandleViews[$handle->sort] = $elemsView[$handle->vp1];
			}
		}

		if(!array_key_exists($idHandle, $this->_tempHandleViews)) {
			return null;
		}

		$vars = [];
		if($isContent) {
			$vars = $this->varsRender;
		} else {
			$vars['varsPrivateSetHandle'] = $this->varsRender;
		}

		$objView = $this->_tempHandleViews[$idHandle];
		if($isPermit && !$this->isPermitRender($objView)) {
			return null;
		}
		return $this->renderPartial(DIR_VIEWS_SITE.$objView->vp1, $vars, true);//vp1 = path view
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
