<?php
//основные системные компоненты
$components = array(
	'storeFile'=>array(
		'class' =>'application.modules.myobj.components.storefile.CCStoreFile',
		'test8'=>'texttest'
	),
);

Yii::app()->params['api_conf_components'] = $components;
//добавляем пользовательские компоненты
apicms\utils\importRecursName('application.modules.myobj.appscms.config.user','components_*',true);
$components = Yii::app()->params['api_conf_components'];
unset(Yii::app()->params['api_conf_components']);
return $components;