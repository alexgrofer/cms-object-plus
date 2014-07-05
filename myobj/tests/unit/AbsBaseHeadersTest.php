<?php
/*
 * Класс для тестирования класса предка AbsBaseHeaders
 *
 * ВАЖНО! при Разработки класса AbsBaseHeaders всегда вначале описывать метот тут!!!
 * ВАЖНО! после Разработки      AbsBaseHeaders всегда проверять этот тест!!!
 * ВАЖНО! каждое УТВЕРЖДЕНИЕ необходимо конмментаровать для лучшего понимания и отладки!!!
 *
 * -у нас должен быть какой то готовый объект с которым мы должны будем работать
 * -другие классы как ссылки не должны находится в этом тесте этт тест предназначен только для тестирования методав класса AbsBaseHeaders
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

	/*
	 * ******** end yii config fixtures
	 */

	/*
 * -если методы могут зависить друг от друга лучше сделать зависимости что бы сразе все проверить борее правильно
 * -get_properties
 * -нужно описать все все работу что ест ьв классе все методы досконально
 */

	/**
	 * Получить свойства
	 * @param bool $force возвращает без кеширование на уровне объекта
	 * @return array должен вернуть массив ключ значение свойство
	 */
	public function testGet_properties($force=false) {

		//каждое утверждение необходимо конмментаровать для лучшего понимания и отладки!!!
		$objHeader = $this->objectsHeaders('AbsBaseHeadersTest_sample_id_1');

		//print_r($objHeader);

	}

}