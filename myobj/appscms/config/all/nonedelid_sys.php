<?php
return array(
	//обычные модели
	'uClasses' => array(
		1, //groups_sys
		2, //views_sys
		3, //templates_sys
		4, //handle_sys
		5, //navigation_sys
		6, //param_sys
		10, //db_dump_sys
	),
	//заголовки
	'$objects$' => array(
		'groups_sys' => array(
			array('identifier_role'=>'guest'),
			array('identifier_role'=>'user'),
			array('identifier_role'=>'moderator'),
			array('identifier_role'=>'administrator'),
		),
	),
);