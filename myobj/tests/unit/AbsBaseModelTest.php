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

	public function testEdit_EArray() {

	}

	public function testGet_EArray() {
		$objModel = new TestAbsBaseModel();
		$objModel->edit_EArray('val_param1','content_e_array_1','param_1');
		$objModel->edit_EArray('val_param2','content_e_array_1','param_2');
		$objModel->save();
		$findObjModel = TestAbsBaseModel::model()->findByPk($objModel->primaryKey);

		$allEArray = $findObjModel->get_EArray('content_e_array_1');
		$this->assertCount(2, $allEArray);
		$this->assertArrayHasKey('param_1', $allEArray);
		$this->assertArrayHasKey('param_2', $allEArray);

		$param1ValEArray = $findObjModel->get_EArray('content_e_array_1', 'param_1');
		$this->assertEquals('val_param1', $param1ValEArray);

		$param1ValEArray = $findObjModel->get_EArray('content_e_array_1', 'param_2');
		$this->assertEquals('val_param2', $param1ValEArray);
	}

	public function testHas_EArray() {
		
	}
}