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
}
