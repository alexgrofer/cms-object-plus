<?php
$models = array(
	'classes' => array(
		'namemodel' => 'uClasses',
		//'witch' => array('relattest', 'relattest.relattest2'), @todo сделать возможность witch
		'relation' => array('properties'=>array('properties','classes'), 'association'=>array('classes','association')),
		'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','tablespace'=>'tablespace','objectCount'=>'countObj',),
		'order_by_def' => array('id desc'),
		//'selfobjrelationElements' => array('properties'=>array('test',)), //сделать описание task
	),
	'properties' => array(
		'namemodel' => 'objProperties',
		'relation' => array('classes'=>array('classes','properties')),
		//'selfobjrelationElements' => array('classes'=>array('test',)), //сделать описание task
		'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','myfield'=>'type'),
		'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	//USER
	'user' => array(
		'namemodel' => 'User',
		'relation' => array('groups'=>array('group','users'),'userpasport'=>array('userpasport','user')),
		'cols' => array('id'=>'id','login'=>'user name'),
		'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
		'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	'group' => array(
		'namemodel' => 'Ugroup',
		'relation' => array('users'=>array('user','groups')), // реляция => [название модели B, реляция в которой обратная ссылка модели B]
		'cols' => array('id'=>'id','name'=>'name','guid'=>'guid'),
		'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
	),
	'userpasport' => array(
		'namemodel' => 'UserPasport',
		'relation' => array('user'=>array('user','userpasport')),
		'cols' => array('id'=>'id','lastname'=>'last name','firstname'=>'first name', 'user_id'=>'user_id'),
	),
	//example models
	'testtablehm' => array(
		'namemodel' => 'TestTableHM',
		'relation' => array('myobjheader'=>array('news_example','test_relat_objcts'))
	),
);

Yii::app()->params['api_conf_models'] = $models;

apicms\utils\importRecursName('MYOBJ.appscms.config.user','models_*',true);
$models = Yii::app()->params['api_conf_models'];
unset(Yii::app()->params['api_conf_models']);
return $models;