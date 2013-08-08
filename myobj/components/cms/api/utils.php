<?php
namespace apicms\utils;

function URender($idtmplhandl,$listtshihandles) {
	if(!array_key_exists($idtmplhandl, $listtshihandles)) {
		return '';
	}
	static $groups_current_handle = array();
	if(!array_key_exists($idtmplhandl,$groups_current_handle)) {
		$groups_current_handle[$idtmplhandl] = $listtshihandles[$idtmplhandl]['objview']->getobjlinks('groups_sys')->findAll();
	}

	$onview = false;
	if(count($groups_current_handle[$idtmplhandl])) {
		$group_handle_ids_top_groups = array();
		$group_handle_names = array();
		foreach($groups_current_handle[$idtmplhandl] as $objgroup) {
			$group_handle_ids_top_groups[] = $objgroup->vp1;
		}

		$patchview= $listtshihandles[$idtmplhandl]['patchview'];

		if(\Yii::app()->user->isGuest && in_array('guestsys', $group_handle_ids_top_groups)) {
			$onview = true;
		}
		elseif(!\Yii::app()->user->isGuest && in_array('authorizedsys', $group_handle_ids_top_groups)) {
			$onview = true;
		}
		elseif(!\Yii::app()->user->isGuest) {
			$groupsuser = \Yii::app()->user->groupsident;
			foreach($group_handle_ids_top_groups as $idsystemgroup) {
				if(in_array($idsystemgroup,$groupsuser)) {
					$onview = true;
					break;
				}
			}

		}
	}
	if($onview) {
		return $patchview;
	}
	else '';
}
function arrvaluesmodel($listobjects, $multinames) {
	$arrv = array();
	foreach($listobjects as $object) {
		if(is_array($multinames)) {
			$arrvsav = array();
			foreach($multinames as $name) {
				$arrvsav[] = $object->$name;
			}
			$arrv[] = $arrvsav;
		}
		else $arrv[] = $object->$multinames;
	}
	return $arrv;
}
function action_job($nameaction,$this_id,$listset=array(),$listsetexcluded=array(),$paramslist,$params_extra) {
	switch($nameaction) {
		case 'addclass':
			$thisclass = \uClasses::getclass($this_id);
			if(count($listset)) {
				$thisclass->UserRelated->links_edit('add','properties',$listset);
			}
			if(count($listsetexcluded)) {
				$thisclass->UserRelated->links_edit('remove','properties',$listsetexcluded);
			}
			break;
		case 'lenksobjedit':
			$ObjHeader = \uClasses::getclass($paramslist[6])->objects()->findByPk($this_id);
			if(count($listset)) {
				$ObjHeader->editlinks('add',$paramslist[1],$listset);
			}
			if(count($listsetexcluded)) {
				$ObjHeader->editlinks('remove',$paramslist[1],$listsetexcluded);
			}
			break;
		case 'relationobj':
			$typerelat = $paramslist[5];
			$fk = $paramslist[4];
			$params_modelget = \Yii::app()->appcms->config['controlui']['objects']['models'][$paramslist[7]];
			//is alias
			if(!is_array($params_modelget)) {
				$namealias = $params_modelget;
				$params_modelget = \Yii::app()->appcms->config['controlui']['objects']['models'][$namealias];unset($namealias);
			}
			$NAMEMODEL_get = $params_modelget['namemodel'];
			$obj = $NAMEMODEL_get::model()->findByPk($this_id);
			if(count($listset)) {
				if($typerelat=='set') $listset = $listset[0];
				$obj->UserRelated->links_edit($typerelat,$params_extra['name_model'],$listset,$fk);
			}
			if(count($listsetexcluded)) {
				if($typerelat!='set') {
					$obj->UserRelated->links_edit('remove',$params_extra['name_model'],$listsetexcluded,$fk);
				}
			}
	}
}
/*
$idpage = 0;
if(strpos(implode('',array_keys($_GET)),'goin_')!==false) {
	foreach($_GET as $key => $value) {
		if($key == 'goin_') {
			$idpage = $value;
		}
	}
}
//normal url
$tamplate = array(
		'action'=>' class="active"',
		'nextleft'=>'<li><a href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">&laquo;</a></li>',
		'prevpg'=>'<li class="previous"><a id="prevpg" href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">&larr; �����</a></li>',
		'nextpg'=>'<li class="next"><a id="nextpg" href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">������ &rarr;</a></li>',
		'nextright'=>'<li><a href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">&raquo;</a></li>',
		'elem'=>'<li%s><a href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">%s</a></li>',
		'pagination' => '
		<div id="pagination" class="pagination">
			<ul class="pager">
				%s
			</ul>
			<p class="pagin-lenks"><ul>%s</ul></p>
		</div>
<script>
$(document).keydown(function(event){if(event.ctrlKey){if(event.keyCode == 37){if($("#prevpg").length){$("#prevpg")[0].click()}}else if(event.keyCode == 39){if($("#nextpg").length){$("#nextpg")[0].click()}}}});
</script>
');
*/
function pagination($indexpage,$countlinks,$count_elems,$count_pages,$urlp,$flagpro,$tamplate,$countleft=5) {
	$countlinks = (int)(ceil($countlinks / (float)($count_elems)));
	$linksp = ''; $linkspt = '';
	$finc = $indexpage % $count_pages;
	$idnext = $indexpage + 2;
	$idprev = $indexpage;
	$idrightnext = $indexpage + $count_pages + 1 - $finc;
	$idleftnext = $indexpage - $finc;

	if($finc == 0) $startslice = $indexpage;
	else		   $startslice = $indexpage - $finc;
	if($flagpro==true && $indexpage > $countleft) $startslice = $indexpage - $countleft;

	$rangenorm = array_slice(range(0,($countlinks - 1)),$startslice, $count_pages);

	if($startslice > 0)
		$linksp .= sprintf($tamplate['nextleft'], $urlp.$idleftnext);

	if($indexpage != 0)
		$linkspt .=  sprintf($tamplate['prevpg'], $urlp.$idprev);

	foreach($rangenorm as $i) {
		$actclassl = '';
		if($indexpage == $i) $actclassl = $tamplate['action'];

		$idthisutl = ($i + 1);

		$linksp .= sprintf($tamplate['elem'], $actclassl, $idthisutl, $idthisutl);
	}

	if($indexpage != $countlinks - 1)
		$linkspt .= sprintf($tamplate['nextpg'], $urlp.$idnext);
	if($idthisutl < $countlinks)
		$linksp .= sprintf($tamplate['nextright'], $urlp.$idrightnext);


	$pagination = sprintf($tamplate['pagination'], $linkspt, $linksp);
	return $pagination;
}
function treelem($elems,$namekeyid,$namekeyparent,$parent=0,$padd='---',$leftp='',$nlist=array(),$objisarray=true) {
	$parenttmp='';
	foreach($elems as $objt) {
		if($objt[$namekeyparent] == $parent) {
			if($parent != 0 && $parenttmp != $parent) {
				$leftp .= $padd;
			}
			array_push($nlist, array($objt,$leftp));
			$parenttmp = $parent;
			$idtop = ($objisarray)?$objt[$namekeyid]:$objt->$namekeyid;
			$nlist = treelem($elems,$namekeyid,$namekeyparent,$idtop,$padd,$leftp,$nlist);
		}
	}
	return $nlist;
}
function GUID()
{
	if (function_exists('com_create_guid') === true)
	{
		return trim(com_create_guid(), '{}');
	}

	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}
function importRecursName($alias,$mask=false,$forceInclude=false,$isreturn=false) {
	$path=\Yii::getPathOfAlias($alias);
	$path_find=$path.DIRECTORY_SEPARATOR.$mask;
	$func = function($normal_name) use($forceInclude) {\Yii::import($normal_name,$forceInclude);};
	$array = ($mask)?glob($path_find):array($alias);
	foreach ($array as $filename) {
		$normal_name = substr($filename,0,strrpos($filename, '.'));
		if($isreturn) return require($filename);
		$func($normal_name);
	}
}