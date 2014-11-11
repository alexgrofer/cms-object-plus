<?php
namespace MYOBJ\controllers;
use \yii as yii;

/**
 * Контроллер работает как базывай для навигации для которой не обязателен контроллер /obj/78-id элемента или codeName - кодовое название
 * Class ObjController
 * @package MYOBJ\controllers
 */
class ObjController extends \MYOBJ\controllers\admin\AbsSiteController {
	protected function afterAction($action) {
		$navigate = $action->getId();
		return $this->renderNavigate($navigate, null);
	}
}
