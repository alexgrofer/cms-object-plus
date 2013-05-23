<?php
$menu = array(
    //alias | namemodel | unicueurl | id | parent
    1 => array('users', 'user','/index.php?r=myobj/admin/objects/models/user',1,0), //'type'=>'model',
    2 => array('groups', 'group','/index.php?r=myobj/admin/objects/models/group',2,1),
    3 => array('userpasport', 'userpasport','/index.php?r=myobj/admin/objects/models/userpasport',3,1),
    //dep_store
    4 => array('dep_store', 'depcat','/index.php?r=myobj/admin/objects/models/depcat',4,0),
    5 => array('depcatoption', 'depcatoption','/index.php?r=myobj/admin/objects/models/depcatoption',5,4),
    6 => array('depcatoptionparams', 'depcatoptionparams','/index.php?r=myobj/admin/objects/models/depcatoptionparams',6,5),
    7 => array('graphic_sale', 'graphic_sale','index.php?r=myobj/admin/objects/ui/graphic_sale',7,4),
);