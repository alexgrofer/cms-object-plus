<?php
if(!$REND_acces_write) {
	echo '<p class="alert">not acces edit</p>';
}
Yii::import('MYOBJ.appscms.api.utils',true);
$classtemplate = uClasses::getclass('templates_sys');
$classhandle = uClasses::getclass('handle_sys');
$templates = $REND_model->getobjlinks('templates_sys')->findAll();
$currenthandles = $REND_model->getobjlinks('handle_sys')->findAll();
$keysidhandle = array();
foreach($currenthandles as $objhandle) {
	$keysidhandle[$objhandle->sort] = $objhandle;
}

$alltemolates = $classtemplate->objects();
if(array_key_exists('submit',$_POST)) {

	if(($templates && $templates[0]->primaryKey != $_POST['settemplid']) || !$templates) {
		if($templates) {
			$REND_model->editlinks('remove','templates_sys',$templates[0]);
		}
		if($_POST['settemplid']!='0') {
			$REND_model->editlinks('add','templates_sys',$_POST['settemplid']);
		}
	}


	foreach($_POST as $key => $idview) {
		if(strpos($key, 'for_handltmp')!==false) {
			list($tmplhandl,$tmplhandl_name) = array_slice(explode('__',$key),1);

			if(array_key_exists($tmplhandl,$keysidhandle)) {
				if($keysidhandle[$tmplhandl]->vp1 != $idview) {
					if($keysidhandle[$tmplhandl]->name!=$tmplhandl_name) {
						$keysidhandle[$tmplhandl]->name=$tmplhandl_name;
					}
					if($idview==0 && $keysidhandle[$tmplhandl]->vp1) {

						//автоматически если удаляем ссылки есть такая задача
						$keysidhandle[$tmplhandl]->delete();
						//task можно не удалять а только отвязывать и ставить vp1 = 0 потом при создании искать свободный
						//$REND_model->editlinks('remove','handle_sys',$keysidhandle[$tmplhandl]->primaryKey);
					}
					else {
						$keysidhandle[$tmplhandl]->vp1 = $idview;
						$keysidhandle[$tmplhandl]->save();
					}
				}
			}
			elseif($idview!='0') {
				$newhandle = $classhandle->initobject();
				$newhandle->name=$tmplhandl_name;
				$newhandle->content='';
				$newhandle->sort = $tmplhandl;
				$newhandle->vp1=$idview;
				$newhandle->save();
				$REND_model->editlinks('add','handle_sys',$newhandle);
			}

		}
	}
	if($this->dicturls['actionid']=='0') {
		$this->redirect($this->getUrlBeforeAction());
	}
	else {
		$this->redirect(Yii::app()->request->url);
	}

}
?>
<form method="post">
<p> <code>template <b>default</b>: </code> <select name="settemplid"><option value="0">---</option>'
<?php
	foreach($alltemolates->findAll() as $objtmpl) {
		$select = ($templates && $templates[0]->primaryKey == $objtmpl->primaryKey)?'selected="selected"':'';
		echo '<option value="'.$objtmpl->primaryKey.'" '.$select.'>'.$objtmpl->name.'</option>';
	}
?>
</select></p>
<?php
if($templates) {
$arrallviews = uClasses::getclass('views_sys')->objects()->findAll();

$contenttmpl=file_get_contents(yii::getPathOfAlias('MYOBJ.views').DIR_TEMPLATES_SITE.$templates[0]->vp1.'_content'.'.php');

$arraypregtmp = array();
preg_match_all('~->renderHandle\(\'(.+)\',(\d+)~',$contenttmpl, $arraypregtmp);
if(count($arraypregtmp)) {
echo '<table  class="table table-condensed"><tr><td>name</td><td>view</td></tr>';
$array_combine = array_combine($arraypregtmp[2], $arraypregtmp[1]);
ksort($array_combine);
foreach($array_combine as $key => $namehand) {
?>
	<tr>
		<td><?php echo $namehand?></td>
		<td>
			<p><select name="<?php echo 'for_handltmp__'.$key.'__'.$namehand?>">
			<?php
			echo '<option value="0">---</option>';
			foreach($arrallviews as $objviw) {
				$select = '';
				if(isset($keysidhandle[$key]) && $keysidhandle[$key]->vp1 == $objviw->primaryKey) {
					$select = 'selected="selected"';
				}
				echo '<option value="'.$objviw->primaryKey.'" '.$select.'>'.$objviw->name.' - '.$objviw->vp1.'.php</option>';
			}
			?>
			</select></p>
		</td>
	</tr>
<?php
}
echo '</table>';
}
}
?>
<p><input name="submit" type="submit" value="save desing"/></p>
</form>
<hr/>
<?php
if(array_key_exists('savep',$_POST)) {
	$contentsav = $REND_model->content;
	foreach($_POST['paramsnav'] as $key => $val) {
		$contentsav = apicms\utils\uiparamnav($contentsav,$key,$val);
	}
	$REND_model->content = $contentsav;
	$REND_model->save();
}
$params = $REND_model->getobjlinks('param_sys')->findAll();
if(count($params)) {
$valuesparam = apicms\utils\uiparamnav($REND_model->content);

?>
<form method="post">
<p>params:</p>
<?php
foreach($params as $objparam) {
	$value=(array_key_exists($objparam->vp1, $valuesparam))?$valuesparam[$objparam->vp1]:'';
	echo '<p>'.$objparam->name.': <textarea type="text" name="paramsnav['.$objparam->vp1.']" >'.$value.'</textarea></p>';
}
?>
<p><input name="savep" type="submit" value="save param"/></p>
</form>
<?php
}
?>