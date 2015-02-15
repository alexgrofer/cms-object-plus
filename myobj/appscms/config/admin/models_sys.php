<?php
$models = array(
	'classes' => array(
		'namemodel' => 'uClasses',
		'relation' => array('properties'=>array('properties','classes'), 'association'=>array('classes','association')),
		'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','tablespace'=>'tablespace','objectCount'=>'countObj',),
		'order_by_def' => array('id desc'),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'properties' => array(
		'namemodel' => 'objProperties',
		'relation' => array('classes'=>array('classes','properties')),
		'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','myfield'=>'type'),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	//USER
	'user' => array(
		'namemodel' => 'User',
		'relation' => array('groups'=>array('group','users'),'userpasport'=>array('userpasport','user')),
		'cols' => array('id'=>'id','login'=>'user name'),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'group' => array(
		'namemodel' => 'Ugroup',
		'relation' => array('users'=>array('user','groups')), // реляция => [название модели B, реляция в которой обратная ссылка модели B]
		'cols' => array('id'=>'id','name'=>'name','guid'=>'guid'),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'userpasport' => array(
		'namemodel' => 'UserPasport',
		'relation' => array('user'=>array('user','userpasport')),
		'cols' => array('id'=>'id','lastname'=>'last name','firstname'=>'first name', 'user_id'=>'user_id'),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
);

Yii::app()->params['api_conf_models'] = $models;

apicms\utils\importRecursName('MYOBJ.appscms.config.user','models_*',true);
$models = Yii::app()->params['api_conf_models'];
unset(Yii::app()->params['api_conf_models']);
return $models;