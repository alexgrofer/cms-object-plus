<style>
.pagination input {display:none}
</style>
<?php
echo '<h4>'.get_class($REND_model).'</h4>';
if($REND_model instanceof AbsBaseHeaders) {
	echo '<h5>class header: '.$REND_model->uclass->codename.'</h5>';
}

if(!$REND_acces_read) {
	echo '<p class="alert">not acces read</p>';
	return;
}

$fix_criteria = clone $REND_model->getDbCriteria();
$new_criteria = new CDbCriteria;
$new_criteria->mergeWith($fix_criteria, 'AND');

$nextCond=null;
if(array_key_exists('serach_param',$_POST)) {
	$index_find = 0;
	$count_pf = 0;
	foreach($_POST['serach_param'] as $val_param_prop) {
		if($val_param_prop) $count_pf++;
	}
	foreach($_POST['serach_param'] as $key => $value) {
		if($value!='') {
			$index_find++;
			$tableAlias = '';
			$valueSearchElem = $_POST['filter_param'][$key];
			$typecond = (array_key_exists('serach_cond',$_POST) && isset($_POST['serach_cond'][$key]))?'OR':'AND';
			$serach_hooks_left = (array_key_exists('serach_hooks_left',$_POST) && isset($_POST['serach_hooks_left'][$key]))?true:false;
			$serach_hooks_right = (array_key_exists('serach_hooks_right',$_POST) && isset($_POST['serach_hooks_right'][$key]))?true:false;
			//если параметр модели указан без псевдонима таблицы берем по умолчанию первичный
			if(strpos($valueSearchElem,'.')===false) {
				$tableAlias = $REND_model->tableAlias.'.';
			}

			if(!$nextCond) {
				$typecond_r = 'AND';
			}
			else {
				$typecond_r = $nextCond;
			}
			$nextCond = $typecond;

			if($serach_hooks_left===true || $index_find==1) {
				$criteria_find = new CDbCriteria();
			}

			//для свойств true
			if(($pos_prop = strpos($valueSearchElem,'__prop'))!==false) {

				$valueSearchElem = substr($valueSearchElem,0,$pos_prop);
				$tmp_criteria = $REND_model->getPropCriteria('condition', $valueSearchElem, $valueSearchElem.' '.$_POST['serach_condition'][$key].' :param_s_prop_'.$key);
				$criteria_find->mergeWith($tmp_criteria, $typecond_r);
				$criteria_find->params[':param_s_prop_'.$key] = $_POST['serach_param'][$key];

				if($index_find>=$count_pf && !$new_criteria->condition) {
					$typecond_r = 'AND';
				}

			}
			//для обычных параметров модели false
			else {
				$criteria_find->addCondition($REND_model->tableAlias.'.'.$valueSearchElem.' '.$_POST['serach_condition'][$key].' :param_s_'.$key, $typecond_r);
				$criteria_find->params[':param_s_'.$key] = $_POST['serach_param'][$key];
			}

			if($serach_hooks_right===true || $index_find>=$count_pf) {
				$new_criteria->mergeWith($criteria_find, $typecond_r);
			}
		}
	}
}
//sort
if(isset($_POST['order_by']) && $_POST['order_by']!='0') {
	$order_array = $_POST['order_by'];
}
elseif($REND_order_by_def) {
	$order_array = str_replace(' ', '---',$REND_order_by_def[0]);
}

if(isset($order_array)) {
	$name_order_explode = explode('---',$order_array);
	if(($pos_prop = strpos($name_order_explode[0],'__prop'))!==false) {
		$orderCriteria = $REND_model->getPropCriteria('order', substr($name_order_explode[0],0,$pos_prop), $name_order_explode[1]);

		$new_criteria->mergeWith($orderCriteria);
	}
	else {
		$new_criteria->order = $REND_model->tableAlias.'.'.$name_order_explode[0].' '.$name_order_explode[1];
	}
	unset($name_order_explode,$pos_prop);
}
unset($order_array);
//sort
$COUNT_P = $REND_model->count($new_criteria);

$arrchecked = $REND_selectedarr;

$htmldiv = '<div>%s</div>';
$htmldiv_class = '<div class="%s">%s</div>';
$htmlp='<p class="%s">%s</p>';
$htmlspan='<span class="%s">%s</span>';
$htmlinput='<input type="%s" name="%s" value="%s" class="%s" />';

