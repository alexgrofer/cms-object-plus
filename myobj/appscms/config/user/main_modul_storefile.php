<?php
$main_user = array(
'homeDirStoreFile' => 'media/upload/storefile',
'ClassesFilesStorageProc' => array('classFilesStorageTest' => 'Test',),
);

Yii::app()->params['api_conf_main'] =  array_merge_recursive(Yii::app()->params['api_conf_main'],$main_user);
