<?php
namespace MYOBJ\controllers;
use \yii as yii;

/**
 * Контроллер для функциональных тестов
 * Class TestController
 */
class TestController extends \MYOBJ\controllers\admin\AbsSiteController {
	public function actionHeader_list() {
		$this->pageTitle = $this->getAction()->getId();

		$this->varsRender['testvar1'] = 'text var testvar1';
	}

	public function actionShow() {
		
	}

	public function actionEdit() {

	}
}