$relationLinkOnly='';
if($this->dicturls['action']=='relationobjonly') {
	$relationLinkOnly = $this->dicturls['paramslist'][3].'/'.$this->dicturls['paramslist'][4].'/relation_name/'.$this->dicturls['paramslist'][7];
}
$arrayuirow = array(
	'edit' => 'objects/models/'.$this->dicturls['paramslist'][1].'/action/edit/',
	'remove' => 'objects/models/'.$this->dicturls['paramslist'][1].'/action/remove/',
	'objects' => '',
	'links' => '',
	'editlinksclass' => '',
	'new' => 'objects/models/'.$this->dicturls['paramslist'][1].'/action/edit/0/'.$relationLinkOnly,
);
$headershtml = '';

$headershtml = implode('</td><td>',array_values($REND_thisparamsui));
if($REND_thispropsui) {
	$headershtml .= '<td>'.(implode('</td><td>',array_values($REND_thispropsui)));
}
if($this->dicturls['class']=='objects' && $this->dicturls['paramslist'][0]=='models' && ($this->dicturls['paramslist'][1]=='classes')) {
	$arrayuirow['objects'] = 'objects/class/';
	if($this->dicturls['paramslist'][3]=='links') {
		$arrayuirow['editlinksclass'] = 'objects/class/';
	}
}

elseif($this->dicturls['class']=='objects') {
	$arrayuirow['edit'] = $this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/';
	$arrayuirow['new'] = $this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/0/'.$relationLinkOnly;
	$arrayuirow['links'] = 'objects/models/classes/'.$this->dicturls['paramslist'][1].'/links/';
	$arrayuirow['remove'] = $this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/remove/';
}
?>
<form method="post" enctype="multipart/form-data">
<?php
$selectorsids = array();
$selectorsids_excluded = array();
$COUNTVIEWELEMS = Yii::app()->appcms->config['countelements'];
$COUNTVIEWPAGES = Yii::app()->appcms->config['countpage'];
$idpage = 0;
if(strpos(implode('',array_keys($_POST)),'goin_')!==false) {
	foreach($_POST as $key => $value) {
		if(strpos($key, 'goin_')!==false) {
			$idpage = substr($key,5);
			break;
		}
	}
}
elseif(array_key_exists('idpage',$_POST)) $idpage = $_POST['idpage'];

if(array_key_exists('selectorsids',$_POST) && $_POST['selectorsids']!='') $selectorsids = explode(',',$_POST['selectorsids']);

if($idpage==1) $idpage=0;
elseif($idpage!=0) $idpage -= 1;
if($COUNT_P > $COUNTVIEWELEMS) {
	$new_criteria->limit = $COUNTVIEWELEMS;
	$new_criteria->offset = $COUNTVIEWELEMS * $idpage;
}

$listall = $REND_model->findAll($new_criteria);

if(array_key_exists('selectorsids_excluded',$_POST) && $_POST['selectorsids_excluded']!='') {
	$selectorsids_excluded = explode(',',$_POST['selectorsids_excluded']);
}
if(array_key_exists('checkedaction',$_POST) || strpos(implode('',array_keys($_POST)),'goin_')!==false) {
	foreach(explode(',',$_POST['pkeys_all']) as $id) {
		if(in_array('elemch_'.$id,array_keys($_POST))!==false) {
			if(in_array($id,$arrchecked)===false) {
				$selectorsids[] = $id;
			}
			elseif(in_array($id,$selectorsids_excluded)!==false) {
				unset($selectorsids_excluded[array_search($id,$selectorsids_excluded)]);
			}
		}
		else {
			if(in_array($id,$selectorsids)!==false) {
				unset($selectorsids[array_search($id,$selectorsids)]);
			}
			elseif(in_array($id,$arrchecked)!==false) {
				$selectorsids_excluded[] = $id;
			}
		}
	}
	$selectorsids = array_unique($selectorsids);
	$selectorsids_excluded = array_unique($selectorsids_excluded);

}

printf($htmlinput,'hidden','idpage',($idpage+1),'');
printf($htmlinput,'hidden','selectorsids',implode(',',$selectorsids),'');
printf($htmlinput,'hidden','selectorsids_excluded',implode(',',$selectorsids_excluded),'');

