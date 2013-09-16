<?php
class AbsPluginStoreFile
{
	protected static $nameClassFile='CStoreFile';
	/**
	 * Возвращает объекты типа файла
	 * @param $arrIdObj
	 */
	public static function factoryInit($arrIdObj) {
		$classFile =  static::$nameClassFile;
		if($arrIdObj==null) {
			return $classFile::init(__CLASS__, null);
		}
		//вернуть массив
	}
}
