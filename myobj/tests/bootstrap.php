<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../../../frameworks/yii-1.1.15.022a51/framework/yiit.php'; //your patch framework
$config=dirname(__FILE__).'/config_test.php';

require_once($yiit);

Yii::setPathOfAlias('MYOBJ', dirname(__FILE__).'/../../myobj');
Yii::import('MYOBJ.models.sys.*');
Yii::import('MYOBJ.components.cms.behaviors.*');


Yii::$enableIncludePath = false; //problem phpunit.phar - PHP Warning:  include(PHPUnit_Extensions_Story_TestCase.php)
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($config);
