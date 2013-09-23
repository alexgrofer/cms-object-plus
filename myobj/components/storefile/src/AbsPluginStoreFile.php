<?php
abstract class AbsPluginStoreFile
{
	protected static $nameClassFile='';
	/**
	 * @var Некоторые дополнительные параметры для поведения плагина (будет кропать картинки определенным способом)
	 */
	protected $_params;
	public function __construct($params=array()) {
		$this->_params = $params;
	}

	/**
	 * Возвращает объект-ы типа StoreFile
	 * @param $arrIdObj
	 */
	public function factoryInit($arrIdObj) {
		if(!$arrIdObj) {
			return $this->createObjStoreFile();
		}
		elseif(count($arrIdObj)) {
			$arrayObjStoreFile = array();
			foreach($arrIdObj as $idObj) {
				$arrayObjStoreFile[] = $this->createObjStoreFile($idObj);
			}
			return $arrayObjStoreFile;
		}
	}
}
