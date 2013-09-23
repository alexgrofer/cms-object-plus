<?php
final class DefaultPluginStoreFile extends AbsPluginStoreFile
{
	const PATH_LOAD = 'media/upload/storefile'; //главная дирректория плагина, не можем изменять
	protected static $nameClassFile='CStoreFile';
	/**
	 * При создании объекта CStoreFile
	 * @param $objStoreFile
	 * @param $objAR
	 */
	public function ConstructEventStoreFile($conf) {
		//
	}
}