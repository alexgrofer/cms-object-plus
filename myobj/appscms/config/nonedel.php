<?php
//запрет на удаление определенных классов и объектов, свойств
$nonedel = array(
	'uClasses' => array(
		//array('codename'=>'8', 'tablespace'=>'1'),
		array('codename'=>'groups_sys'),
		array('codename'=>'templates_sys'),
		array('codename'=>'views_sys'),
		array('codename'=>'handle_sys'),
		array('codename'=>'navigation_sys'),
		array('codename'=>'param_sys'),
		array('codename'=>'db_dump_sys'),
	),
	'objProperties' => array(

	),
	//для классов - в одной таблице могут быть объекты разных классов
	'$objects$' => array(
		'groups_sys' => array(
			array('identifier_role'=>'guest'),
			array('identifier_role'=>'user'),
			array('identifier_role'=>'moderator'),
			array('identifier_role'=>'administrator'),
		),
		//'za' => array(
			//array('name'=>'sdf', 'sort'=>'33'),
		//),
	),
);

Yii::app()->params['api_conf_nonedel'] = $nonedel;

apicms\utils\importRecursName('MYOBJ.appscms.config.user','nonedel_*',true);
$nonedel = Yii::app()->params['api_conf_nonedel'];
unset(Yii::app()->params['api_conf_nonedel']);
return $nonedel;