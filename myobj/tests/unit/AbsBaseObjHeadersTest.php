<?php
/*
 * Класс для тестирования класса предка TestAbsBaseObjHeaders
 *
 * ВАЖНО! при Разработки класса TestAbsBaseObjHeaders всегда вначале описывать метод тут!!!
 * 		проверяем что он не работает
 * 		создаем тест
 * 		реализовываем метод в классе
 * 		проверяем тест
 * ВАЖНО! после Разработки      TestAbsBaseObjHeaders всегда проверять этот тест!!!
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

		//изменим конфиг приложения
		//добавляем тестовое табличное пространство
		$spacescl = Yii::app()->appcms->config['spacescl'];
		if(isset($spacescl['777'])===false) {
			$spacescl['777'] = array('namemodel'=>'TestAbsBaseObjHeaders','namelinksmodel'=>'linksObjectsAllTestAbsBase');
			self::editTestConfig($spacescl, 'spacescl');
		}
	}

	public $fixtures=array(
		'objectAbsBaseHeader'=>'TestAbsBaseObjHeaders', //объекты TestAbsBaseObjHeaders
		'objProperty'=>'objProperties', //объекты objProperties
	);

	public function OktestGetNameLinksModel() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');

		//название ссылки на модель в которой хранятся ссылки объектов (цепляются друг на друга с помощью дочерней таблицы)
		$this->assertEquals('linksObjectsAllTestAbsBase', $objHeader->getNameLinksModel());
	}

	public function OktestRelations() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');

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
	public function OktestBeforeFind() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
		unset($objHeader->dbCriteria->with['lines.property']);
		unset($objHeader->dbCriteria->with['uclass.properties']);
		//сделаем возможность цеплять к объекту другие объекты
		$objHeader->isitlines = true;
		//джойнить в запросе таблицу строй всегда
		$objHeader->force_join_props = true;
		$objHeader->beforeFind();
		$this->assertArrayHasKey('lines.property', $objHeader->dbCriteria->with);
		$this->assertArrayHasKey('uclass.properties', $objHeader->dbCriteria->with);

		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
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
	public function OktestGetUProperties() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_2');
		$this->assertEquals($objHeader->uProperties['codename1'], 'type upcharfield line1 header 2');

		//обновляем свойства из другой ссылки на этот объект
		$findObjHeader = $objHeader::model()->findByPk($objHeader->primaryKey);
		$findObjHeader->uProperties = ['codename2', 'type uptextfield line2 header 2new'];
		$findObjHeader->save();

		//старое значение
		$this->assertEquals($objHeader->uProperties['codename2'], 'type uptextfield line2 header 2');
		//принудительно обновляем из базы
		$objHeader->getUProperties(true);
		$this->assertEquals($objHeader->uProperties['codename2'], 'type uptextfield line2 header 2new');
	}

	/**
	 * @return TestAbsBaseObjHeaders
	 *
	 */
	public function OktestSetUProperties() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_3');

		//установим новые свойства
		$objHeader->uProperties = ['codename1', 'type upcharfield line3 header 3new'];
		$objHeader->uProperties = ['codename2', 'type uptextfield line4 header 3new'];

		$this->assertEquals($objHeader->{'codename1'.$objHeader::PRE_PROP}, 'type upcharfield line3 header 3new');
		$this->assertEquals($objHeader->{'codename2'.$objHeader::PRE_PROP}, 'type uptextfield line4 header 3new');

		//вернем объект для зависимости testSaveProperties
		return $objHeader;
	}

	/**
	 * @depends testSetUProperties
	 */
	public function OktestSaveProperties(TestAbsBaseObjHeaders $objHeader) {
		//нужно узнать какими были свойства до того как из изменили
		$this->assertEquals($objHeader->getOldProperties(), ['codename1'=>'type upcharfield line3 header 3', 'codename2'=>'type uptextfield line4 header 3']);

		$objHeader->saveProperties();
		//теперь свойства переписаны и старыми являются новые
		$this->assertEquals($objHeader->getOldProperties(), ['codename1'=>'type upcharfield line3 header 3new', 'codename2'=>'type uptextfield line4 header 3new']);

		//проверим что свойства сохранились
		$findObjHeader = $objHeader::model()->findByPk($objHeader->primaryKey);
		$this->assertEquals($findObjHeader->uProperties, ['codename1'=>'type upcharfield line3 header 3new', 'codename2'=>'type uptextfield line4 header 3new']);
	}

	public function etestEditlinks() {
		/* @var $objHeader1 TestAbsBaseObjHeaders */
		/*
		 * при созданении нового объекта для него создается общая ссылка
		 * это возможность ссылать на другие объекты
		 */
		$objHeader1 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_1');
		$objHeader1->save();
		$objHeader2 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_2');
		$objHeader2->save();
		$objHeader3 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_3');
		$objHeader3->save();

		//привяжем два объекта(3,4) класса "codename3" к TestAbsBaseObjHeaders_sample_id_1
		$objHeader1->editlinks('add','codename3',array(
				$objHeader2->primaryKey,
				$objHeader3->primaryKey,
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
				$objHeader2->primaryKey,
				$objHeader3->primaryKey,
			)
		);

		return $objHeader1;
	}

	/**
	 * @depends testEditlinks
	 */
	public function dtestGetobjlinks(TestAbsBaseObjHeaders $objHeader) {
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
	public function dtestAfterSave() {
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_5');
		$objHeader->flagAutoAddedLinks=false;
		$objHeader->save();
		$objectcurrentlink = $objHeader->toplink;
		//ссылка не должна быть созданна
		$this->assertNull($objectcurrentlink);

		$objHeader->flagAutoAddedLinks=true;
		$objHeader->save();
		$objectcurrentlink = $objHeader->toplink;
		//ссылка создалась
		$this->assertNotNull($objectcurrentlink);

		//ну нужно проверять saveProperties так как для него есть уже тесты
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

	/*
	 *
	 */
	function ftestBeforeDelete() {
		//создать объекты двух ранных классов
		$objHeader1 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_1');
		$objHeader1->save();

		//изменим конфиг приложения
		$config = [];
		$config['controlui']['none_del']['objects']['codename1'] = array('id'=>1);
		self::editTestConfig(array_merge_recursive(Yii::app()->appcms->config, $config));

		$objHeader1->delete();

		//объект небыл удален так как это запрещено в конфигурации
		$findObjHeader = $objHeader1::model()->findByPk($objHeader1->primaryKey);
		$this->assertNotNull($findObjHeader);

		$objHeader2 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_2');
		$objHeader2->save();

		$objHeader2->editlinks('add','codename1',array(
				$objHeader1->primaryKey,
			)
		);
		$topLink = $objHeader2->toplink;
		$objHeader2->delete();

		//должны быть удалены быть все строки этого объекта
		$nameLinesModel = $objHeader1->getActiveRelation('lines')->className;
		$this->assertNull($nameLinesModel::model()->findByPk($objHeader2->lines[0]->primaryKey));

		$nameLinksModel = $objHeader1->getActiveRelation('toplink')->className;

		//все ссылки стерты
		$this->assertEmpty($topLink->getRelated('links', true));
		//не должно остаться ведущей ссылки
		$nameLinksModel = $objHeader1->getActiveRelation('toplink')->className;
		$this->assertNull($nameLinksModel::model()->findByPk($objHeader2->toplink->primaryKey));
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