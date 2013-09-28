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
		Yii::import('application.modules.myobj.components.storefile.behaviors.*');
		Yii::import('application.modules.myobj.models.modul_storefile.*');
	}

	/**
	 * Инициализирует объекты с необходимым плагином для работы
	 * @param string $nameClassPlugin название класса плагина
	 * @param array $arrIdObj список объектов
	 * @return mixed возвращает объект или список(array) объектов класса FileStoreCms
	 */
	public function obj($nameClassPlugin=null,array $arrIdObj=null) {
		if(is_array($nameClassPlugin))  $arrIdObj = $nameClassPlugin;
		if($nameClassPlugin===null || is_array($nameClassPlugin)) $nameClassPlugin = 'DefaultPluginStoreFile';
		$params = array('isSelfEdit'=>false); //дополнительные параметры если это необходимо (например резать изображения другим способом)
		$objPlugin = new $nameClassPlugin($params);
		if($arrIdObj) {
			return $objPlugin->factoryInit($arrIdObj);
		}
		else {
			return $objPlugin->factoryInit(null);
		}
	}

	/**
	 * Удаление объекта-тов из базы данных
	 * @param string $nameClassPlugin название класса плагина
	 * плагин может обладать некими спицифичными
	 * @param array $arrObj список объектов
	 */
	public function delobj($nameClassPlugin,array $arrIdObj) {
		$nameClassPlugin::delete($arrIdObj);
	}
}
