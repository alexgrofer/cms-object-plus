<?php

/**
 * Class FormModelTest
 * Тестируем форму редактирования добавления и другие возможности формы (ajax)
 */
class FormModelTest extends WebTestCase {
	protected function setUp()
	{
		Yii::app()->assetManager->basePath = yii::getPathOfAlias('MYOBJ.tests.fixtures.'.get_class($this));
		parent::setUp();

		//изменим конфиг приложения
		//добавляем тестовое табличное пространство
		$spacescl = Yii::app()->appcms->config['spacescl'];
		if(isset($spacescl['777'])===false) {
			$spacescl['777'] = array('namemodel'=>'TestAbsBaseObjHeaders', 'nameModelLinks'=>['base'=>'linksObjectsSystem']);
			self::editTestConfig($spacescl, 'spacescl');
		}
	}

	public $fixtures=array(
		'objectAbsBaseHeader'=>'TestAbsBaseObjHeaders', //объекты TestAbsBaseObjHeaders
		'objProperty'=>'objProperties', //объекты objProperties
	);

	//tests

	public function testIndex() {
		$this->open('');
		$this->assertTextPresent('Welcome');
	}
}
