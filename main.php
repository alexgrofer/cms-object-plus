<?php

return array(
	'modules'=>array(
		"myobj",
	),

	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class' => 'application.modules.myobj.components.WebUser',
		),
		'authManager'=>array(
			'class' => 'application.modules.myobj.components.PhpAuthManager',
			'defaultRoles' => array('guest'),
		),
	),
	'urlManager'=>array(
		'urlFormat'=>'path',
		'showScriptName'=>false,
		'rules'=>array(
			'myobj/admin'=>'myobj/admin',
			//test create url $this->createUrl('/myobj/admin',array('id'=>100,'year'=>2008));
			'showScriptName'=>false,
		),
	),
);
