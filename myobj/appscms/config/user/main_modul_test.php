<?php
$main_user = array(
	
);

Yii::app()->params['api_conf_main'] =  array_merge_recursive(Yii::app()->params['api_conf_main'],$main_user);