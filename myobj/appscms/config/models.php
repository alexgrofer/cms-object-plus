<?php
$models = array(
    'classes' => array(
        'namemodel' => 'uClasses',
		//дополнительные настройки
        'edit' => null, //все
		//'witch' => null, //можно описать полностью включая select, condition, тип join
		'witch' => array('relattest', 'relattest.relattest2'),
		'find' => array('id', 'prop_m_o__test', 'relattest.name', 'relattest.relattest2.name'), //параметр, свойство, реляц модели relattest параметра  name,
		//свойство искать по 'prop_m_o__', реляционную таблицу по точке последнии два слова
		'sort' => array('id', 'prop_test', 'relattest.name', 'relattest.relattest2.name'),
		'addcontroller' => '', //дополнительный контроллер если необходим - может быть необходим при редактировании
        'relation' => array('properties', 'classes' => 'association'),
        'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','tablespace'=>'tablespace','objectCount'=>'countObj'), // добавить relattest.relattest2.name
        'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'order_by' => array(array('id', 'desc')), //проверить возможность relattest.relattest2.name ASC, причем добавить в массив
    ),
    'properties' => array(
        'namemodel' => 'objProperties',
        'edit' => null,
        'relation' => array('classes'),
        'selfobjrelationElements' => array('classes'=>array('test123',)),
        //'selfobjrelationElements' => array('classes'=>array('namecol','namecol2')),
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
    //STORE
    'depcat' => array(
        'namemodel' => 'DepCatCategory', 'relation' => array('depcatoption'), 'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    'depcatoption' => array(
        'namemodel' => 'DepCatOption', 'relation' => array('depcatoptionparams'), 'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    'depcatoptionparams' => array(
        'namemodel' => 'DepCatOptionParam', 'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    
    //headers links example
    /*
    'myObjHeaders' => array('namemodel' => 'myObjHeaders', 'cols' => array('id'=>'ids','name'=>'name')),
    'myObjLines' => array('namemodel' => 'myObjLines', 'relation' => array('property'), 'cols' => array('id'=>'id','uptextfield'=>'textfield','upcharfield'=>'charfield','uptimefield'=>'timefield','updatefield'=>'datefield','upintegerfield'=>'integerfield','upfloatfield'=>'floatfield')),
    'lines' => 'myObjLines', //alias
    'property' => 'properties', //alias
    */
);

$models_menu = array(
        array('user'),
        array('group'),
        array('userpasport'),
        //STORE
        array('depcat'),
);
