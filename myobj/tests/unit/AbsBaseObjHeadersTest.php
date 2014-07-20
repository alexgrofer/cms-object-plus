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

	protected function setUp()
	{
		Yii::app()->assetManager->basePath = yii::getPathOfAlias('MYOBJ.tests.fixtures.'.get_class($this));
		parent::setUp();
	}

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

	public function testGetNameLinksModel() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		//название ссылки на модель в которой хранятся ссылки объектов (цепляются друг на друга с помощью дочерней таблицы)
		$this->assertEquals('linksObjectsAllTestAbsBase', $objHeader->getNameLinksModel());
		$objHeader->save();
	}

	public function testRelations() {
		/* @var $objHeader TestAbsBaseObjHeaders */
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
	public function testBeforeFind() {
		/* @var $objHeader TestAbsBaseObjHeaders */
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
		/* @var $objHeader TestAbsBaseObjHeaders */
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

	/**
	 * @return TestAbsBaseObjHeaders
	 *
	 */
	public function testSetUProperties() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');

		$objHeader->uProperties = ['codename1', 'new type upcharfield1'];
		$objHeader->uProperties = ['codename2', 'new type uptextfield2'];

		//тут еще необходимые утверждения

		//вернем объект для зависимости testSaveProperties
		return $objHeader;
	}

	/**
	 * @depends testSetUProperties
	 */
	public function testSaveProperties(TestAbsBaseObjHeaders $objHeader) {
		//нужно узнать какими были свойства до того как из изменили
		$this->assertEquals($objHeader->getOldProperties(), ['codename1'=>'type upcharfield1', 'codename2'=>'type uptextfield2']);

		$objHeader->saveProperties();
		//теперь свойства переписаны и старыми являются новые
		$this->assertEquals($objHeader->getOldProperties(), ['codename1'=>'new type upcharfield1', 'codename2'=>'new type uptextfield2']);

		$findObjHeader = $objHeader::model()->findByPk($objHeader->primaryKey);
		$this->assertEquals($findObjHeader->uProperties, ['codename1'=>'new type upcharfield1', 'codename2'=>'new type uptextfield2']);
	}

	/**
	 * тестируем защищенный метод _getobjectlink
	 */
	public function testPRPT_Getobjectlink() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');

		$class = new ReflectionClass($objHeader);
		$method = $class->getMethod('_getobjectlink');
		$method->setAccessible(true);

		$objectlink = $method->invoke($objHeader);
		$this->assertInstanceOf('linksObjectsAllTestAbsBase', $objectlink);

		$this->assertEquals($objectlink->attributes['uclass_id'], '1');
		$this->assertEquals($objectlink->attributes['idobj'], '1');
	}
	public function Editlinks() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');

		/*
		 * ссылкм можно добавлять удалять чистить редактировать все это проверить
		 *
		 * узнать какие ссылки могут быть добавить, удалить, очистит все
		 * добавить 3 ссылки на 1 на 1 класс и 2 на другой
		 * удадить одну
		 * проверить
		 * очистить все
		 * проверить что нет ссылок
		 * добавить 3 ссылки на 1 на 1 класс и 2 на другой
		 * вернуть объект
		 */
		return $objHeader;
	}

	/**
	 * @depends testSetUProperties
	 */
	public function Getobjlinks(TestAbsBaseObjHeaders $objHeader) {
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