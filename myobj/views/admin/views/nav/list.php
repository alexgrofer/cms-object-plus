<?php
$objectsNavigate = $REND_model->findAll();

$arrayuirow['edit'] = $this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/';
$arrayuirow['edittempl'] = $this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edittempl/';
$arrayuirow['new'] = $this->dicturls['class'].'/'.$this->dicturls['paramslist'][0].'/'.$this->dicturls['paramslist'][1].'/action/edit/0/';
$arrayuirow['remove'] = $this->dicturls['class'].'/class/'.$this->dicturls['paramslist'][1].'/action/remove/';


$listobjsort = MYOBJ\appscms\core\base\SysUtils::treelem($objectsNavigate,null,'id','parent_id',function($str) {return $str.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';});

?>
<a class="btn" href="<?php echo $this->createAdminUrl($arrayuirow['new'])?>">new object</a>
<form method="post">
<table class="table table-striped table-bordered table-condensed table-hover">
<tr>
<td><input class="btn btn-primary" type="submit" name="push_" value="top" /></td>
<td>id</td>
<td>name</td>
<td>desc</td>
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
	if($arrayuirow['edit']) $uihtml .= ' <a href="'.$this->createAdminUrl($arrayuirow['edit'].$obj->primaryKey).'"><i class="icon-edit"></i></a>';

	$objclass_navigation_sys = uClasses::getclass('navigation_sys');
	$uihtml .= ' | <a class="btn" href="'.$this->createAdminUrl('objects/class/param_sys/action/relationobjonly/'.$obj->primaryKey.'/add/relation_name/navigate').'">edit params</a>';

	$uihtml .= ' | <a class="btn" href="'.$this->createAdminUrl('objects/class/templates_sys/action/relationobj/'.$obj->primaryKey.'/add/relation_name/navigationsDef').'">template def</a>';
	$uihtml .= ' | <a class="btn" href="'.$this->createAdminUrl('objects/class/templates_sys/action/relationobj/'.$obj->primaryKey.'/add/relation_name/navigationsMobileDef').'">template mobile</a>';

	if($arrayuirow['edittempl']) $uihtml .= ' | <a class="btn btn-primary" href="'.$this->createAdminUrl($arrayuirow['edittempl'].$obj->primaryKey, array('usercontroller'=>'usernav')).'">handles</a>';

	$visibl = ($obj->show)?'+':'-';

	$uihtml .= ' | --- <a onclick="return confirm(\'remove id - '.$obj->primaryKey.'\')" href="'.$this->createAdminUrl($arrayuirow['remove'].$obj->primaryKey).'"><i class="icon-remove"></i></a>';
	echo '<tr><td><input type="checkbox" name="elemch_'.$obj->primaryKey.'" /></td><td>'.$obj->primaryKey.'</td><td><span class="label label-info">'.$objarr['left'].$obj->name.'</span></td><td><small>'.$obj->desc.'</small></td><td>'.$obj->controller.'</td><td>'.$obj->action.'</td><td>'.$obj->parent_id.'</td><td>'.$obj->sort.'</td><td>'.$visibl.'</td>
	<td><input class="btn btn-small btn-info" type="submit" name="push_'.$obj->id.'" value="push" /> | '.$uihtml.' </td>
	</tr>';
}
?>
</table>
</form>