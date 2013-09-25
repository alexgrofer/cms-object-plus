<?php
/**
 * Интерфейс работает с файлами которые хранятся в контексте базы данных CActiveRecord
 * Interface IPluginStoreFileARModel
 */
interface IPluginStoreFileARModel
{
	public function buildStoreFile($ARObj);
}