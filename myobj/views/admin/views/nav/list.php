<?php
$objectsNavigate = $REND_model->findAll();

$urladmclass = $this->dicturls['admin'];
$arrayuirow['edit'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/';
$arrayuirow['edittempl'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edittempl/';
$arrayuirow['new'] = $urladmclass.'/'.$this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/0/';
$arrayuirow['links'] = $urladmclass.'/objects/models/classes/'.$this->dicturls['paramslist'][1].'/links/';
$arrayuirow['remove'] = $urladmclass.'/'.$this->dicturls['class'].'/class/'.$this->dicturls['paramslist'][1].'/action/remove/';


$listobjsort = apicms\utils\treelem($objectsNavigate,null,'id','parent_id',function($str) {return $str.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';});

?>
<a class="btn" href="<?php echo $arrayuirow['new'];?>">new object</a>
<form method="post">
<table class="table table-striped table-bordered table-condensed table-hover">
<tr>
<td><input class="btn btn-primary" type="submit" name="push_" value="top" /></td>
<td>id</td>
<td>name</td>
<td>controller</td><td>action</td>
<td>top</td>
<td>sort</td>
<td>visible</td>
<td>ui</td>
</tr>

<?php
foreach($listobjsort as $objarr) {
	$obj = $objarr['obj'];
	$uihtml = '';
	if($arrayuirow['edit']) $uihtml .= ' <a href="'.$arrayuirow['edit'].$obj->primaryKey.'"><i class="icon-edit"></i></a>';
	if($arrayuirow['links']) $uihtml .= ' | <a href="'.$arrayuirow['links'].$obj->primaryKey.'">links</a>';

	$objclass = uClasses::getclass(array('navigation_sys','param_sys'));
	$uihtml .= ' | <a href="'.$urladmclass.'/objects/class/'.$objclass['param_sys']->primaryKey.'/action/lenksobjedit/'.$obj->primaryKey.'/class/'.$objclass['navigation_sys']->primaryKey.'">params</a>';
	if($arrayuirow['edittempl']) $uihtml .= ' | <a class="btn btn-primary" href="'.$arrayuirow['edittempl'].$obj->primaryKey.'&usercontroller=usernav">template</a>';

	$visibl = ($obj->show)?'+':'-';

	$uihtml .= ' | --- <a onclick="return confirm(\'remove id - '.$obj->primaryKey.'\')" href="'.$arrayuirow['remove'].$obj->primaryKey.'"><i class="icon-remove"></i></a>';
	echo '<tr><td><input type="checkbox" name="elemch_'.$obj->primaryKey.'" /></td><td>'.$obj->primaryKey.'</td><td><span class="label label-info">'.$objarr['left'].$obj->name.'</span></td><td>'.$obj->controller.'</td><td>'.$obj->action.'</td><td>'.$obj->parent_id.'</td><td>'.$obj->sort.'</td><td>'.$visibl.'</td>
	<td><input class="btn btn-small btn-info" type="submit" name="push_'.$obj->id.'" value="push" /> | '.$uihtml.' </td>
	</tr>';
}
?>
</table>
</form>