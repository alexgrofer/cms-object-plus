<?php
$config = Yii::app()->appcms->config['controlui'];
$array_menu_conf = $config['menu'];
$currentidmenu = false;
foreach($array_menu_conf as $key => $arrMenu) {
	if(strpos(Yii::app()->request->url,$arrMenu[2])!==false) {
		$currentidmenu = $key;
	}
}
if($currentidmenu!==false) {

function treetop($menu,$keyfind,$array) {
	if(!isset($menu[$keyfind])) return;
	$topelem = $menu[$keyfind][4];
	$isact = '';
	foreach($menu as $key => $val) {

		if($topelem == $val[4]) {

			$array[$keyfind][] = array($val,($keyfind===$key?true:false));
		}
	}
	treetop($menu,$topelem,&$array);
}
$newarray = array();
treetop($array_menu_conf,$currentidmenu,&$newarray);
$revarsarray = array_reverse($newarray);
$htmlmen = '';
foreach($revarsarray as $arrMenu) {
	$htmlmen .= '<p>';
	foreach($arrMenu as $confmen) {
		$htmlmen .= '<a class="btn btn-small'.($confmen[1]?' btn-primary':'').'" href="'.$confmen[0][2].'">'.$confmen[0][0].'</a> ';
	}
	$htmlmen .= '</p>';
}

echo '<div class="well">'.$htmlmen.'</div>';

}
?>
