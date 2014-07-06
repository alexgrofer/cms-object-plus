<?php
/*
 * Класс для тестирования класса предка AbsBaseHeaders
 *
 * ВАЖНО! при Разработки класса AbsBaseHeaders всегда вначале описывать метот тут!!!
 * ВАЖНО! после Разработки      AbsBaseHeaders всегда проверять этот тест!!!
 * ВАЖНО! каждое УТВЕРЖДЕНИЕ необходимо конмментаровать для лучшего понимания и отладки!!!
 *
 * !!!каждое утверждение необходимо конмментаровать для лучшего понимания и последующей отладки!!!
 *
 */
class AbsBaseHeadersTest extends CDbTestCase {

	/*
	 * необходимые фикстуры моделей:
	 * myObjHeaders
	 *
	 * необходимые фикстуры таблиц:
	 *
	 */

	public $fixtures=array(
		'objectsHeaders'=>'myObjHeaders',
	);


	//end yii config fixtures

	public function getNameLinksModel() {
		//PROBLEM
	}

	public function testRelations()
	{
		//
	}

	/*
	 *
	 */
	public function testBeforeFind() {
		//
	}

	/*
	 *
	 */
	public function testGet_properties($force=false) {
		//
	}
	private function _saveProperties() {
		//PROBLEM
	}

	private function _getobjectlink() {
		//PROBLEM
	}
	public function testEditlinks() {
		//
	}

	/*
	 *
	 */
	public function testGetobjlinks() {
		//
	}

	/**
	 * естирование метода afterSave
	 *
	 * Сохранение дополнительных данных после первичного сохранения объекта
	 * зависимость от методов:
	 * автоматическое добавление ссылки если новый
	 * добавление ссылки
	 * добавление нового свойства
	 * проверка на остаток старых свойств если понадобятся
	 */
	public function testAfterSave() {
		//
	}

	/*
	 *
	 */
	function testBeforeDelete() {
		//
	}

	public function testBeforeSave() {
		//
	}

	/*
	 *
	 */
	public function testHasProperty() {
		//
	}

	/*
	 *
	 */
	public function testPropertyNames() {
		//
	}

	/*
	 *
	 */
	public function testGetClassProperties() {
		//
	}

	/*
	 * -если методы могут зависить друг от друга лучше сделать зависимости что бы сразе все проверить борее правильно
	 * -get_properties
	 * -нужно описать все все работу что ест ьв классе все методы досконально
	 */
	public function testSet_properties() {
		//каждое утверждение необходимо конмментаровать для лучшего понимания и отладки!!!

		$objHeader = $this->objectsHeaders('AbsBaseHeaders_sample_id_1');

		$propertiesArray = $objHeader->get_properties();
		//должен возвращать массив
		$this->assertTrue(is_array($propertiesArray));
		//должно быть 3 свойства
		$this->assertEquals(count($propertiesArray),3);

		//добавить свойства найти метод добавить новые свойства
		//проверить force
	}

	/*
	 *
	 */
	public function testSetAttributes($values) {

	}

	/*
	 *
	 */
	protected function dinamicModel() {
		//
	}

	/*
	 *
	 */
	public function testDeclareObj() {
		//
	}

	/*
	 *
	 */
	protected function testInitObj() {
		//
	}
}