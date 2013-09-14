<?php
class CCStoreFile extends CComponent {
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

	/**
	 * Инициализирует объекты с необходимым плагином для работы
	 * @param string $nameClassPlugin название класса плагина
	 * @param array $arrIdObj список объектов
	 * @return mixed возвращает объект или список объектов класса FileStoreCms
	 */
	public function obj($nameClassPlugin,array $arrIdObj=null) {
		return $nameClassPlugin::init($arrIdObj);
	}

	/**
	 * Удаление объекта-тов из базы данных
	 * @param string $nameClassPlugin название класса плагина
	 * плагин может обладать некими спицифичными
	 * @param array $arrObj список объектов
	 */
	public function delobj($nameClassPlugin,array $arrIdObj) {
		return $nameClassPlugin::delobj($arrIdObj);
	}
}
