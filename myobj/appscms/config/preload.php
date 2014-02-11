<?php
//системные компоненты
$components_preload = array(

);

Yii::app()->params['api_conf_components_preload'] = $components_preload;
//добавляем пользовательские компоненты
apicms\utils\importRecursName('MYOBJ.appscms.config.user','preload_*',true);
$components_preload = Yii::app()->params['api_conf_components_preload'];
unset(Yii::app()->params['api_conf_components_preload']);
return $components_preload;