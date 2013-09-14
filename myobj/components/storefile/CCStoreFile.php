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
		Yii::import('application.modules.myobj.components.storefile.src.*');
		Yii::import('application.modules.myobj.components.storefile.src.plugins.*');
	}

	/**
	 * Инициализирует объекты с необходимым плагином для работы
	 * @param string $nameClassPlugin название класса плагина
	 * @param array $arrIdObj список объектов
	 * @return mixed возвращает объект или список объектов класса FileStoreCms
	 */
	public function obj($nameClassPlugin=null,array $arrIdObj=null) {
		if(is_array($nameClassPlugin) || is_int($arrIdObj) || $arrIdObj===null) {
			$nameClassPlugin = 'DefaultPluginStoreFile';
			$arrIdObj = $nameClassPlugin;
		}
		if($arrIdObj) {
			if(is_int($arrIdObj)) {
				$arrIdObj = array($arrIdObj);
			}
			$objectsDB = $nameClassPlugin::nameModel->findinkey($arrIdObj);
			$objectsArrayStoreFile = array();
			foreach($objectsDB as $obj) {
				$objectsArrayStoreFile[] = CStoreFile::init($nameClassPlugin,$obj);
			}
			return $objectsArrayStoreFile;
		}
		else {
			return CStoreFile::init($nameClassPlugin,null);
		}
	}

	/**
	 * Удаление объекта-тов из базы данных
	 * @param string $nameClassPlugin название класса плагина
	 * плагин может обладать некими спицифичными
	 * @param array $arrObj список объектов
	 */
	public function delobj($nameClassPlugin,array $arrIdObj) {
		// примерно таким же образом как self::obj()
		CStoreFile::del($arrIdObj);
	}
}
