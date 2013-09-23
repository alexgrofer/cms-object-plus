<?php
class CStoreFile extends AbsCStoreFile {
	public $test = 5;
	public static function create($conf) {
		$nameClassStoreFile = $conf['objPlugin']::nameClassFile;
		$objStoreFile = new $nameClassStoreFile();
		$conf['objPlugin']->ConstructEventStoreFile(array('objStoreFile'=>$objStoreFile, 'objAR'=>$conf['objAR']));
	}
}