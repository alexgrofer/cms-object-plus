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
 * ВАЖНО! если в методе теста происходит обновление данных в базе тогда нужно каждый раз использовать разные объекты
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

	public function OktestEditlinks() {
		/* @var $objHeader4 TestAbsBaseObjHeaders */
		/*
		 * при созданении нового объекта для него создается общая ссылка
		 * это возможность ссылать на другие объекты
		 */
		$objHeader4 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_4');
		$objHeader4->save();
		$objHeader5 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_5');
		$objHeader5->save();
		$objHeader6 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_6');
		$objHeader6->save();

		//привяжем два объекта(3,4) класса "codename2" к TestAbsBaseObjHeaders_sample_id_4
		$objHeader4->editlinks('add','codename2',array(
				$objHeader5->primaryKey,
				$objHeader6->primaryKey,
			)
		);
		//найдем объект в базе данных
		$findObjHeader4 = $objHeader4::model()->findByPk($objHeader4->primaryKey);
		$objHeaderS = $findObjHeader4->getobjlinks('codename2')->findAll();
		//теперь у него две ссылки
		$this->assertCount(2, $objHeaderS);
		$this->assertEquals([$objHeader5->primaryKey,$objHeader6->primaryKey], array($objHeaderS[0]->primaryKey,$objHeaderS[1]->primaryKey));

		//удалить одну ссылку
		$objHeader4->editlinks('remove','codename2',array(
				$objHeader5->primaryKey,
			)
		);
		//найдем объект в базе данных
		$findObjHeader4 = $objHeader4::model()->findByPk($objHeader4->primaryKey);
		$objHeaderS = $findObjHeader4->getobjlinks('codename2')->findAll();
		//теперь у него только одна ссылка
		$this->assertCount(1, $objHeaderS);
		$this->assertEquals($objHeader6->primaryKey, $objHeaderS[0]->primaryKey);

		//очистить все ссылки для класс
		$objHeader4->editlinks('clear','codename2');
		$findObjHeader4 = $objHeader4::model()->findByPk($objHeader4->primaryKey);
		$objHeaderS = $findObjHeader4->getobjlinks('codename2')->findAll();
		$this->assertCount(0, $objHeaderS);

		$objHeader4->editlinks('add','codename2',array(
				$objHeader5->primaryKey,
				$objHeader6->primaryKey,
			)
		);

		return array($objHeader4, $objHeader5->primaryKey, $objHeader6->primaryKey);
	}

	/**
	 * @depends testEditlinks
	 */
	public function OktestGetobjlinks(array $objHeader) {
		$objHeaderS = $objHeader[0]->getobjlinks('codename2')->findAll();
		//у объекта должно быть 2 ссылки
		$this->assertCount(2, $objHeaderS);
		$this->assertEquals([$objHeader[1],$objHeader[2]], array($objHeaderS[0]->primaryKey,$objHeaderS[1]->primaryKey));
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
	public function OktestAfterSave() {
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_7');
		$objHeader->flagAutoAddedLinks=false;
		$objHeader->afterSave();
		$objectcurrentlink = $objHeader->toplink;
		//ссылка не должна быть созданна
		$this->assertNull($objectcurrentlink);

		$objHeader->flagAutoAddedLinks=true;
		$objHeader->afterSave();
		$objectcurrentlink = $objHeader->toplink;
		//ссылка создалась
		$this->assertNotNull($objectcurrentlink);
	}

	/*
	 *
	 */
	function OktestBeforeDelete() {
		//создать объекты двух ранных классов
		$objHeader8 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_8');
		$objHeader8->save();
		$objHeader9 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_9');
		$objHeader9->save();

		//изменим конфиг приложения
		$config = [];
		$config['controlui']['none_del']['objects']['codename1'] = array('id'=>8);
		self::editTestConfig(array_merge_recursive(Yii::app()->appcms->config, $config));

		$objHeader8->delete();

		//объект небыл удален так как это запрещено в конфигурации
		$findObjHeader = $objHeader8::model()->findByPk($objHeader8->primaryKey);
		$this->assertNotNull($findObjHeader);

		$objHeader9->editlinks('add','codename1',array(
				$objHeader8->primaryKey,
			)
		);
		$topLink = $objHeader9->toplink;
		$objHeader9->delete();

		//должны быть удалены быть все строки этого объекта
		$nameLinesModel = $objHeader9->getActiveRelation('lines')->className;
		$this->assertNull($nameLinesModel::model()->findByPk($objHeader9->lines[0]->primaryKey));

		$nameLinksModel = $objHeader9->getActiveRelation('toplink')->className;

		//все ссылки стерты
		$this->assertEmpty($topLink->getRelated('links', true));
		//не должно остаться ведущей ссылки
		$nameLinksModel = $objHeader9->getActiveRelation('toplink')->className;
		$this->assertNull($nameLinksModel::model()->findByPk($objHeader9->toplink->primaryKey));
	}

	/*
	 *
	 */
	public function OktestHasProperty() {
		//проверить существование свойств по одному
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
		$this->assertTrue($objHeader->hasProperty('codename1'));
		$this->assertTrue($objHeader->hasProperty('codename2'));
		$this->assertFalse($objHeader->hasProperty('codename3'));
	}

	/*
	 *
	 */
	public function OktestPropertyNames() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
		$this->assertEquals($objHeader->propertyNames(), ['codename1', 'codename2']);
	}

	/*
	 *
	 */
	public function OktestSetAttributes() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
		$nameClass = get_class($objHeader);
		$objHeader->attributes = array(
			'codename1'.$nameClass::PRE_PROP=>'value1',
			'codename2'.$nameClass::PRE_PROP=>'value2',
		);
		$this->assertEquals($objHeader->uProperties, ['codename1'=>'value1', 'codename2'=>'value2']);
	}

	public function testSetCDbCriteriaUProp() {
		/* @var $modelObjects TestAbsBaseObjHeaders */
		$modelObjects = uClasses::getclass('codename3')->objects();

		$modelObjects->setCDbCriteriaUProp('codename1', 'condition', 'codename1=:param_prop_1');
		$modelObjects->getDbCriteria()->params[':param_prop_1'] = 'type upcharfield line1 header 10';

		$modelObjects->getDbCriteria()->addCondition($modelObjects->getTableAlias().'.param1=:param_param1');
		$modelObjects->getDbCriteria()->params[':param_param1'] = 'text param1 header 10';
		$saveCriteria = $modelObjects->getDbCriteria();

		$this->assertEquals(1, $modelObjects->count($saveCriteria));

		$this->assertEquals(1, count($modelObjects->findAll($saveCriteria)));

		//устанавливаем критерию по ПСЕВДОСВОЙСТВАМ
		//$modelObjects->setCDbCriteriaUProp('select','codename_news_section_example');
		//$modelObjects->setCDbCriteriaUProp('codename_news_section_example', 'condition', 'codename_news_section_example=:business');
		//$modelObjects->setCDbCriteriaUProp('params', array(':business'=>'jahdjakhsd'));
		//$modelObjects->setCDbCriteriaUProp('order','date_create DESC, first_name ASC');
		//проверяем по индексам что и как отсортировалось

		//продолжаем устанавливать критерию --- ОБЫЧНЫЕ СВОЙСТВА
		//$modelObjects->getDbCriteria()->addCondition("codename_news_section_example=:p4");
		//$modelObjects->getDbCriteria()->params[':p4'] = 'sdfsdf';
		//$modelObjects->order = 'date_create DESC, first_name ASC';
		//проверяем по индексам что и как отсортировалось
	}

	/*
	 *
	 */
	public function DeclareObj() {
		//была добавленна реляция toplink
		//заполнен список имен свойств
		//добавленны новые свойства класса для псевдосвойств
		//установленные правила валидации для свойств
		//установленны так же сложные правила исходя из ностройки свойства

	}

	/*
	 *
	 */
	public function initObj() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		//$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_10');
		//$objHeader->initObj();
		//заполним переменную oldProperties
	}
}