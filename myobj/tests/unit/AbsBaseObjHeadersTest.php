<?php
class AbsBaseObjHeadersTest extends AbsDbTestCase {

	public $fixtures=array(
		'objectAbsBaseHeader'=>'TestAbsBaseObjHeaders', //объекты TestAbsBaseObjHeaders
		'objProperty'=>'objProperties', //объекты objProperties
	);

	protected function getBasePathFixtures() {
		return yii::getPathOfAlias('MYOBJ.tests.fixtures.'.get_class($this));
	}

	protected function setConfig() {
		//изменим конфиг приложения
		//добавляем тестовое табличное пространство
		$spacescl = Yii::app()->appcms->config['spacescl'];
		if(isset($spacescl['777'])===false) {
			$spacescl['777'] = array('namemodel'=>'TestAbsBaseObjHeaders', 'nameModelLinks'=>['base'=>'linksObjectsSystem']);
			self::editTestConfig($spacescl, 'spacescl');
		}
	}

	public function testRelations() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');

		//по умолчанию должен быть данный набор связующих переменных
		$this->assertEquals(array_keys($objHeader->relations()), array('uclass', 'lines'));
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
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
		unset($objHeader->dbCriteria->with['lines.property']);
		unset($objHeader->dbCriteria->with['uclass.properties']);
		//сделаем возможность цеплять к объекту другие объекты
		$objHeader->isitlines = true;
		//джойнить в запросе таблицу строй всегда
		$objHeader->force_join_props = true;

		self::accessibleMethod($objHeader, 'beforeFind');

		$this->assertArrayHasKey('lines.property', $objHeader->dbCriteria->with);
		$this->assertArrayHasKey('uclass.properties', $objHeader->dbCriteria->with);

		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
		unset($objHeader->dbCriteria->with['lines.property']);
		unset($objHeader->dbCriteria->with['uclass.properties']);
		//если строки в этом классе отключенны значит нельзя их использовать также
		$objHeader->isitlines = false;
		$objHeader->force_join_props = true;

		self::accessibleMethod($objHeader, 'beforeFind');

