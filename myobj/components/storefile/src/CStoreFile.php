<?php
class CStoreFile extends AbsCStoreFile {

	public static function create($arrConf) {
		$objPlugin = $arrConf['objPlugin'];
		$objPlugin->buildStoreFile();
	}
}