?>
<a  class="btn" href="<?=$this->createAdminUrl($arrayuirow['new'])?>">new object</a>
<p>
<input name="exportcsv" type="file" /><input name="exportcsv_ispk" type="checkbox" />resave_pk создаст с таким id
<input onclick="return confirm(\'export CSV file\')" class="btn btn-danger" type="submit" value="export" />
</p>
<?php
if($this->dicturls['action']=='' && (count($selectorsids) || count($selectorsids_excluded))) {
	echo ' | ---------- <input onclick="return confirm(\'remove selected objects\')" class="btn btn-danger" name="checkdelete" type="submit" value="delete">';
	echo ' | ---------- <input onclick="return confirm(\'import CSV selected objects\')" class="btn btn-danger" name="importcsv" type="submit" value="CSV">';
}
if($this->dicturls['action']!='') {
	if($arrchecked) {
		echo '<div><code class="headermen">before saved: '.implode(',',$arrchecked).'</code></div>';
	}
	$html = '<code class="headermen cgreen">action: <b>'.$this->dicturls['action'].'</b></code> <input onclick="return confirm(\'save ?\')" class="btn btn-mini btn-danger" type="submit" name="saveaction" value="save" />';
	echo $html;
}
?>
<?php
if(count($selectorsids) || count($selectorsids_excluded)) {
	$html = '';
	if(count($selectorsids)) $html .= sprintf($htmlspan,'', 'selected: ['.implode(',',$selectorsids).']');
	if(count($selectorsids_excluded)) $html .= sprintf($htmlspan, 'backgroundred', 'unselected in: ['.implode(',',$selectorsids_excluded).']');
	echo '<div><code class="headermen cred">'.$html.'</code></div>';
}
$select_params_model = array();
foreach($REND_find as $val) {
	$select_params_model[$val] = $val;
}
?>
<div>

<div class="row-fluid">
<div class="span10">


<?php
$n_p=0;
do {
$n_p++;
if((isset($_POST['serach_param']) && isset($_POST['serach_param'][$n_p]) && trim($_POST['serach_param'][$n_p])=='') || (isset($_POST['serach_param']) && !isset($_POST['serach_param'][$n_p])) && $n_p != max(array_keys($_POST['serach_param']))+1) {
	continue;
}
?>
<div class="form-inline">
<input name="serach_hooks_left[<?=$n_p?>]" id="<?=$n_p?>_leftcl" type="checkbox" <?=((isset($_POST['serach_hooks_left']) && isset($_POST['serach_hooks_left'][$n_p]))?'checked="checked"':'');?> />
<?php
echo '<label for="'.$n_p.'_leftcl">(</label>&nbsp;'.CHtml::dropDownList('filter_param['.$n_p.']', ((isset($_POST['filter_param']) && isset($_POST['filter_param'][$n_p]))?$_POST['filter_param'][$n_p]:''),$select_params_model);
?>&nbsp;
<input  class="input-mini" name="serach_condition[<?=$n_p?>]" type="text" value="<?=((isset($_POST['serach_condition']) && isset($_POST['serach_condition'][$n_p]))?$_POST['serach_condition'][$n_p]:'=')
;?>" />
&nbsp;<input name="serach_param[<?=$n_p?>]" type="text" value="<?=((isset($_POST['serach_param']) && isset($_POST['serach_param'][$n_p]))?$_POST['serach_param'][$n_p]:'');?>" />
<input name="serach_hooks_right[<?=$n_p?>]" id="<?=$n_p?>_rightcl"  type="checkbox" <?=((isset($_POST['serach_hooks_right']) && isset($_POST['serach_hooks_right'][$n_p]))?'checked="checked"':'');?> />
	<label for="<?=$n_p?>_rightcl">)&nbsp;</label>|----------|&nbsp;<input name="serach_cond[<?=$n_p?>]" id="<?=$n_p?>_orlab"  type="checkbox" <?=((isset($_POST['serach_cond']) && isset($_POST['serach_cond'][$n_p]))?'checked="checked"':'');?> />&nbsp;<label for="<?=$n_p?>_orlab">OR</label>
</div>
<?php
}
while(isset($_POST['serach_param']) && $n_p <= max(array_keys($_POST['serach_param'])));
unset($select_array);
?>
<input class="btn"  type="submit" value="find">

