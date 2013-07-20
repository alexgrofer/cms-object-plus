<?php
$models = array(
    'classes' => array(
        'namemodel' => 'uClasses',
        'edit' => null, //все
        //'witch' => array('relattest', 'relattest.relattest2'), task сделать возможность
        'addcontroller' => '', //task проверить
        'relation' => array('properties', 'classes' => 'association'),
        'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','tablespace'=>'tablespace','objectCount'=>'countObj',),
        'order_by_def' => array('id desc')
    ),
    'properties' => array(
        'namemodel' => 'objProperties',
        'edit' => null,
        'relation' => array('classes'),
        'selfobjrelationElements' => array('classes'=>array('test',)), //сделать описание task
        'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','myfield'=>'type'),
        'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
    ),
    //alias
    'uclass' => 'classes',

    //USER
    'user' => array('namemodel' => 'User', 'relation' => array('group','userpasport'), 'cols' => array('id'=>'id','login'=>'user name'), 'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
    'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    'group' => array('namemodel' => 'Ugroup', 'relation' => false, 'cols' => array('id'=>'id','name'=>'name','guid'=>'guid'), 'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    'userpasport' => array('namemodel' => 'UserPasport', 'relation' => false, 'cols' => array('id'=>'id','firstname'=>'first name','lastname'=>'last name')),
    //storage files
    'storagef' => array(
        'namemodel' => 'filesStorage',
        'edit' => null,
        'order_by' => array('id DESC'),
    ),
);
require(dirname(__FILE__).'/user/models.php');
$models = array_merge($models,$models_user);