<?php
class СStoreFiles extends CComponent {
	private $test8;
	public function  setTest8($val) {
		$this->test8 = $val;
	}
	public function getTest8() {
		return $this->test8;
	}
	public function init() {
		//import
		Yii::import('application.modules.myobj.components.storefiles.procFilesStorage.*');
	}

	public function newobj($nameClassPlugin,$params) {
		return $nameClassPlugin::newobj($params);
	}

	public function getobj($nameClassPlugin,$params) {
		return $nameClassPlugin::get($params);
	}

	public function editobj($nameClassPlugin,$params) {
		return $nameClassPlugin::editobj($params);
	}

	public function delobj($nameClassPlugin,$params) {
		return $nameClassPlugin::delobj($params);
	}
}
