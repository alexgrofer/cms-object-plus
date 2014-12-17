<?php
namespace MYOBJ\controllers;
use \yii as yii;
use \CActiveRecord as CActiveRecord;

/**
 * Начинаю делать новый контроллер для админки
 * Class NewAdminController
 * @package MYOBJ\controllers
 */
class NewAdminController extends \Controller {
	/**
	 * Просмотр объекта модели
	 * @param $nameModel - название модели
	 * @param $idObject - id объекта
	 */
	public function actionShowObjectModel($nameModel,$idObject) {
		//
	}

	/**
	 * Просмотр объекта класса
	 * @param $nameClass
	 * @param $idObject
	 */
	public function actionShowObjectClass($nameClass,$idObject) {
		//
	}

	/**
	 * Редактирование объекта модели
	 * @param $nameModel - название модели
	 * @param $idObject - id объекта
	 */
	public function actionEditObjectModel($nameModel,$idObject) {
		//
	}

	/**
	 * Редактирование объекта класса
	 * @param $nameClass
	 * @param $idObject
	 */
	public function actionEditObjectClass($nameClass,$idObject) {
		//
	}

	/**
	 * Список моделей
	 */
	public function actionListModels() {
		//
	}

	/**
	 * Список классов
	 */
	public function actionListClasses() {
		//
	}

	/**
	 * Редактирование реляций моделей
	 * @param $nameEditModel название модели
	 */
	public function actionEditRelationModel($nameEditModel) {
		//
	}

	/**
	 * Редактирование реляций класса
	 * @param $nameEditClass название codename класса
	 */
	public function actionEditRelationClass($nameEditClass) {
		//
	}

	public function actions()
	{
		return array(
			'editLinkClass'=>'MYOBJ.controllers.newAdmin.EditLinkClassAction',
		);
	}
}