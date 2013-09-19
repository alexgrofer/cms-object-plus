<?php
class AbsPluginStoreFile
{
	protected static $nameClassFile='CStoreFile';
	/**
	 * @var Некоторые дополнительные параметры для поведения
	 */
	protected $_params;
	public function __construct($params=array()) {
		$this->_params = $params;
	}

	/**
	 * Возвращает объекты типа файла
	 * @param $arrIdObj
	 */
	public function factoryInit($arrIdObj) {
		$nameClassFile =  static::$nameClassFile;
		if($arrIdObj==null) {
			return new $nameClassFile($this, null);
		}
		elseif(count($arrIdObj)) {
			//вернуть массив
		}
		return null;
	}
}
