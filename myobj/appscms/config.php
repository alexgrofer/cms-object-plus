<?php
$classes_system = array(
    'group'=>'groups_sys',
    'template'=>'templates_sys',
    'view'=>'views_sys',
    'handle'=>'handle_sys',
    'navigation'=>'navigation_sys',
    'params'=>'param_sys',
);

require(dirname(__FILE__).'/UIconfig.php');
return array(
    'controlui' => array(
        'objects' => array( //columns header type model, controller inside myobj/controllers/cms
            'headers_spaces' => array(
                //codename class
                'myObjHeaders' => array(
                    'default' => array(
                        'cols' => array('id'=>'id','name'=>'name'),
                        'groups_read' => null,
                        'groups_write' => null,
                    ),
                    //headers links example
                    /*
                    'exampleclass' => array(
                        'cols' => array('id'=>'id','name'=>'name'),
                        'cols_props' => array('newtexttest'=>'newtexttest',),
                        'order_by' => array(array('id', 'desc')),
                        'relation' => array('lines'),
                    ),
                    */
                    
                ), //cols
                'systemObjHeaders' => array(
                    'default' => array(
                        'cols' => array('id'=>'id','name'=>'name'),
                        'groups_read' => null,
                        'groups_write' => null,
                    ),
                    $classes_system['handle'] => array(
                        'cols' => array('id'=>'id','name'=>'namehandle','vp1'=>'id view'),
                        'edit' => array('name', 'sort', array('vp1','idview')),
                        'relation' => array('uclass'),
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
                        'edit' => array('name', array('vp1','codename')),
                        'groups_read' => null,
                        'groups_write' => null,
                    ),
                ),
            ),
            'models' => array(
                'classes' => array(
                    'namemodel' => 'uClasses',
                    'edit' => null,
                    'relation' => array('properties', 'classes' => 'association'),
                    'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','tablespace'=>'tablespace'),
                    'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
                    'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
                    'order_by' => array(array('id', 'desc')),
                ),
                'properties' => array(
                    'namemodel' => 'objProperties',
                    'edit' => null,
                    'relation' => null,
                    'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','myfield'=>'type'),
                    'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
                    'groups_write' => null,
                    'order_by' => array(array('id', 'desc')),
                ),
                //alias
                'uclass' => 'classes',
                
                //USER
                'user' => array('namemodel' => 'User', 'relation' => array('group','userpasport'), 'cols' => array('id'=>'id','login'=>'user name'), 'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
                'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
                'group' => array('namemodel' => 'Ugroup', 'relation' => false, 'cols' => array('id'=>'id','name'=>'name','guid'=>'guid')),
                'userpasport' => array('namemodel' => 'UserPasport', 'relation' => false, 'cols' => array('id'=>'id','firstname'=>'first name','lastname'=>'last name')),
                
                //headers links example
                /*
                'myObjHeaders' => array('namemodel' => 'myObjHeaders', 'cols' => array('id'=>'ids','name'=>'name')),
                'myObjLines' => array('namemodel' => 'myObjLines', 'relation' => array('property'), 'cols' => array('id'=>'id','uptextfield'=>'textfield','upcharfield'=>'charfield','uptimefield'=>'timefield','updatefield'=>'datefield','upintegerfield'=>'integerfield','upfloatfield'=>'floatfield')),
                'lines' => 'myObjLines', //alias
                'property' => 'properties', //alias
                */
            ),
        ),
        'ui' => $ui,
    ),
    'spacescl' => array(
        '1' => array('namemodel'=>'myObjHeaders','namelinksmodel'=>'linksObjectsAllMy'),
        '2' => array('namemodel'=>'systemObjHeaders','namelinksmodel'=>'linksObjectsAllSystem'),
        '3' => array('namemodel'=>'storedepObjHeaders','namelinksmodel'=>null),
    ),
    'menumodel' => array(
        array('user'),
        array('group'),
        array('userpasport'),
    ),
    'menuui' => $ui_menu,
    'TYPES_MYFIELDS_CHOICES' => array(
        '1'=>'str',
        '2'=>'text',
        '3'=>'html',
        '4'=>'datetime',
        '5'=>'url',
        '6'=>'email',
        '7'=>'bool',
        '8'=>'file',
    ),
    'TYPES_COLUMNS' => array(
        '1'=>'upcharfield',
        '2'=>'uptextfield',
        '3'=>'uptextfield',
        '4'=>'updatetimefield',
        '5'=>'uptextfield',
        '6'=>'upcharfield',
        '7'=>'upcharfield',
        '8'=>'uptextfield',
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
        'file'=>'file',
    ),
    'rulesvalidatedef' => array( //Valid values include 'string', 'integer', 'float', 'array', 'date', 'time' and 'datetime'.
        'str' => '
type
type=>string
',
        'file' => '
file
types=>jpg, gif, png
us_set_patch=>media/files/
us_set_funk_lines_loader=>defloadfunc
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