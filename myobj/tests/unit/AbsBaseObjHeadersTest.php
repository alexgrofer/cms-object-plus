<?php
/*
 * Класс для тестирования класса предка AbsBaseObjHeaders
 *
 * ВАЖНО! при Разработки класса AbsBaseObjHeaders всегда вначале описывать метот тут!!!
 * 		проверяем что он не работает
 * 		создаем тест
 * 		реализовываем метод в классе
 * 		проверяем тест
 * ВАЖНО! после Разработки      AbsBaseObjHeaders всегда проверять этот тест!!!
 * ВАЖНО! каждое УТВЕРЖДЕНИЕ необходимо конмментаровать для лучшего понимания и отладки!!!
 * ВАЖНО! необходимо описывать только утверждения свойственные для работы метода и не больше!!!!
 *
 * приставка testPRPT, это тест приватного или защищенного метрода метода
 *
 *
 */
class AbsBaseObjHeadersTest extends CDbTestCase {

	/*
	 * необходимые фикстуры моделей:
	 * TestAbsBaseObjHeaders
	 *
	 * необходимые фикстуры таблиц:
	 *
	 */

	public $fixtures=array(
		'objectAbsBaseHeader'=>'TestAbsBaseObjHeaders', //объекты TestAbsBaseObjHeaders
		'objProperty'=>'objProperties', //объекты objProperties
	);

	public function GetNameLinksModel() {
		/* @var $objHeader myObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		//название ссылки на модель в которой хранятся ссылки объектов (цепляются друг на друга с помощью дочерней таблицы)
		$this->assertEquals('linksObjectsAllTestAbsBase', $objHeader->getNameLinksModel());
		$objHeader->save();
	}

	public function Relations() {
		/* @var $objHeader myObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');

		//по умолчанию должен быть данный набор связующих переменных
		$this->assertEquals(array_keys($objHeader->relations()), array('uclass', 'lines', 'lines_sort', 'lines_find'));
		$this->assertNotEmpty($objHeader->uclass);

		//если не нужно работать со строками то нужна ссылка только на uclass
		$objHeader->isitlines = false;
		$this->assertEquals(array_keys($objHeader->relations()), array('uclass'));
	}

	/*
	 *
	 */
	public function BeforeFind() {
		/* @var $objHeader myObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		unset($objHeader->dbCriteria->with['lines.property']);
		unset($objHeader->dbCriteria->with['uclass.properties']);
		//сделаем возможность цеплять к объекту другие объекты
		$objHeader->isitlines = true;
		//джойнить в запросе таблицу строй всегда
		$objHeader->force_join_props = true;
		$objHeader->beforeFind();
		$this->assertArrayHasKey('lines.property', $objHeader->dbCriteria->with);
		$this->assertArrayHasKey('uclass.properties', $objHeader->dbCriteria->with);

		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		unset($objHeader->dbCriteria->with['lines.property']);
		unset($objHeader->dbCriteria->with['uclass.properties']);
		//если строки в этом классе отключенны значит нельзя их использовать также
		$objHeader->isitlines = false;
		$objHeader->force_join_props = true;
		$objHeader->beforeFind();
		$this->assertArrayNotHasKey('lines.property', $objHeader->dbCriteria->with);
		$this->assertArrayNotHasKey('uclass.properties', $objHeader->dbCriteria->with);
	}

	/*
	 *
	 */
	public function testGetUProperties($force=false) {
		/* @var $objHeader myObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		$this->assertEquals($objHeader->uProperties['codename1'], 'type upcharfield1');

		//обновляем свойства из другой ссылки на этот объект
		$findObjHeader = $objHeader::model()->findByPk($objHeader->primaryKey);
		$findObjHeader->uProperties = ['codename2', 'type uptextfield2'];
		$findObjHeader->save();


		$this->assertEquals($objHeader->uProperties['codename2'], '');
		//принудительно обновляем из базы
		$objHeader->getUProperties(true);
		$this->assertEquals($objHeader->uProperties['codename2'], 'type uptextfield2');
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
	public function PRPT_Getobjectlink() {
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
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
	public function Editlinks() {
		/*
		 * ссылкм можно добавлять удалять чистить редактировать все это проверить
		 */
	}

	/*
	 *
	 */
	public function Getobjlinks() {
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
	public function AfterSave() {
		//создать новый объект можно с пом класса class::obj
		//ghjntcnbnm протестит ьсохранение ссылки с flagAutoAddedLinks и без

		//изменить свойства
		//после схранения они должны быть в базе
	}

	/*
	 *
	 */
	function BeforeDelete() {
		//
	}

	/*
	 *
	 */
	public function HasProperty() {
		//проверить существование свойств по одному
		//проверить не существование названия свойства accesfalse
	}

	/*
	 *
	 */
	public function PropertyNames() {
		//получить текущий спикок имен свойств
		//добавить в класс новое свойство
		//добавить в объект данные нового свойства
		//получить новый список свойств
	}

	/*
	 * -если методы могут зависить друг от друга лучше сделать зависимости что бы сразе все проверить борее правильно
	 * -нужно описать все все работу что ест ьв классе все методы досконально
	 */
	public function SetUProperties() {
		//проверить как работает сеттер

		//каждое утверждение необходимо конмментаровать для лучшего понимания и отладки!!!

		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');

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
	public function SetAttributes() {

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
	public function DeclareObj() {
		//
	}

	/*
	 *
	 */
	public function InitObj() {
		//
	}
}