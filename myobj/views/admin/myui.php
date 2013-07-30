<?php
//Set Permission
$config = UCms::getInstance()->config['controlui'];
$array_menu_conf = $config['menu'];

foreach($array_menu_conf as $nameMenu => $arrConf) {
	$namemodel = $arrConf[1];
	$confarray = (strpos($namemodel,'ui--')===false)?$config['objects']['models']:$config['ui'];
	if(strpos($namemodel,'class--')) {
		$confarray = false;
	}

	if(isset($confarray[$namemodel]) && array_key_exists('groups_read', $confarray[$namemodel]) && !(array_intersect(Yii::app()->user->groupsident, $confarray[$namemodel]['groups_read'])))  {
		unset($array_menu_conf[$nameMenu]);
	}
}

function treemen($array_menu_conf,$parent=0) {
	$temlp = '';
	foreach($array_menu_conf as $key => $val) {
		if($val[4]==$parent) {
			$temlp_rec = treemen($array_menu_conf,$val[3]);
			$temlp .= '<li'.(($temlp_rec)?' class="dropdown-submenu"':'').'><a href="'.$val[2].'">'.$val[0].'</a>';
			if($temlp_rec) {
				$temlp .= '<ul class="dropdown-menu">'.$temlp_rec.'</ul>';
			}
			$temlp .= '</li>';
		}
	}

	return $temlp;
}
$listobjsort = treemen($array_menu_conf);

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