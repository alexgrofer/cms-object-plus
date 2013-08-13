<?php
class StoreFiles extends CComponent {
	private $test8;
	public function  setTest8($val) {
		$this->test8 = $val;
	}
	public function getTest8() {
		return $this->test8;
	}
	public function init() {
		//import
		Yii::import('application.modules.myobj.models.modul_storefile.*');
		Yii::import('application.modules.myobj.components.storefiles.procFilesStorage.*');
	}

	/**
	 * Получает объект типа AbsBaseStoreFiles
	 * @param string $plugin названия плагина
	 * @param string $model название модели в которой хранятся файлы
	 * @return Abs типа
	 */
	public function instanceObj($plugin) {
		$modelName = $plugin::namemodel();
		$storefileobj = $modelName::model() - //а у него уже должны быть все необходимые методы для редактирования удаления и создания нового
		$storefileobj->plugin = $plugin;//и дополняет необходимым и рапаметрами из плагина
	}

	/**
	 * Получает массив объектов @link AbsBaseStoreFiles модели плагина $plugin
	 * @param array или массив ключей для поиска или
	 * @param string $plugin название класса плагина
	 * @return массив найденных файлов
	 */
	public function getObj($keys,$plugin) {
		AbsBaseStoreFiles
	}
}
