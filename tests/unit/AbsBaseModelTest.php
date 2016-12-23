<?php
class AbsBaseModelTest extends AbsDbTestCase {
	public $fixtures=array(
		'objectAbsBaseModel'=>'TestAbsBaseModel', //объекты AbsBaseModel
	);

	function testBeforeDelete() {
		$objHeader1 = $this->objectAbsBaseModel('TestAbsBaseModel_sample_id_1');

		//изменим конфиг приложения (task перенести в тест AbsModelTest)
		$config = [];
		$config['none_del_id']['TestAbsBaseModel'] = array($objHeader1->primaryKey);
		self::editTestConfig(array_merge_recursive(Yii::app()->appcms->config, $config));

		$objHeader1->delete();

		//объект небыл удален так как это запрещено в конфигурации
		$findObjHeader = $objHeader1::model()->findByPk($objHeader1->primaryKey);
		$this->assertNotNull($findObjHeader);
	}

	function testEditEArray() {
		$objHeader1 = $this->objectAbsBaseModel('TestAbsBaseModel_sample_id_1');
		//свойство которое есть в правилах конфига
		$objHeader1->editEArray('param1', array('p_EArray_1'=>'123456'));
		//есть ошибки
		$this->assertFalse($objHeader1->validate());
		$errors = $objHeader1->getErrors();
		//ошибка в параметре EArray
		$this->assertTrue(isset($errors['p_EArray_1']));
		//поправил значение
		$objHeader1->editEArray('param1', array('p_EArray_1'=>'12345'));
		//теперь нет ошибки
		$this->assertTrue($objHeader1->validate());
		//сохранить
		$objHeader1->save();
		//перезагрузим модель
		$findObjHeader = $objHeader1::model()->findByPk($objHeader1->primaryKey);
		//получить свойство именно то которое было сохраненно
		$this->assertEquals($findObjHeader->getEArray('param1', 'p_EArray_1'), '12345');
		//свойство которого нет в правилах
		$objHeader1->editEArray('param1', array('prop_none_rule'=>'test1'));
		//нет ошибки
		$this->assertTrue($objHeader1->validate());
		//сохранить
		$objHeader1->save();
		//перезрузать
		$findObjHeader = $objHeader1::model()->findByPk($objHeader1->primaryKey);
		//получит все два свойства все они совпадают
		$this->assertEquals($findObjHeader->getEArray('param1', 'p_EArray_1'), '12345');
		$this->assertEquals($findObjHeader->getEArray('param1', 'prop_none_rule'), 'test1');
		$array_all = $findObjHeader->getEArray('param1');
		$this->assertEquals(count($array_all), 2);
		$this->assertEquals($array_all['p_EArray_1'], '12345');
		$this->assertEquals($array_all['prop_none_rule'], 'test1');
		//свойству поставить пустоту
		//сохранить
		//через спец метод получить сериализацию по свойству и понять что там пустота реально

		//проверка json
	}
}
