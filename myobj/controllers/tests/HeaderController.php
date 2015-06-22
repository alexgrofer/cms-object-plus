<?php
namespace MYOBJ\controllers;
use \yii as yii;

/**
 * пример url: /myobj/tests/header/list
 * нужно создать навигацию с controller = tests/header, action = list
 * Контроллер для функциональных тестов cms - заголовки и модели
 * Class TestController
 */
class HeaderController extends \MYOBJ\appscms\src\AbsSiteController {
	public function actionList() {
		$modelTestHeader = \uClasses::getclass('test_header')->objects();

		$this->render(DIR_VIEWS_SITE.'test/header_list', array('modelTestHeader'=>$modelTestHeader));
	}

	/**
	 * @param bool $id редакрируемый объект, если пуст то создает новый
	 * 			ХОТЯ для каждой логической единыцы лучше делать отдельный экшен - actionNew
	 */
	public function actionEdit($id=false) {
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
				if($validate_params_value!=='') {
					$validate_params = explode(',', $validate_params_value);
				}
			}
			//параметр отвечает за сохранение объета AR в онлайне
			if(isset($array_attributes_edit['save_event'])) {
				$is_save_event = $array_attributes_edit['save_event'];
			}

			$objEdit->attributes = $array_attributes_edit;
			$isValidate = $objEdit->validate($validate_params);
			//для объектов AR
			$function_save = function() use($objEdit, $validate_params) {
				return $objEdit->save(
					false, //не проверять снова данные т.е сделали это выше
					$validate_params //обновить только отдельные сталбцы а не все данные
				);
			};

			//ajax
			if(Yii::app()->request->isAjaxRequest) {
				//проверка атрибуте в онлайне
				$strJson = \CActiveForm::validate($objEdit, $validate_params);

				//сохранение объектов AR в онлайне только если если это существующий объект и нет ошибок
				if($objEdit->isNewRecord==false && $is_save_event && !\CJSON::decode($strJson)) {
					$function_save();
				}
				//вернем данные об ошибках
				yii::app()->end($strJson);
			}

			//base
			if($isValidate) {
				//для объектов AR
				$function_save();
				if($id) {
					$this->refresh();
				}
				else {
					$this->redirect($this->createUrl('edit', array('id'=>$objEdit->id)));
				}
			}
		}

		$this->render(DIR_VIEWS_SITE.'test/header_edit', array(
			'objEdit'=>$objEdit,
			'validate_params_value'=>$validate_params_value,
		));
	}
}