		$this->assertArrayNotHasKey('lines.property', $objHeader->dbCriteria->with);
		$this->assertArrayNotHasKey('uclass.properties', $objHeader->dbCriteria->with);
	}

	/*
	 *
	 */
	public function testGetUProperties() {
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
	public function testSetUProperties() {
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
	public function testSaveProperties(TestAbsBaseObjHeaders $objHeader) {
		//нужно узнать какими были свойства до того как из изменили
		$this->assertEquals($objHeader->getOldProperties(), ['codename1'=>'type upcharfield line3 header 3', 'codename2'=>'type uptextfield line4 header 3']);

		$objHeader->saveProperties();
		//теперь свойства переписаны и старыми являются новые
		$this->assertEquals($objHeader->getOldProperties(), ['codename1'=>'type upcharfield line3 header 3new', 'codename2'=>'type uptextfield line4 header 3new']);

		//проверим что свойства сохранились
		$findObjHeader = $objHeader::model()->findByPk($objHeader->primaryKey);
		$this->assertEquals($findObjHeader->uProperties, ['codename1'=>'type upcharfield line3 header 3new', 'codename2'=>'type uptextfield line4 header 3new']);
	}

	public function testEditlinks() {
		/* @var $objHeader4 TestAbsBaseObjHeaders */
		$objHeader4 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_4');
		$objHeader5 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_5');
		$objHeader6 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_6');

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
	public function testGetobjlinks(array $objHeader) {
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
	public function testAfterSave() {
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_7');

		//task
	}

	/*
	 *
	 */
	function testBeforeDelete() {
		//создать объекты двух ранных классов
		$objHeader8 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_8');
		$objHeader8->save();
		$objHeader9 = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_id_9');
		$objHeader9->save();

		//изменим конфиг приложения (task перенести в тест AbsModelTest)
		$config = [];
		$config['none_del_id']['TestAbsBaseObjHeaders'] = array($objHeader8->primaryKey);
		self::editTestConfig(array_merge_recursive(Yii::app()->appcms->config, $config));

		$objHeader8->delete();

		//объект небыл удален так как это запрещено в конфигурации
		$findObjHeader = $objHeader8::model()->findByPk($objHeader8->primaryKey);
		$this->assertNotNull($findObjHeader);

		$objHeader9->editlinks('add','codename1',array(
				$objHeader8->primaryKey,
			)
		);

		$objHeader8->editlinks('add','codename2',array(
				$objHeader9->primaryKey,
			)
		);

		//как то проверить что в таблице ссылок больше нет ссылок и обратных ссылок у объекта $objHeader8
	}

	/*
	 *
	 */
	public function testHasProperty() {
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
	public function testPropertyNames() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
		$this->assertEquals($objHeader->propertyNames(), ['codename1', 'codename2']);
	}

	/*
	 *
	 */
	public function testSetAttributes() {
		/* @var $objHeader TestAbsBaseObjHeaders */
		$objHeader = $this->objectAbsBaseHeader('TestAbsBaseObjHeaders_sample_noSave');
		$nameClass = get_class($objHeader);
		$objHeader->attributes = array(
			'codename1'.$nameClass::PRE_PROP=>'value1',
			'codename2'.$nameClass::PRE_PROP=>'value2',
		);
		$this->assertEquals($objHeader->uProperties, ['codename1'=>'value1', 'codename2'=>'value2']);
	}

	public function testGetPropCriteria() {
		/* @var $modelObjects TestAbsBaseObjHeaders */
		$modelObjects = uClasses::getclass('codename3')->objects();
		//необходимо склонировать текущую критерию
		$fix_criteria = clone $modelObjects->getDbCriteria();
		//если несколько свойства необходимо каждое поставить в скобки
		$criteria1 = $modelObjects->getPropCriteria('condition',array('codename1', 'codename2'), '(codename1=:h_10_prop_1)
		 AND (codename2=:h_10_prop_2)');
		$criteria1->params[':h_10_prop_1'] = 'h type upcharfield line1 header 10';
		$criteria1->params[':h_10_prop_2'] = 'g type uptextfield line2 header 10';

		$new_criteria = new CDbCriteria;
		$new_criteria->mergeWith($fix_criteria);
		$new_criteria->mergeWith($criteria1);//($fix_condition) AND ($criteria1)
		$this->assertEquals(1, $modelObjects->count($new_criteria));
		//
		$criteria2 = $modelObjects->getPropCriteria('condition',array('codename1', 'codename2'), '(codename1=:h_12_prop_1)
		 AND (codename2=:h_12_prop_2)');
		$criteria2->params[':h_12_prop_1'] = 'e type upcharfield line1 header 12';
		$criteria2->params[':h_12_prop_2'] = 'd type uptextfield line2 header 12';

		$criteria3 = new CDbCriteria;
		$criteria3->addCondition($modelObjects->getTableAlias().'.param2=:param_param2');
		$criteria3->params[':param_param2'] = 'text param2 header 13';

		$criteria2->mergeWith($criteria1, 'OR'); //($criteria1 OR $criteria2)
		$criteria3->mergeWith($criteria2, 'OR'); //($criteria1 OR ($criteria2 OR $criteria3))

		$new_criteria = new CDbCriteria;
		$new_criteria->mergeWith($fix_criteria);
		$new_criteria->mergeWith($criteria3); //($fix_condition) AND (($criteria1 OR ($criteria2 OR $criteria3)))
		$this->assertEquals(3, $modelObjects->count($new_criteria));

		$fix_criteria_find = clone $new_criteria;
		//установить сортировку по свойству codename1 desc
		$order_criteria_codename1_desc = $modelObjects->getPropCriteria('order','codename1','DESC');

		$new_criteria = new CDbCriteria;
		$new_criteria->mergeWith($fix_criteria_find);//($fix_condition) AND (($criteria1 OR ($criteria2 OR $criteria3)))
		$new_criteria->mergeWith($order_criteria_codename1_desc);//($fix_condition) AND (($criteria1 OR ($criteria2 OR $criteria3))) order by 'codename1_desc'
		$objects = $modelObjects->findAll($new_criteria);
		$this->assertEquals(3, count($objects));
		$this->assertEquals($objects[0]->uProperties['codename1'], 'h type upcharfield line1 header 10');
		$this->assertEquals($objects[1]->uProperties['codename1'], 'e type upcharfield line1 header 12');
		$this->assertEquals($objects[2]->uProperties['codename1'], null); //task пока null не получилось опусить вниз при сортировке

		$this->assertEquals($objects[0]->uProperties['codename2'], 'g type uptextfield line2 header 10');
		$this->assertEquals($objects[1]->uProperties['codename2'], 'd type uptextfield line2 header 12');
		$this->assertEquals($objects[2]->uProperties['codename2'], 'b type uptextfield line2 header 13');

		$order_criteria_codename1_asc = $modelObjects->getPropCriteria('order','codename1','ASC');

		$new_criteria = new CDbCriteria;
		$new_criteria->mergeWith($fix_criteria_find);//($fix_condition) AND (($criteria1 OR ($criteria2 OR $criteria3)))
		$new_criteria->mergeWith($order_criteria_codename1_asc);//($fix_condition) AND (($criteria1 OR ($criteria2 OR $criteria3))) order by 'codename1_asc'
		$objects = $modelObjects->findAll($new_criteria);
		$this->assertEquals(3, count($objects));
		$this->assertEquals($objects[0]->uProperties['codename1'], 'e type upcharfield line1 header 12');
		$this->assertEquals($objects[1]->uProperties['codename1'], 'h type upcharfield line1 header 10');
		$this->assertEquals($objects[2]->uProperties['codename1'], null);

		$this->assertEquals($objects[0]->uProperties['codename2'], 'd type uptextfield line2 header 12');
		$this->assertEquals($objects[1]->uProperties['codename2'], 'g type uptextfield line2 header 10');
		$this->assertEquals($objects[2]->uProperties['codename2'], 'b type uptextfield line2 header 13');

		$fix_criteria_find_order  = clone $new_criteria;

		$new_criteria = new CDbCriteria;;
		$new_criteria->mergeWith($fix_criteria_find_order);
		$new_criteria->limit = 1;
		$new_criteria->offset = 0;
		$objects = $modelObjects->findAll($new_criteria);
		$this->assertEquals(1, count($objects));

		$this->assertEquals($objects[0]->uProperties['codename1'], 'e type upcharfield line1 header 12');
		$this->assertEquals($objects[0]->uProperties['codename2'], 'd type uptextfield line2 header 12');

		$new_criteria = new CDbCriteria;;
		$new_criteria->mergeWith($fix_criteria_find_order);
		$new_criteria->limit = 1;
		$new_criteria->offset = 1;
		$objects = $modelObjects->findAll($new_criteria);
		$this->assertEquals(1, count($objects));

		$this->assertEquals($objects[0]->uProperties['codename1'], 'h type upcharfield line1 header 10');
		$this->assertEquals($objects[0]->uProperties['codename2'], 'g type uptextfield line2 header 10');

		$new_criteria = new CDbCriteria;;
		$new_criteria->mergeWith($fix_criteria_find_order);
		$new_criteria->limit = 1;
		$new_criteria->offset = 2;
		$objects = $modelObjects->findAll($new_criteria);
		$this->assertEquals(1, count($objects));

		$this->assertEquals($objects[0]->uProperties['codename1'], null);
		$this->assertEquals($objects[0]->uProperties['codename2'], 'b type uptextfield line2 header 13');

		/*************************************/

		/*
		 * Просто обычный поиск когда условие "И"
		 * Если одно свойство в getPropCriteria (Внутри скобки не обязательны)
		 */
		$modelObjects = uClasses::getclass('codename3')->objects();
		$fix_criteria = clone $modelObjects->getDbCriteria();

		//установка критерии для поиска по свойству
		$criteria1 = $modelObjects->getPropCriteria('condition','codename1', 'codename1=:param_prop_1');
		$criteria1->params[':param_prop_1'] = 'd type upcharfield line1 header 10';

		//установка критерии для поиска по обычному параметру
		$criteria2 = new CDbCriteria;
		$criteria2->addCondition($modelObjects->getTableAlias().'.param1=:param_param1');
		$criteria2->params[':param_param1'] = 'text param1 header 14';

		$criteria2->mergeWith($criteria1); //($criteria1 AND $criteria2)

		$new_criteria = new CDbCriteria;
		$new_criteria->mergeWith($fix_criteria);
		$new_criteria->mergeWith($criteria2); //(...) AND ($criteria1 AND $criteria2)
		$this->assertEquals(1, $modelObjects->count($new_criteria));
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