<?php
abstract class AbsPluginStoreFile
{
	/**
	 * @var string Название класса файла
	 */
	protected static $nameClassFile='';

	/**
	 * @var Некоторые дополнительные параметры для поведения плагина (будет кропать картинки определенным способом)
	 */
	protected $_params;
	public function __construct($params=array()) {
		$this->_params = $params;
	}

	/**
	 * Фабрика объектов файлов
	 * @param null $arrIdObj
	 * @return mixed
	 */
	abstract public function factoryInit($arrIdObj=null);
}
