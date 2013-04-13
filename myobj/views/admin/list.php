<style>
.pagination input {display:none}
</style>
<?php
if(array_key_exists('serach_param',$_POST)) {
    foreach($_POST['serach_param'] as $key => $value) {
        if($value!='') {
            $REND_model->dbCriteria->addCondition(($REND_model->tableAlias.'.'.$_POST['filter_param'][$key].' '.$_POST['serach_condition'][$key]).' :serach_condition_'.$key);
            $REND_model->dbCriteria->params[':serach_condition_'.$key] = $_POST['serach_param'][$key];
        }
    }
}

if(array_key_exists('serach_prop',$_POST) && trim($_POST['serach_prop'])!='') {
    //serach_prop_condition
    $array_search = array($_POST['filter_prop'], $_POST['serach_prop_condition'],"'".$_POST['serach_prop']."'");
    $REND_model = $REND_model->setuiprop(array('condition' => array($array_search)));
    unset($array_search);
}

if(array_key_exists('order_by_prop',$_POST) && $_POST['order_by_prop']!='-') {
    $filterprop = explode('---',$_POST['order_by_prop']);
    $REND_model->setuiprop(array('order'=>array($filterprop)));
}
else {
    if($REND_order_by_param) {
        $filterprop = $REND_order_by_param;
    }
    if(array_key_exists('order_by_param',$_POST) && $_POST['order_by_param']!='-') {
        $filterprop = array(explode('---',$_POST['order_by_param']));
    }
    if(isset($filterprop) && $this->dicturls['paramslist'][0]!='class') {
        $REND_model->dbCriteria->order = implode(' ',$filterprop[0]);
    }
    else {
        if(isset($filterprop)) {
        $REND_model->order_cols_model($filterprop);
        }
    }
}

$modelCRITERIA = $REND_model->dbCriteria;


$COUNT_P = $REND_model->count();

$arrchecked = $REND_selectedarr;

$htmldiv = '<div>%s</div>';
$htmldiv_class = '<div class="%s">%s</div>';
$htmlp='<p class="%s">%s</p>';
$htmlspan='<span class="%s">%s</span>';
$htmlinput='<input type="%s" name="%s" value="%s" class="%s" />';

$urladmclass = $this->dicturls['admin'];
$arrayuirow = array(
    'edit' => $urladmclass.'/objects/models/'.$this->dicturls['paramslist'][1].'/action/edit/',
    'remove' => $urladmclass.'/'.$this->dicturls['class'].'/action/remove/',
    'objects' => '',
    'links' => '',
    'editlinksclass' => '',
    'navgroup' => '',
    'new' => $urladmclass.'/objects/models/'.$this->dicturls['paramslist'][1].'/action/edit/0/',
);
$headershtml = '';

$headershtml = implode('</td><td>',array_values($REND_thisparamsui));
if($REND_thispropsui) {
    $headershtml .= '<td>'.(implode('</td><td>',array_values($REND_thispropsui)));
}
if($this->dicturls['class']=='objects' && $this->dicturls['paramslist'][0]=='models' && ($this->dicturls['paramslist'][1]=='classes')) {
    $arrayuirow['objects'] = $urladmclass.'/objects/class/';
    if($this->dicturls['paramslist'][3]=='links') {
        $arrayuirow['editlinksclass'] = $urladmclass.'/objects/class/';
    }
}

