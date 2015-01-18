<?php
namespace MYOBJ\controllers;
use \yii as yii;

/**
 * Контроллер для функциональных тестов
 * Class TestController
 */
class TestController extends \MYOBJ\controllers\admin\AbsSiteController {
	public function actionHeader_list() {
		$this->setPageTitle('test page - '.$this->getAction()->getId());

		$modelTestHeader = \uClasses::getclass('test_header')->objects();

		$this->varsRender['modelTestHeader'] = $modelTestHeader;
	}

	public function actionShow() {
		
	}

	/**
	 * @param bool $id редакрируемый объект, если пуст то создает новый
	 * 			ХОТЯ для каждой логической единыцы лучше делать отдельный экшен - actionHeader_new
	 */
	public function actionHeader_edit($id=false) {
		$this->setPageTitle('test page - '.$this->getAction()->id);

		if($id) {
			$objEdit = \uClasses::getclass('test_header')->objects()->findByPk($id);
		}
		else {
			$objEdit = \uClasses::getclass('test_header')->initobject();
		}

		$nameModel = get_class($objEdit);

		$validate_params = null;
		$validate_params_value = '';
		if(isset($_POST[$nameModel]) && isset($_POST[$nameModel]['validate_params'])) {
			$validate_params_value = $_POST[$nameModel]['validate_params'];
			$validate_params = \CJavaScript::jsonDecode($validate_params_value);
		}

		if(Yii::app()->request->isAjaxRequest) {
			echo \CActiveForm::validate($objEdit, $validate_params);
			yii::app()->end();
		}

		if(isset($_POST[$nameModel])) {
			$objEdit->attributes = $_POST[$nameModel];
			$isValidate = $objEdit->validate($validate_params);
			if($isValidate) {
				$objEdit->save(
					false, //не проверять снова данные т.е сделали это выше
					$validate_params //обновить только отдельные сталбцы а не все данные
				);
				if($id) {
					$this->refresh();
				}
				else {
					$this->redirect(yii::app()->createUrl('myobj/test/header_edit', array('id'=>$objEdit->id)));
				}
			}
		}


		$this->varsRender['objEdit'] = $objEdit;
		$this->varsRender['validate_params_value'] = $validate_params_value;
	}
}
