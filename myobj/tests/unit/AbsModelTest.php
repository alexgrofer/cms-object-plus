<?php
/*
 * Класс для тестирования класса предка AbsBaseModel
 *
 * -у нас должен быть какой то готовый объект с которым мы должны будем работать
 * -другие классы как ссылки не должны находится в этом тесте этт тест предназначен только для тестирования методав класса AbsBaseHeaders
 */
class AbsBaseModelTest extends CDbTestCase {
	/*
	 * -если методы могут зависить друг от друга лучше сделать зависимости что бы сразе все проверить борее правильно
	 * -get_properties
	 * -
	 */
	public function getPropTest() {
		$this->assertFalse(true);
	}
}
