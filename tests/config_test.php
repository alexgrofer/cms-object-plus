<?php

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

$config =  CMap::mergeArray(
	require(dirname(__FILE__).'/../../../config/test.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'MYOBJ.tests.DbFixtureManager',
				'basePath'=>yii::getPathOfAlias('MYOBJ.tests.fixtures'),
			),

			'db'=>array(
				'connectionString'=>'mysql:host=localhost;dbname=DBTest',
			),

		),
	)
);

$config['components']['log'] = array(
	'class'=>'CLogRouter',
	'routes'=>array(
		array(
			'class'=>'CFileLogRoute',
			'logFile'=>'application_test.log',
			'levels'=>'', //все уровни
		),
	),
);

return $config;
