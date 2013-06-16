<?php
$models_user = array(
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
