<?php
/*
 * При Разработки класса всегда вначале описывать метод тут!!!
 * 		проверяем что он не работает
 * 		создаем тест
 * 		реализовываем метод в классе
 * 		проверяем тест
 * ВАЖНО! после Разработки всегда проверять этот тест!!!
 * ВАЖНО! каждое УТВЕРЖДЕНИЕ необходимо конмментаровать для лучшего понимания и отладки!!!
 * ВАЖНО! необходимо описывать только утверждения свойственные для работы метода и не больше!!!!
 * ВАЖНО! если в методе теста происходит обновление данных в базе тогда нужно каждый раз использовать разные объекты
 *
 */
abstract class AbsDbTestCase extends CDbTestCase
{
	protected function getBasePathFixtures() {
		return yii::getPathOfAlias('MYOBJ.tests.fixtures.'.get_class($this));
	}

	protected function setConfig() {}

	protected function setUp()
	{
		Yii::app()->assetManager->basePath = $this->getBasePathFixtures();

		parent::setUp();

		$this->setConfig();
	}

	/**
	 * Метод устанавливает необходимую конфигурацию в тестовом режиме, так как в обычном режиме конфигурацию изменить нельзя
	 * @param array $newConfig
	 */
	static function editTestConfig(array $newConfig, $nameElem=null) {
		$class = new ReflectionClass(Yii::app()->appcms);
		$conf = $class->getProperty('config');
		$conf->setAccessible(true);
		if($nameElem) {
			$arrayConfig = Yii::app()->appcms->config;
			$arrayConfig[$nameElem] = $newConfig;
			$newConfig = $arrayConfig;
		}
		$conf->setValue(Yii::app()->appcms,$newConfig);
	}

	/**
	 * Вызвать приватный метод объекта
	 * @param array $newConfig
	 */
	static function accessibleMethod($obj, $nameMethod) {
		$method = new ReflectionMethod(get_class($obj), $nameMethod);
		$method->setAccessible(true);
		return $method->invoke($obj);
	}
}