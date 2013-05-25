<?php
$models = array(
    'classes' => array(
        'namemodel' => 'uClasses',
		//дополнительные настройки
        'edit' => null, //все
		//'witch' => array('relattest', 'relattest.relattest2'), task Доделать возможность получить методом
        //task сделать возможность искать по свойствам в одном меню, и по реляциям вопрос как искать так relattest2.name или relattest.relattest2.name
        //с relattest2.name проще искать в таблице
		//'find' => array('id'=>'id', 'prop_test'=>'prep_test', 'relattest.name' => 'relattest.name', 'relattest.relattest2.name'=>'relattest.relattest2.name'),
		//task возможность добавить в сортировка реляции какие имена использовать читать выше
        //'sort' => array('id', 'prop_test', 'relattest.name', 'relattest.relattest2.name'),
		'addcontroller' => '', //task проверить
        'relation' => array('properties', 'classes' => 'association'),
        'cols' => array('id'=>'id','name'=>'name','codename'=>'codename','tablespace'=>'tablespace','objectCount'=>'countObj',
        //'relattest2.id'=>'name123' //task проверить после создания метод который вытаскивает без запросов  добавить relattest.relattest2.name
        ),
        'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
        'order_by' => array('id DESC'), //проверить возможность relattest.relattest2.name ASC, причем добавить в массив
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
        'order_by' => array('id DESC'),
    ),
    //alias
    'uclass' => 'classes',
    
    //USER
    'user' => array('namemodel' => 'User', 'relation' => array('group','userpasport'), 'cols' => array('id'=>'id','login'=>'user name'), 'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
    'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
    'group' => array('namemodel' => 'Ugroup', 'relation' => false, 'cols' => array('id'=>'id','name'=>'name','guid'=>'guid'), 'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812')),
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
    'graphic_sale' => array(
        'controller' => 'admin/dep_store/graphic_sale.php',
        'groups_read' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'), //не отображается в меню
        //'groups_write' => array('CC99CD08-A1BF-461A-B1FE-3182B24D2812'),
    ),
    //headers links example
    /*
    'myObjHeaders' => array('namemodel' => 'myObjHeaders', 'cols' => array('id'=>'ids','name'=>'name')),
    'myObjLines' => array('namemodel' => 'myObjLines', 'relation' => array('property'), 'cols' => array('id'=>'id','uptextfield'=>'textfield','upcharfield'=>'charfield','uptimefield'=>'timefield','updatefield'=>'datefield','upintegerfield'=>'integerfield','upfloatfield'=>'floatfield')),
    'lines' => 'myObjLines', //alias
    'property' => 'properties', //alias
    */
);