elseif($this->dicturls['class']=='objects') {
    $arrayuirow['edit'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/';
    $arrayuirow['new'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/0/';
    $arrayuirow['links'] = $urladmclass.'/objects/models/classes/'.$this->dicturls['paramslist'][1].'/links/';
    $arrayuirow['remove'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/remove/';
    //groups views
    $objclass = uClasses::getclass(array('views_sys','groups_sys'));
    if($objclass['views_sys']->id==$this->dicturls['paramslist'][1]) {
        $arrayuirow['navgroup'] = $urladmclass.'/objects/class/'.$objclass['groups_sys']->id.'/action/lenksobjedit/%s/class/'.$objclass['views_sys']->id;
    }
    
    if($this->dicturls['action']=='lenksobjedit') {
        $arrayuirow['links'] = $urladmclass.'/objects/models/classes/'.$this->dicturls['paramslist'][6].'/links/';
    }
}
?>
<form method="post">
<?php
$selectorsids = array();
$selectorsids_excluded = array();
$COUNTVIEWELEMS = $this->apcms->config['countelements'];
$COUNTVIEWPAGES = $this->apcms->config['countpage'];
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
    $modelCRITERIA->offset = $COUNTVIEWELEMS * $idpage;
    $modelCRITERIA->limit = $COUNTVIEWELEMS;
}
$REND_model->setDbCriteria($modelCRITERIA);

$listall = $REND_model->findAll();

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
<a  class="btn" href="<?php echo $arrayuirow['new'];?>">new object</a>
<?php
if($this->dicturls['action']=='' && (count($selectorsids) || count($selectorsids_excluded))) {
    echo ' | ---------- <input class="btn btn-danger" name="checkdelete" type="submit" value="delete">';
}
if($this->dicturls['action']!='') {
    if($arrchecked) {
        echo '<div><code class="headermen">before saved: '.implode(',',$arrchecked).'</code></div>';
    }
    $html = '<code class="headermen cgreen">action: <b>'.$this->dicturls['action'].'</b></code> <input class="btn btn-mini btn-danger" type="submit" name="saveaction" value="save" />';
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
?>
<div>

<?php

$select_array = $REND_thisparamsui;
$order_by_array = array('-'=>'-');
foreach($select_array as $key => $value) {
    $order_by_array[$key] = $value;
    $order_by_array[$key.'---desc'] = $value.'-desc';
    
}
$select_order_by_param = CHtml::dropDownList('order_by_param', ((array_key_exists('order_by_param',$_POST) && $_POST['order_by_param'])?$_POST['order_by_param']:''),$order_by_array);

?>

<div id="filtersort" class="well" style="padding:0; padding-top:15px">
params:<br/>
<?php
$n_p=0;
do {
if(isset($_POST['serach_param']) && isset($_POST['serach_param'][$n_p]) && trim($_POST['serach_param'][$n_p])=='') {
    $n_p++;
    continue;
}
echo CHtml::dropDownList('filter_param['.$n_p.']', ((isset($_POST['filter_param']) && isset($_POST['filter_param'][$n_p]))?$_POST['filter_param'][$n_p]:''),$select_array);
?> 
<input  class="input-mini" name="serach_condition[<?php echo $n_p?>]" type="text" value="<?php echo ((isset($_POST['serach_condition']) && isset($_POST['serach_condition'][$n_p]))?$_POST['serach_condition'][$n_p]:'=')
;?>" />
<input name="serach_param[<?php echo $n_p?>]" type="text" value="<?php echo ((isset($_POST['serach_param']) && isset($_POST['serach_param'][$n_p]))?$_POST['serach_param'][$n_p]:'');?>" /><br/>
<?php
$n_p++;
}
while(isset($_POST['serach_param']) && $n_p <= count($_POST['serach_param']));
unset($select_array);

if($REND_objclass!==null && count($REND_objclass->properties)) {
$list_prop = CHtml::listData($REND_objclass->properties, 'codename', 'name');
$order_by_array = array('-'=>'-');
foreach($list_prop as $key => $value) {
    $order_by_array[$key] = $value;
    $order_by_array[$key.'---desc'] = $value.'-desc';
    
}

if($REND_thispropsui) {
    $select_order_by_prop = CHtml::dropDownList('order_by_prop', ((array_key_exists('order_by_prop',$_POST) && $_POST['order_by_prop'])?$_POST['order_by_prop']:''),$order_by_array);
}
$select_search_props = CHtml::dropDownList('filter_prop', ((array_key_exists('filter_prop',$_POST) && $_POST['filter_prop'])?$_POST['filter_prop']:''),$list_prop);
?>
| - | prop: <?php echo $select_search_props;?> 
<input  class="input-mini" name="serach_prop_condition" type="text" value="<?php echo (array_key_exists('serach_prop_condition',$_POST) && trim($_POST['serach_prop_condition'])!='')?$_POST['serach_prop_condition']:'=';?>" />
<input name="serach_prop" type="text" value="<?php echo (array_key_exists('serach_prop',$_POST) && trim($_POST['serach_prop'])!='')?$_POST['serach_prop']:'';?>" />
<?php } ?>
<input  class="btn" name="button_serach_prop" type="submit" value="find" />
<?php if($REND_thispropsui) {?>
| -- | sort prop: 
<?php echo $select_order_by_prop;?>
<input class="btn" name="button_order_by_prop" type="submit" value="sort" />
<?php }?>
| -- | sort param: 
<?php echo $select_order_by_param;?>
<input class="btn" name="button_order_by_prop" type="submit" value="sort" />
</div>
<?php


if($COUNT_P) {
?>
<table class="table table-striped table-bordered table-condensed table-hover">
<tr class="success">
<td><input class="btn btn-mini" name="allsetchecked" type="submit" value="s"> <input class="btn btn-mini btn-danger" name="checkedaction" type="submit" value="action check" /></td><td><?php echo $headershtml?></td><td>ui</td>
</tr>
<?php
$pkeys_all = array();


$relations_links_model = '';

$relconfsetting = $REND_confmodel;

if($relconfsetting && array_key_exists('relation', $relconfsetting)) {
    $rel_arr_vis = $relconfsetting['relation'];
}

if(isset($rel_arr_vis)) {
    foreach($REND_model->relations() as $namerelat => $val) {
        if(in_array($namerelat, $rel_arr_vis)) {
            $nameparammodel = $this->dicturls['paramslist'][1];
            if($this->dicturls['paramslist'][0]=='class') {
                $objclass = uClasses::getclass($this->dicturls['paramslist'][1]);
                $nameparammodel = $this->apcms->config['spacescl'][$objclass->tablespace]['namemodel'];
            }
            $typerelat = ($val[0]==$REND_model::MANY_MANY || $val[0]==$REND_model::HAS_MANY)?'add':'set';
            $name_current_model = $namerelat;
            $namemodel_alias = array_search($namerelat,$rel_arr_vis);
            if(array_key_exists($namemodel_alias, $this->apcms->config['controlui'][$this->dicturls['class']]['models'])) {
                $name_current_model = $namemodel_alias;
            }
            $print_link = ' | <a href="'.$urladmclass.'/objects/models/'.$name_current_model.'/action/%s/IDELEMENT/'.$typerelat.'/models/'.$nameparammodel.'">%s</a>';
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
        echo '<td>'.$obj->$colname.'</td>';
    }
    if($REND_thispropsui) {
        $properties = $obj->get_properties();
        foreach(array_keys($REND_thispropsui) as $colname) {
            echo '<td>'.$properties[$colname].'</td>';
        }
    }
    $uihtml = '';
    if($arrayuirow['edit']) $uihtml .= ' <a href="'.$arrayuirow['edit'].$obj->primaryKey.'"><i class="icon-edit"></i></a>';
    
        if($arrayuirow['objects']) $uihtml .= ' | <a href="'.$arrayuirow['objects'].$obj->primaryKey.'/action/edit/0/"><i class="icon-plus-sign"></i></a> | <a href="'.$arrayuirow['objects'].$obj->primaryKey.'">objects</a>';
        if($arrayuirow['links'] && $this->dicturls['paramslist'][0]=='class') $uihtml .= ' | <a href="'.$arrayuirow['links'].$obj->primaryKey.'">links</a>';
        
        if($arrayuirow['navgroup']) $uihtml .= ' | <a href="'.sprintf($arrayuirow['navgroup'],$obj->primaryKey).'">group_permission</a>';
        
        if($arrayuirow['editlinksclass']) $uihtml .= ' | <a href="'.$arrayuirow['editlinksclass'].$obj->primaryKey.'/action/lenksobjedit/'.$this->dicturls['paramslist'][4].'/class/'.$this->dicturls['paramslist'][2].'">editlinksclass</a>';

    
    if($relations_links_model) $uihtml .= ' | ---relation <i class="icon-arrow-right"></i>'.str_replace('IDELEMENT',$obj->primaryKey,$relations_links_model);
    
    $uihtml .= ' | --- <a onclick="return confirm(\'remove pk - '.$obj->primaryKey.'\')" href="'.$arrayuirow['remove'].$obj->primaryKey.'"><i class="icon-remove"></i></a>';
    echo '<td>'.$uihtml.' </td></tr>';
}

?>
</table></div>

<?php
if($COUNT_P>$COUNTVIEWELEMS) {
/*
$tamplate = array(
        'action'=>' class="active"',
        'nextleft'=>'<li><a href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">&laquo;</a></li>',
        'prevpg'=>'<li class="previous"><a id="prevpg" href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">&larr; Назад</a></li>',
        'nextpg'=>'<li class="next"><a id="nextpg" href="'.$this->createUrl($this->dicturls['all'],array('goin_'=>'')).'%s">Вперед &rarr;</a></li>',
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

$tamplate = array(
        'action'=>' class="active"',
        'nextleft'=>'<li><input type="submit" name="goin_%s" value="true" /><a href="#">&laquo;</a></li>',
        'prevpg'=>'<li class="previous"><input type="submit" name="goin_%s" value="true" /><a id="prevpg" href="#">Ctrl &larr;</a></li>',
        'nextpg'=>'<li class="next"><input type="submit" name="goin_%s" value="true" /><a id="nextpg" href="#">Ctrl &rarr;</a></li>',
        'nextright'=>'<li><input type="submit" name="goin_%s" value="true" /><a href="#">&raquo;</a></li>',
        'elem'=>'<li%s><input type="submit" name="goin_%s" value="true" /><a href="#">%s</a>
            
        </li>',
        'pagination' => '
        <div id="pagination" class="pagination">
        
            <ul class="pager">
                %s
            </ul>
            <p class="pagin-lenks"><ul>%s</ul></p>
        </div>
<script>
$("#pagination a").bind("click", function(){
    $(this).prev()[0].click();
    return false;
});
$(document).keydown(function(event){if(event.ctrlKey){if(event.keyCode == 37){if($("#prevpg").length){$("#prevpg")[0].click()}}else if(event.keyCode == 39){if($("#nextpg").length){$("#nextpg")[0].click()}}}});
</script>
');
echo '<div style="padding-bottom: 60px">'.apicms\utils\pagination($idpage,$COUNT_P,$COUNTVIEWELEMS,$COUNTVIEWPAGES,'',true,$tamplate).'</div>';
}
?>
<input name="pkeys_all" type="hidden" value="<?php echo implode(',',$pkeys_all);?>" />
</form>
<?php
}
?>
