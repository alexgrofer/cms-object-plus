<?php
return array(
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
		'namemodel' => 'UserAdmin',
		'cols' => array('id'=>'id','login'=>'user name'),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
	'group' => array(
		'namemodel' => 'UgroupAdmin',
		'relation' => array(
			'users'=>array('user','groups'),
		),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
);