<?php
/*
 * Класс для тестирования класса предка AbsBaseHeaders
 *
 * ВАЖНО! при Разработки класса AbsBaseHeaders всегда вначале описывать метот тут!!!
 * ВАЖНО! после Разработки      AbsBaseHeaders всегда проверять этот тест!!!
 * ВАЖНО! каждое УТВЕРЖДЕНИЕ необходимо конмментаровать для лучшего понимания и отладки!!!
 * ВАЖНО! необходимо описывать только утверждения свойственные для работы метода и не больше!!!!
 *
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
		//'objectAbsBaseHeader'=>'TestAbsBaseHeaders', //строки
		'objectAbsBaseHeader'=>'TestAbsBaseHeaders', //объекты TestAbsBaseHeaders
	);


	//end yii config fixtures

	public function testGetNameLinksModel() {
		/* @var $objHeader myObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseHeaders_sample_id_1');

		$this->assertEquals('linksObjectsAllMy', $objHeader->getNameLinksModel());
	}

	public function testRelations() {
		/* @var $objHeader myObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseHeaders_sample_id_1');

		//по умолчанию есть свойства
		$this->assertEquals(array_keys($objHeader->relations()), array('uclass', 'lines', 'lines_sort', 'lines_find'));
		$this->assertNotEmpty($objHeader->uclass);

		//если не нужно работать со строками то только нужена ссылка на класс ->uclass
		$objHeader->isitlines = false;
		$this->assertEquals(array_keys($objHeader->relations()), array('uclass'));
	}

	/*
	 *
	 */
	public function testBeforeFind() {
		/* @var $objHeader myObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseHeaders_sample_id_1');

		$objHeader->isitlines = true;

		$objHeader->force_join_props = true;
		$objHeader->beforeFind();
		$this->assertArrayHasKey('lines.property', $objHeader->dbCriteria->with);
		$this->assertArrayHasKey('uclass.properties', $objHeader->dbCriteria->with);

		$objHeader->force_join_props = false;
		$objHeader->beforeFind();
		$this->assertArrayNotHasKey('lines.property', $objHeader->dbCriteria->with);
		$this->assertArrayNotHasKey('uclass.properties', $objHeader->dbCriteria->with);
	}

	/*
	 *
	 */
	public function testGetUProperties($force=false) {
		/* @var $objHeader myObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseHeaders_sample_id_1');

		//необходимо создать два свойства они должны быть привязанны к классу и заполнить их
		//получить эти всойства
		//изменить эти свойства но через другую ссылку
		//получить по старой ссылке - не должны быть новые свойства, если видим новые нет смысла в $force
		//$force - true - теперь должны увидеть
		//
		//
		print_r($objHeader->uProperties);
	}
	public function saveProperties() {
		/*
		 *изменить свойства
		 * посмотреть GetUProperties возвращает новый
		 * old prop возвращает новые
		 * GetUProperties force - тоже самое
		 */
	}

	/**
	 * тестируем защищенный метод _getobjectlink
	 */
	public function testPRIVATE_Getobjectlink() {
		$objHeader = $this->objectAbsBaseHeader('AbsBaseHeaders_sample_id_1');
		$class = new ReflectionClass($objHeader);
		$method = $class->getMethod('_getobjectlink');
		$method->setAccessible(true);

		$objectLinks = $method->invoke($objHeader);

		/*
		 * возвращает объект класса baselinks
		 * возвращает правильную ссылку получаем объект и смотрим чему равен idobj, uclass_id
		 */

		//$objectLinks = $objHeader->_getobjectlink();
	}
	public function testEditlinks() {
		/*
		 * ссылкм можно добавлять удалять чистить редактировать все это проверить
		 */
	}

	/*
	 *
	 */
	public function testGetobjlinks() {
		/*
		 * изночатьно  у класса добавить 2 ссылки на 1 класс и 2 на другой
		 * проверить по каждому классу колличестро и id возвр ссылок
		 */
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
		//создать новый объект можно с пом класса class::obj
		//ghjntcnbnm протестит ьсохранение ссылки с flagAutoAddedLinks и без

		//изменить свойства
		//после схранения они должны быть в базе
	}

	/*
	 *
	 */
	function testBeforeDelete() {
		//
	}

	/*
	 *
	 */
	public function testHasProperty() {
		//проверить существование свойств по одному
		//проверить не существование названия свойства accesfalse
	}

	/*
	 *
	 */
	public function testPropertyNames() {
		//получить текущий спикок имен свойств
		//добавить в класс новое свойство
		//добавить в объект данные нового свойства
		//получить новый список свойств
	}

	/*
	 * -если методы могут зависить друг от друга лучше сделать зависимости что бы сразе все проверить борее правильно
	 * -нужно описать все все работу что ест ьв классе все методы досконально
	 */
	public function testSetUProperties() {
		//проверить как работает сеттер

		//каждое утверждение необходимо конмментаровать для лучшего понимания и отладки!!!

		$objHeader = $this->objectAbsBaseHeader('AbsBaseHeaders_sample_id_1');

		$propertiesArray = $objHeader->uProperties;
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
	public function testSetAttributes() {

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
	public function testInitObj() {
		//
	}
}