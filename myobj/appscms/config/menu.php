<?php
$menu = array(
	//alias | namemodel | unicueurl | id | parent | sort
	array('users', 'user','/index.php?r=myobj/admin/objects/models/user',1,0,0), //'type'=>'model',
	array('groups', 'group','/index.php?r=myobj/admin/objects/models/group',2,1,0),
	array('userpasport', 'userpasport','/index.php?r=myobj/admin/objects/models/userpasport',3,1,0),
	//storege file
	array('storage files', 'storagef','/index.php?r=myobj/admin/objects/models/storagef',3,0,0),
	//db damps
	array('DB Damps', 'class--db_dump_sys','/index.php?r=myobj/admin/objects/class/db_dump_sys/&usercontroller=userdbdamp',3,0,0),
);
require(dirname(__FILE__).'/user/menu.php');