<?php
define('IS_CONSOLE', true);

$yiit=dirname(__FILE__).'/../../../../../vendor/yii/framework/yiit.php';
require_once($yiit);
$config=dirname(__FILE__).'/config_test.php';
Yii::createWebApplication($config);

Yii::import('application.modules.myobj.*');
MyobjModule::createCMS();
