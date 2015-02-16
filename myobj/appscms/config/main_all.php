<?php
$objects = SysUtils::array_array_merge(
	SysUtils::importRecursName('MYOBJ.appscms.config.admin','objects_*',true)
);
$models = SysUtils::array_array_merge(
	SysUtils::importRecursName('MYOBJ.appscms.config.admin','models_*',true)
);

$main = array(
	'controlui' => array(
		'objects' => array( //columns header type model, controller inside myobj/controllers/cms
			'conf_ui_classes' => $objects,
			'models' => $models,
		),
	),
	'TYPES_MYFIELDS_CHOICES' => array(
		'1'=>'str',
		'2'=>'text',
		'3'=>'html',
		'4'=>'datetime',
		'5'=>'url',
		'6'=>'email',
		'7'=>'bool',
	),
	'TYPES_COLUMNS' => array(
		'1'=>'upcharfield',
		'2'=>'uptextfield',
		'3'=>'uptextfield',
		'4'=>'updatetimefield',
		'5'=>'uptextfield',
		'6'=>'upcharfield',
		'7'=>'upcharfield',
	),
	'TYPES_MYFIELDS' => array(
		'str'=>'text',
		'text'=>'textarea',
		'html'=>'textarea',
		'datetime'=>'text',
		'url'=>'text',
		'ip'=>'text',
		'email'=>'text',
		'bool'=>'checkbox',
	),
	'rulesvalidatedef' => array( //Valid values include 'string', 'integer', 'float', 'array', 'date', 'time' and 'datetime'.
		'str' => '
type
type=>string
',
		'datetime' => '
type
type=>datetime
datetimeFormat=>yyyy-MM-dd hh:mm:ss
',
	),

'language_def' => 'en',
'languages' => array('ru', 'en', 'th', 'vi', 'de'),

'sys_db_type_InnoDB'=>false, //использовать если в системе нет поддержки внешних ключей
);

return $main;