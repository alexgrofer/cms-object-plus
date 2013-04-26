<?php
$classes_system = array(
    'group'=>'groups_sys',
    'template'=>'templates_sys',
    'view'=>'views_sys',
    'handle'=>'handle_sys',
    'navigation'=>'navigation_sys',
    'params'=>'param_sys',
);

require(dirname(__FILE__).'/objects.php');
require(dirname(__FILE__).'/models.php');
require(dirname(__FILE__).'/UI.php');
return array(
    'controlui' => array(
        'objects' => array( //columns header type model, controller inside myobj/controllers/cms
            'headers_spaces' => $objects,
            'models' => $models,
        ),
        'ui' => $ui,
    ),
    'spacescl' => $set_spaces,
    'menumodel' => $models_menu,
    'menuui' => $ui_menu,
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
'countelements' => 10,
'countpage' => 10,
'bdcms_pref' => 'setcms_',
'objindexname' => 'index',
);
//u_randomname=>0
//u_patch=>/