</div>
<div class="span2">
<?php
//sort
if(count($REND_order_by)) {
$select_order_by = array('0'=>'');
foreach($REND_order_by as $value) {
	$select_order_by[$value.'---asc'] = $value;
	$select_order_by[$value.'---desc'] = $value.'-desc';
}

echo CHtml::dropDownList('order_by', ((isset($_POST['order_by']) && isset($_POST['order_by']))?$_POST['order_by']:''),$select_order_by);
echo '<input class="btn"  type="submit" value="sort">';
}
//sort
?>
</div>
</div>
<?php
$pkeys_all = array();
if($COUNT_P) {
?>
<table class="table table-striped table-bordered table-condensed table-hover">
<tr class="success">
<td><input class="btn btn-mini" name="allsetchecked" type="submit" value="s"> <input class="btn btn-mini btn-danger" name="checkedaction" type="submit" value="action check" /></td><td><?=$headershtml?></td><td>ui</td>
</tr>
<?php
if($REND_relation) {
	$relations_links_model = '';
	foreach($REND_model->relations() as $namerelat => $val) {
		if(isset($REND_relation[$namerelat])) {
			$namemodelconf = $REND_relation[$namerelat];

			//это ссылка на модель или класс
			$modelORclass = (isset($REND_relation[$namerelat][2]) && $REND_relation[$namerelat][2])?'class':'models';

			$print_link = ' | <a href="'.$this->createAdminUrl('objects/'.$modelORclass.'/'.$namemodelconf[0].'/action/%s/IDELEMENT/add/relation_name/'.$namemodelconf[1]).'">%s</a>';
			$relations_links_model .= sprintf($print_link,'relationobj',$namerelat);
			if($REND_model::HAS_MANY) $relations_links_model .= sprintf($print_link,'relationobjonly','<');
			unset($print_link);
		}
	}
}

foreach($listall as $obj) {
	$strchecked=(in_array($obj->primaryKey, $selectorsids)!==false || (in_array($obj->primaryKey,$arrchecked)!==false && in_array($obj->primaryKey,$selectorsids_excluded)===false) || array_key_exists('allsetchecked',$_POST))?'checked="checked"':'';

	$pkeys_all[] = $obj->primaryKey;

	echo '<tr><td><input name="elemch_'.$obj->primaryKey.'" type="checkbox" '.$strchecked.' /></td>';
	foreach(array_keys($REND_thisparamsui) as $colname) {
		$valobj = '';
		if(strpos($colname, '.')!==false) {
			$arrRlname=explode('.',$colname);
			$propRelat = array_pop($arrRlname);
			$valobj = $obj;
			foreach($arrRlname as $valRel) {
				if($valobj) $valobj = $valobj->$valRel;
			}
			if($valobj) $valobj = $valobj->$propRelat;
		}
		else {
			$valobj = $obj->$colname;
		}

		echo '<td>'.$valobj.'</td>';
	}
	if($REND_thispropsui) {
		foreach(array_keys($REND_thispropsui) as $colname) {
			echo '<td>'.$obj->uProperties[$colname].'</td>';
		}
	}
	$uihtml = '';
	if($arrayuirow['edit']) $uihtml .= ' <a href="'.$this->createAdminUrl($arrayuirow['edit'].$obj->primaryKey.'/'.(($relationLinkOnly && $strchecked)?$relationLinkOnly:'')).'"><i class="icon-edit"></i></a>';

	if($arrayuirow['objects']) $uihtml .= ' | <a href="'.$this->createAdminUrl($arrayuirow['objects'].$obj->primaryKey.'/action/edit/0/').'">
	<i class="icon-plus-sign"></i></a> | <a href="'.$this->createAdminUrl($arrayuirow['objects'].$obj->codename).'">objects</a>';
	if($arrayuirow['links'] && $this->dicturls['paramslist'][0]=='class') {
		$uihtml .= ' | <a href="'.$this->createAdminUrl($arrayuirow['links'].$obj->primaryKey).'">links</a>';
		$uihtml .= ' | <a href="'.$this->createAdminUrl($arrayuirow['links'].$obj->primaryKey, array('is_bask_link'=>1)).'">back links</a>';
	}

	if($arrayuirow['editlinksclass']) {
		$uihtml .= ' | link types: ';
		if(Yii::app()->request->getParam('is_bask_link')) {
			$uihtml .= ' <b>BACK</b> ';
		}
		$addParamLinkBack = Yii::app()->request->getParam('is_bask_link')==false?array():array('is_bask_link'=>1);
		$templatLinkType = '<a href="'.$this->createAdminUrl($arrayuirow['editlinksclass'].$obj->codename.'/action/lenksobjedit/'.$this->dicturls['paramslist'][4].'/class/'.$this->dicturls['paramslist'][2], array_merge(array('type_link'=>'LINK_TYPE'),$addParamLinkBack)).'">LINK_TYPE</a>';

		$classConfLink = uClasses::getclass($this->dicturls['paramslist'][2]);
		if(Yii::app()->request->getParam('is_bask_link')) {
			$classConfLink = $obj;
		}
		foreach(array_keys($classConfLink->getNamesModelLinks()) as $typeName) {
			$uihtml .= ' - '.str_replace('LINK_TYPE', $typeName, $templatLinkType);
		}
	}


	if(isset($relations_links_model)) {
		$uihtml .= ' | ---relation <i class="icon-arrow-right"></i>'.str_replace('IDELEMENT',$obj->primaryKey,$relations_links_model);
	}

	$uihtml .= ' | --- <a onclick="return confirm(\'remove pk - '.$obj->primaryKey.'\')" href="'.$this->createAdminUrl($arrayuirow['remove'].$obj->primaryKey).'"><i class="icon-remove"></i></a>';
	echo '<td>'.$uihtml.' </td></tr>';
}

?>
</table></div>

<?php
if($COUNT_P>$COUNTVIEWELEMS) {
/*
$tamplate = array(
		'action'=>' class="active"',
		'nextleft'=>'<li><a href="'.Yii::app()->createUrl($this->route,array('goin_'=>'')).'%s">&laquo;</a></li>',
		'prevpg'=>'<li class="previous"><a id="prevpg" href="'.Yii::app()->createUrl($this->route,array('goin_'=>'')).'%s">&larr; Назад</a></li>',
		'nextpg'=>'<li class="next"><a id="nextpg" href="'.Yii::app()->createUrl($this->route,array('goin_'=>'')).'%s">Вперед &rarr;</a></li>',
		'nextright'=>'<li><a href="'.Yii::app()->createUrl($this->route,array('goin_'=>'')).'%s">&raquo;</a></li>',
		'elem'=>'<li%s><a href="'.Yii::app()->createUrl($this->route,array('goin_'=>'')).'%s">%s</a></li>',
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

$tamplate = array(
		'action'=>' class="active"',
		'in_start'=>'<li><input type="submit" name="goin_T_ID_PAGE" value="true" /><a href="#">&laquo;</a></li>',
		'in_left'=>'<li class="previous"><input type="submit" name="goin_T_ID_PAGE" value="true" /><a id="prevpg" href="#">Ctrl &larr;</a></li>',
		'in_right'=>'<li class="next"><input type="submit" name="goin_T_ID_PAGE" value="true" /><a id="nextpg" href="#">Ctrl &rarr;</a></li>',
		'in_end'=>'<li><input type="submit" name="goin_T_ID_PAGE" value="true" /><a href="#">&raquo;</a></li>',
		'elements'=>'<li%s><input type="submit" name="goin_T_ID_PAGE" value="true" /><a href="#">T_ID_PAGE</a>

		</li>',
		'pagination' => '
		<div id="pagination" class="pagination">

			<ul class="pager">
				{{IN_LEFT}}{{IN_RIGHT}}
			</ul>
			<p class="pagin-lenks"><ul>
			{{IN_START}}
			{{ELEMENTS}}
			{{IN_END}}
			</ul></p>
		</div>
<script>
$("#pagination a").on("click", function(){
	$(this).prev()[0].click();
	return false;
});
$(document).keydown(function(event){if(event.ctrlKey){if(event.keyCode == 37){if($("#prevpg").length){$("#prevpg")[0].click()}}else if(event.keyCode == 39){if($("#nextpg").length){$("#nextpg")[0].click()}}}});
</script>
');
echo '<div style="padding-bottom: 60px">'.MYOBJ\appscms\core\base\SysUtils::pagination($idpage,$COUNT_P,$COUNTVIEWELEMS,$COUNTVIEWPAGES,'',true,$tamplate, 3).'</div>';
}
?>
<input name="pkeys_all" type="hidden" value="<?=implode(',',$pkeys_all);?>" />
<?php
}
else echo '<div class="well">none objects</div>';
?>
</form>