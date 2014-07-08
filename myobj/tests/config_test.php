<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/../../../config/main.php'),
	array(
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
				'basePath'=>yii::getPathOfAlias('MYOBJ.tests.fixtures'),
			),

			'db'=>array(
				'connectionString'=>'mysql:host=localhost;dbname=DBTest',
			),

			'log'=>array(
				'class'=>'CLogRouter',
				'routes'=>array(
					array(
						'class'=>'CFileLogRoute',
						'levels'=>'error, warning, trace',
					),
				),
			),

		),
	)
);
