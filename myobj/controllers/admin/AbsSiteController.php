<?php
namespace MYOBJ\controllers\admin;
use yii;
use uClasses;

abstract class AbsSiteController extends \Controller {
	public $thisObjNav = null;
	public $varsRender = null;

	/* в представдение можно передать переменные
	public function indexAction() {
		$this->varsRender = ['name'=>'5'];
	}
	*/

	final private function renderNavigateContent($navigate, $action) {
		$findParamNameNav = is_int($navigate)?'id':'vp2';
		$findParamNameAction = 'vp3';
		$objNav = \uClasses::getclass('navigation_sys')->objects()->findByAttributes(array($findParamNameNav=>$navigate, $findParamNameAction=>$action));

		if($objNav) {
			if(!($templateObj = $objNav->getobjlinks('templates_sys')->find())) {
				throw new \CException(Yii::t('cms','none object template'));
			}
			$this->thisObjNav = $objNav;

			$this->layout='/cms/templates/'.$templateObj->vp1;
			$this->render('/cms/templates/'.$templateObj->vp1.'_content');
		}
		else {
			throw new \CHttpException(404,'page not is find');
		}
	}

	public function renderView($objView, $isPermit, $vars) {
		if($isPermit && !$this->isPermitRender($objView)) {
			return '';
		}
		return $this->renderPartial('/../views/site/views/'.$objView->vp1, $vars);//vp1 = path view
	}

	private $_tempHandleViews=null;
	public function renderHandle($name, $idHandle, $isPermit=true) {
		if($this->_tempHandleViews==null) {
			$this->_tempHandleViews = array();
			$handles = $this->thisObjNav->getobjlinks('handle_sys')->findAll(); //vp1 = view ID, sort = handle ID
			$idsView = array();
			foreach($handles as $handle) {
				$idsView[] = $handle->vp1;
			}
			$CRITERIA = new CDbCriteria();
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
			return '';
		}

		return $this->renderView($this->_tempHandleViews[$idHandle], $isPermit, $this->varsRender);
	}

	protected function afterAction($action) {
		return $this->renderNavigateContent(yii::app()->getController()->getId(), $action);
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
