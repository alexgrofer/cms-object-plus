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

	protected function setUp()
	{
		Yii::app()->assetManager->basePath = yii::getPathOfAlias('MYOBJ.tests.fixtures.'.get_class($this));
		parent::setUp();
	}

	public $fixtures=array(
		'objectAbsBaseHeader'=>'TestAbsBaseObjHeaders', //объекты TestAbsBaseObjHeaders
		'objProperty'=>'objProperties', //объекты objProperties
	);

	public function rtestGetNameLinksModel() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		//название ссылки на модель в которой хранятся ссылки объектов (цепляются друг на друга с помощью дочерней таблицы)
		$this->assertEquals('linksObjectsAllTestAbsBase', $objHeader->getNameLinksModel());
		$objHeader->save();
	}

	public function etestRelations() {
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
	public function rtestBeforeFind() {
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
	public function rtestGetUProperties($force=false) {
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
	public function rtestSetUProperties() {
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
	public function rtestSaveProperties(TestAbsBaseObjHeaders $objHeader) {
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
	public function rtestPRPT_Getobjectlink() {
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

	public function rtestEditlinks() {
		/* @var $objHeader1 TestAbsBaseObjHeaders */
		/*
		 * при созданении нового объекта для него создается общая ссылка
		 * это возможность ссылать на другие объекты
		 */
		$objHeader1 = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		$objHeader1->save();
		$objHeader2 = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_2');
		$objHeader2->save();
		$objHeader3 = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_3');
		$objHeader3->save();
		$objHeader4 = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_4');
		$objHeader4->save();

		//привяжем два объекта(3,4) класса "codename3" к AbsBaseObjHeaders_sample_id_1
		$objHeader1->editlinks('add','codename3',array(
				$objHeader3->primaryKey,
				$objHeader4->primaryKey,
			)
		);
		//найдем объект в базе данных
		$findObjHeader1 = $objHeader1::model()->findByPk($objHeader1->primaryKey);
		$objHeaderS = $findObjHeader1->getobjlinks('codename3')->findAll();
		//теперь у него две ссылки
		$this->assertCount(2, $objHeaderS);
		$this->assertEquals([3,4], array($objHeaderS[0]->primaryKey,$objHeaderS[1]->primaryKey));

		//удалить одну ссылку
		$objHeader1->editlinks('remove','codename3',array(
				$objHeader3->primaryKey,
			)
		);
		//найдем объект в базе данных
		$findObjHeader1 = $objHeader1::model()->findByPk($objHeader1->primaryKey);
		$objHeaderS = $findObjHeader1->getobjlinks('codename3')->findAll();
		//теперь у него только одна ссылка
		$this->assertCount(1, $objHeaderS);
		$this->assertEquals(4, $objHeaderS[0]->primaryKey);

		//очистить все ссылки для класс
		$objHeader1->editlinks('clear','codename3');
		$findObjHeader1 = $objHeader1::model()->findByPk($objHeader1->primaryKey);
		$objHeaderS = $findObjHeader1->getobjlinks('codename3')->findAll();
		$this->assertCount(0, $objHeaderS);

		$objHeader1->editlinks('add','codename3',array(
				$objHeader3->primaryKey,
				$objHeader4->primaryKey,
			)
		);

		return $objHeader1;
	}

	/**
	 * @depends testEditlinks
	 */
	public function atestGetobjlinks(TestAbsBaseObjHeaders $objHeader) {
		$objHeaderS = $objHeader->getobjlinks('codename3')->findAll();
		//у объекта должно быть 2 ссылки
		$this->assertCount(2, $objHeaderS);
		$this->assertEquals([3,4], array($objHeaderS[0]->primaryKey,$objHeaderS[1]->primaryKey));
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
	public function ftestAfterSave() {
		$class = new ReflectionClass('TestAbsBaseObjHeaders');
		$method_getobjectlink = $class->getMethod('_getobjectlink');
		$method_getobjectlink->setAccessible(true);

		$objHeader = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		$objHeader->flagAutoAddedLinks=false;
		$objHeader->save();
		$objectcurrentlink = $method_getobjectlink->invoke($objHeader);
		//ссылка не должна быть созданна
		$this->assertNull($objectcurrentlink);

		$objHeader->flagAutoAddedLinks=true;
		$objHeader->save();
		$objectcurrentlink = $method_getobjectlink->invoke($objHeader);
		//ссылка создалась
		$this->assertNotNull($objectcurrentlink);

		//ну нужно проверять saveProperties так как для него есть уже тесты
	}

	/**
	 * Метод устанавливает необходимую конфигурацию в тестовом режиме, так как в обычном режиме конфигурацию изменить нельзя
	 * @param array $newConfig
	 */
	static function editTestConfig(array $newConfig) {
		$class = new ReflectionClass(Yii::app()->appcms);
		$conf = $class->getProperty('config');
		$conf->setAccessible(true);
		$conf->setValue(Yii::app()->appcms,$newConfig);
	}

	/*
	 *
	 */
	function testBeforeDelete() {
		//создать объекты двух ранных классов
		$objHeader1 = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_1');
		$objHeader1->save();
		$primaryKeyobjHeader1 = $objHeader1->primaryKey;

		$objHeader2 = $this->objectAbsBaseHeader('AbsBaseObjHeaders_sample_id_2');
		$objHeader2->save();
		$primaryKeyobjHeader2 = $objHeader2->primaryKey;

		//изменим конфиг приложения
		$config = [];
		$config['controlui']['none_del']['objects']['codename1'] = array('id'=>1);
		self::editTestConfig(array_merge_recursive(Yii::app()->appcms->config, $config));

		$objHeader1Lines = $objHeader1->lines;
		$modelLines = $objHeader1->getModelLines();

		$objHeader1->delete();
		$objHeader2->delete();

		//объект небыл удален
		$findObjHeader = $objHeader1::model()->findByPk($primaryKeyobjHeader1);
		$this->assertNotNull($findObjHeader);

		//объект был удален
		$findObjHeader = $objHeader2::model()->findByPk($primaryKeyobjHeader2);
		$this->assertNull($findObjHeader);

		//должны быть удалены быть все строки этого объекта
		$findObjHeader = $modelLines->findByPk($objHeader1Lines[0]->primaryKey);
		$this->assertNull($findObjHeader);


		//не должно остаться ведущей ссылки
		//не должно остаться ссылко на этот объект
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