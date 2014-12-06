<?php
if(!$REND_acces_write) {
	echo '<p class="alert">not acces edit</p>';
}
/** @var $objRequest CHttpRequest */
$objRequest = Yii::app()->request;

/** @var $THIS_NAVIGATE NavigateSystemObjHeaders */
$THIS_NAVIGATE = $REND_model;

/** @var $classTemplate uClasses */
$classTemplate = uClasses::getclass('templates_sys');
/** @var $classHandle uClasses */
$classHandle = uClasses::getclass('handle_sys');
/** @var $classView uClasses */
$classView = uClasses::getclass('views_sys');

//показать юзеру текущий шаблон по умолчанию или выбранный шаблон в селекте
/** @var $currentObjectTemplate TemplateSystemObjHeaders */
$currentObjectTemplate = $THIS_NAVIGATE->templateDefault;
if(($select_template_id = $objRequest->getPost('select_template_id', null))) {
	$currentObjectTemplate = $classTemplate->initobject()->findByPk($select_template_id);
}

//все хендлы для выбранного или текущего шаблона шаблоона
$arrayObjectsHandles = $THIS_NAVIGATE->getobjlinks('handle_sys')->findAllByAttributes(['template_id'=>$select_template_id]);

//выбрать шаблон для настройки
?>
<form id="handle_config" name="handle_config" method="post">
<p>
	<code>template:</code>
	<select name="select_template_id" onselect="$('#handle_config').submit()">
		<?
		//в случае если нет шаблона по умолчанию и не выбран в select показываем пустой
		if(!$currentObjectTemplate) {?>
			<option value="0">none</option>
		<?php
		}
		foreach ($classTemplate->initobject()->findAll() as $objTemplate) {
			$selected = ($currentObjectTemplate)?($select_template_id == $currentObjectTemplate->primaryKey):false?'selected="selected"':'';
			echo '<option value="'.$objTemplate->primaryKey.'" '.$selected.'>'.$objTemplate->name.'</option>';
		}
		?>
	</select>
</p>


<?php
if(isset($_POST['submit_handle_config'])) {
	$contentTemplate = file_get_contents(yii::getPathOfAlias('MYOBJ.views').DIR_TEMPLATES_SITE .$currentObjectTemplate->path.'_content'.'.php');

	$arrayPreg_Match = array();
	preg_match_all('~->renderHandle\(\'(.+)\',(\d+)~', $contentTemplate, $arrayPreg_Match);
	if (count($arrayPreg_Match)) {
		echo '<table  class="table table-condensed"><tr><td>name</td><td>view</td></tr>';
		$array_combine = array_combine($arrayPreg_Match[2], $arrayPreg_Match[1]);
		ksort($array_combine);
		foreach ($array_combine as $key => $nameHandle) {
			?>
			<tr>
				<td><?php echo $nameHandle ?></td>
				<td>
					<p><select name="<?='for_handltmp__'.$key.'__'.$nameHandle ?>">
						<?php
						//всегда даем возможность удалить хендлер
						echo '<option value="0">none</option>';
						/** @var $objView ViewSystemObjHeaders */
						foreach ($classView->initobject()->findAll() as $objView) {
							$select = '';
							if (isset($arrayThisHandle[$key]) && $arrayThisHandle[$key]->view_id == $objView->primaryKey) {
								$select = 'selected="selected"';
							}
							echo '<option value="'.$objView->primaryKey.'" '. $select.'>'.$objView->name.' - '.$objView->path.'.php</option>';
						}
						?>
					</select></p>
				</td>
			</tr>
		<?php
		}
		echo '</table>';
	}

	?>
	<p><input name="submit_handle_config" type="submit" value="save"/></p>
	</form>
<?
}