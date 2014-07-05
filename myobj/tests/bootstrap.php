<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../../../frameworks/yii-1.1.15.022a51/framework/yiit.php';
$config=dirname(__FILE__).'/../../../config/test.php';

require_once($yiit);

Yii::setPathOfAlias('MYOBJ', '/var/www/projects/realty/protected/modules/myobj');
Yii::import('MYOBJ.models.sys.*');
Yii::import('MYOBJ.components.cms.behaviors.*');


Yii::$enableIncludePath = false;
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($config);
