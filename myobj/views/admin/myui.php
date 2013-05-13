<?php
$array_menu_conf = array( //unicue url
    'users' => array('user','/index.php?r=myobj/admin/objects/models/user'), //'type'=>'model',
    'groups' => array('group','/index.php?r=myobj/admin/objects/models/group'),
    'userpasport' => array('userpasport','/index.php?r=myobj/admin/objects/models/userpasport'),
    //dep_store
    'dep_store' => array('depcat','/index.php?r=myobj/admin/objects/models/depcat'),
    'depcatoption' => array('depcatoption','/index.php?r=myobj/admin/objects/models/depcatoption'),
    'depcatoptionparams' => array('depcatoptionparams','/index.php?r=myobj/admin/objects/models/depcatoptionparams'),
    'graphic_sale' => array('graphic_sale','index.php?r=myobj/admin/objects/ui/graphic_sale'),
);
$array_menu_sub = array(
    'users' => array(
        'groups','userpasport',
    ),
    'dep_store' => array(
        'depcatoption'=>array('depcatoptionparams'),'graphic_sale',
    ),
);
//сделать подменю

//Set Permission
$config = UCms::getInstance()->config['controlui'];

foreach($array_menu_conf as $nameMenu => $arrConf) {
    $namemodel = $arrConf[0];
    $url = $arrConf[0];
    $confarray = (strpos($url,'/ui/')===false)?$config['objects']['models']:$config['ui'];

    if((array_key_exists('groups_read', $confarray[$namemodel]) && !(array_intersect(Yii::app()->user->groupsident, $confarray[$namemodel]['groups_read']))))  {
        unset($array_menu_conf[$nameMenu]);
    }

}

function treemen($arraymen, $array_menu_conf) {
    $temlp = '';
    foreach($arraymen as $key => $val) {
        $nameval = (is_array($val))?$key:$val;
        if(!isset($array_menu_conf[$nameval])) continue;
        $temlp .= '<li'.((is_array($val))?' class="dropdown-submenu"':'').'><a href="'.$array_menu_conf[$nameval][1].'">'.$nameval.'</a>'."\n";
        if(is_array($val)) {
            $temlp .="\t".'<ul class="dropdown-menu">'."\n";
            $temlp .= treemen($val,$array_menu_conf);
            $temlp .= "\t".'</ul>'."\n";
        }

        $temlp .= '</li>'."\n";
    }
    return $temlp;
}
$listobjsort = treemen($array_menu_sub, $array_menu_conf);

echo '
<div class="btn-group">
    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
        UI
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
';
echo $listobjsort;
echo '</ul></div>';
?>