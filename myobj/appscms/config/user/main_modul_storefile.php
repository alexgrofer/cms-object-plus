<?php
$main_user = array(
	'homeDirStoreFile' => 'media/upload/storefile', //папка для загрузок исполюзуется в дефолтовом плагине, или может использоваться в любом другом
	'ClassesFilesStorageProc' => array(
		'PluginFilesStorageDefault'=>'Default',
		'PluginFilesStorageExtra' => 'Extra', //дополнительный плагин, например для загрузки изображений
	),
);

Yii::app()->params['api_conf_main'] =  array_merge_recursive(Yii::app()->params['api_conf_main'],$main_user);
