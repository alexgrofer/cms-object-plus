<?php
$listall = $REND_model->findAll();

$urladmclass = $this->dicturls['admin'];
$arrayuirow['edit'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/';
$arrayuirow['edittempl'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edittempl/';
$arrayuirow['new'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/0/';
$arrayuirow['links'] = $urladmclass.'/objects/models/classes/'.$this->dicturls['paramslist'][1].'/links/';
$arrayuirow['remove'] = $urladmclass.'/'.$this->dicturls['class'].'/class/'.$this->dicturls['paramslist'][1].'/action/remove/';


$listobjsort = apicms\utils\treelem($listall,'id','vp1',0,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
?>
<a class="btn" href="<?php echo $arrayuirow['new'];?>">new object</a>
<form method="post">
<table class="table table-striped table-bordered table-condensed table-hover">
<tr>
<td><input class="btn btn-primary" type="submit" name="push_0" value="0" /></td>
<td>id</td>
<td>name</td>
<td>codename</td>
<td>sort</td>
<td>visible</td>
<td>ui</td>
</tr>

<?php
foreach($listobjsort as $objarr) {
    $obj = $objarr[0];
    $uihtml = '';
    if($arrayuirow['edit']) $uihtml .= ' <a href="'.$arrayuirow['edit'].$obj->id.'"><i class="icon-edit"></i></a>';
    if($arrayuirow['links']) $uihtml .= ' | <a href="'.$arrayuirow['links'].$obj->id.'">links</a>';
    
    $objclass = uClasses::getclass(array('navigation_sys','param_sys','controllersnav_sys'));
    $uihtml .= ' | <a href="'.$urladmclass.'/objects/class/'.$objclass['param_sys']->id.'/action/lenksobjedit/'.$obj->id.'/class/'.$objclass['navigation_sys']->id.'">params</a>';
    $uihtml .= ' | <a href="'.$urladmclass.'/objects/class/'.$objclass['controllersnav_sys']->id.'/action/lenksobjedit/'.$obj->id.'/class/'.$this->dicturls['paramslist'][1].'">controller</a>';
    if($arrayuirow['edittempl']) $uihtml .= ' | <a class="btn btn-primary" href="'.$arrayuirow['edittempl'].$obj->id.'">template</a>';
    
    $visibl = ($obj->bp1)?'+':'-';
    
    $uihtml .= ' | --- <a onclick="return confirm(\'remove id - '.$obj->id.'\')" href="'.$arrayuirow['remove'].$obj->id.'"><i class="icon-remove"></i></a>';
    echo '<tr><td><input type="checkbox" name="elemch_'.$obj->id.'" /></td><td>'.$obj->id.'</td><td><span class="label label-info">'.$objarr[1].$obj->name.'</span></td><td>'.$obj->vp2.'</td><td>'.$obj->sort.'</td><td>'.$visibl.'</td>
    <td><input class="btn btn-small btn-info" type="submit" name="push_'.$obj->id.'" value="push" /> | '.$uihtml.' </td>
    </tr>';
}
?>
</table>
</form>