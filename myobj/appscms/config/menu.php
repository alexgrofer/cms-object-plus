<?php
$menu = array(
    //alias | namemodel | unicueurl | id | parent
    1 => array('users', 'user','/index.php?r=myobj/admin/objects/models/user',1,0), //'type'=>'model',
    2 => array('groups', 'group','/index.php?r=myobj/admin/objects/models/group',2,1),
    3 => array('userpasport', 'userpasport','/index.php?r=myobj/admin/objects/models/userpasport',3,1),
);
require(dirname(__FILE__).'/user/menu.php');