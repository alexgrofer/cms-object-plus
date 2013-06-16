<?php

$objects = array(
    'systemObjHeaders' => array(
        'default' => array(
            'cols' => array('id'=>'id','name'=>'name'),
            'groups_read' => null,
            'groups_write' => null,
        ),
        $classes_system['handle'] => array(
            'cols' => array('id'=>'id','name'=>'namehandle','vp1'=>'id view'),
            'edit' => array('name', 'sort', array('vp1','idview')),
            'groups_read' => null,
            'groups_write' => null,
        ),
        $classes_system['navigation'] => array(
            'cols' => null,
            'edit' => array('name', 'sort', array('vp2','codename'),array('vp1','top'),array('bp1','visible')),
            'controller' => 'admin/nav_sys.php',
            'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
            'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        ),
        $classes_system['group'] => array(
            'cols' => array('id'=>'id','name'=>'name','vp1'=>'outside identifier','vp2'=>'codename',),
            'edit' => array('name', array('vp1','outside-id_group'), array('content','description'), array('vp2','codename')),
            'groups_read' => null,
            'groups_write' => null,
        ),
        $classes_system['template'] => array(
            'cols' => array('id'=>'id','name'=>'name template'),
            'edit' => array('name', array('vp1','patch_template'), array('content','description')),
            'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
            'groups_write' => null,
            'order_by' => array(array('id', 'desc')),
        ),
        $classes_system['view'] => array(
            'cols' => array('id'=>'id','name'=>'name view'),
            'edit' => array('name', array('vp1','patch_view'), array('content','description')),
            'groups_read' => null,
            'groups_write' => null,
        ),
        $classes_system['params'] => array(
            'cols' => array('id'=>'id','name'=>'name','vp1'=>'codename'),
            'edit' => array('name', array('vp1','codename'),'content'),
            'groups_read' => null,
            'groups_write' => null,
        ),
    ),
);

$set_spaces = array(
    '2' => array('namemodel'=>'systemObjHeaders','namelinksmodel'=>'linksObjectsAllSystem'),
);
require(dirname(__FILE__).'/user/objects.php');
$objects = array_merge($objects,$objects_user);