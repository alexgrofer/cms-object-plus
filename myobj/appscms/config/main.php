<?php
defined('DIR_TEMPLATES_SITE') or define('DIR_TEMPLATES_SITE', '/site/templates/');
defined('DIR_VIEWS_SITE') or define('DIR_VIEWS_SITE', '/site/views/');

list($objects,$set_spaces,$classes_system) = apicms\utils\importRecursName('MYOBJ.appscms.config','objects.php',true,true);
$models = apicms\utils\importRecursName('MYOBJ.appscms.config','models.php',true,true);

$main = array(
	'controlui' => array(
		'objects' => array( //columns header type model, controller inside myobj/controllers/cms
			'conf_ui_classes' => $objects,
			'models' => $models,
		),
	),
	'spacescl' => $set_spaces,
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

'classes_system' => $classes_system,
'language_def' => 'en',
'languages' => array('ru', 'en', 'th', 'vi', 'de'),

'sys_db_type_InnoDB'=>true, //в случае true не будет делать лишний запрос для удаления ссылок MTM при работе с AbsBaseModel::clearMTMLink
);

//возможно дополнение конфигурации $main дополнительными пользовательскими параметрами
Yii::app()->params['api_conf_main_user'] = array();
apicms\utils\importRecursName('MYOBJ.appscms.config.user','main_*',true);
$main_user = Yii::app()->params['api_conf_main_user'];
$main['user_conf'] = $main_user;
unset(Yii::app()->params['api_conf_main_user']);
return $main;
