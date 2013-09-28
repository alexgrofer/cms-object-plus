<?php
class ModelARStoreFile extends AbsModelARStoreFile
{
	/**
	 * @var Название плагина который будет использоваться для этой модели
	 */
	public $namePluginLoader=EnumerationPluginStoreFile::DEF;
	/**
	 * @var array настройки плагина:
	 * ar_model_store_file - название модели для плагина
	 */
	public $pluginConstructLoaderParamsConf=array('ar_model_store_file'=>'ModelARStoreFile');
}
