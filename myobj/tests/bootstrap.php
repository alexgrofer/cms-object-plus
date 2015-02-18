<?php
//selenium
$_SERVER['SCRIPT_FILENAME'] = realpath(__DIR__.'/../../../../index.php');
$_SERVER['SCRIPT_NAME'] = basename($_SERVER['SCRIPT_FILENAME']);

$path_folder_framework = '/../../../../../frameworks/yii/framework';

// change the following paths if necessary
$yiit=dirname(__FILE__).$path_folder_framework.'/yiit.php'; //your patch framework

defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require_once($yiit);

Yii::setPathOfAlias('MYOBJ', dirname(__DIR__));
Yii::import('MYOBJ.models.sys.*');
Yii::import('MYOBJ.components.cms.behaviors.*');

Yii::import('MYOBJ.tests.vendor.models.*');
Yii::import('MYOBJ.tests.vendor.src.*');

Yii::$enableIncludePath = false; //problem phpunit.phar - PHP Warning:  include(PHPUnit_Extensions_Story_TestCase.php)
require_once(dirname(__FILE__).'/WebTestCase.php');

$config=dirname(__FILE__).'/config_test.php';
Yii::createWebApplication($config);

// automatically send every new message to available log routes
Yii::getLogger()->autoFlush = 1;
// when sending a message to log routes, also notify them to dump the message
// into the corresponding persistent storage (e.g. DB, email)
Yii::getLogger()->autoDump = true;

yii::app()->setComponents(array(
	'appcms'=>array(
		'class' =>'MYOBJ.components.cms.AppCMS',
		'isAdminUI'=>false //тестируем не админку
	)
));
