<?php
final class DefaultPluginStoreFile extends AbsPluginStoreFile implements IPluginStoreFileARModel
{
	const PATH_LOAD = 'media/upload/storefile'; //главная дирректория плагина, не можем изменять
	const NAME_CLASS_FILE= 'CStoreFile';
	const AR_MODEL_STORE_FILE= 'ModelARStoreFile';


	public function buildStoreFile($ARObj) {
		//создать объект файла
		$classStoreFile = self::NAME_CLASS_FILE;
		$objStoreFile = $classStoreFile();
		//собрать и вернуть готовый объект файл
	}

	public function factoryInit($arrIdObj=null) {
		$nameClassARModel = self::AR_MODEL_STORE_FILE;
		$objModelStoreFile = $nameClassARModel::model();
		if(!$arrIdObj) {
			return $this->buildStoreFile($objModelStoreFile);
		}
		elseif(count($arrIdObj)) {
			$objModelStoreFile->dbCriteria->addInCondition('id', $arrIdObj);
			$arrayObjARStoreFile = $objModelStoreFile->findAll();
			$arrayObjStoreFile = array();
			foreach($arrayObjARStoreFile as $ARObj) {
				$arrayObjStoreFile[] = $this->buildStoreFile($ARObj);
			}
			return $arrayObjStoreFile;
		}
	}
}