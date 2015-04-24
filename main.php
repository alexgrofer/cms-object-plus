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
);
