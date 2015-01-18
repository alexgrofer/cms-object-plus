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

		$validate_params_value = '';
		$is_save_event = false; //сохранять объект при событиях type, change

		if(isset($_POST[$nameModel])) {
			$array_attributes_edit = $_POST[$nameModel];
			$validate_params = null; //параметры которые нужно валидировать

			if(isset($array_attributes_edit['validate_params'])) {
				$validate_params_value = $array_attributes_edit['validate_params'];
				$validate_params = \CJavaScript::jsonDecode($validate_params_value);
			}

			if(isset($array_attributes_edit['save_event'])) {
				$is_save_event = $array_attributes_edit['save_event'];
			}

			$objEdit->attributes = $array_attributes_edit;
			$isValidate = $objEdit->validate($validate_params);
			$function_save = function() use($objEdit, $validate_params) {
				return $objEdit->save(
					false, //не проверять снова данные т.е сделали это выше
					$validate_params //обновить только отдельные сталбцы а не все данные
				);
			};
			//ajax
			if(Yii::app()->request->isAjaxRequest) {
				$strJson = \CActiveForm::validate($objEdit, $validate_params);
				if($is_save_event) {
					if($objEdit->validate()==false) { //проверить весь объект так как идет сохранение а не проверка отдельного поля
						$strJson = \CJSON::encode($objEdit->getErrors());
					}
					else {
						$function_save();
					}
				}
				yii::app()->end($strJson);
			}

			//base
			if($isValidate) {
				$function_save();
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
		$this->varsRender['is_save_event'] = $is_save_event;
	}
}
