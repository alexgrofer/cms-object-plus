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

if($currentObjectTemplate) {
	//все хендлы навигации для выбранного или текущего шаблона
	$arrayObjectsHandles = $THIS_NAVIGATE->getobjlinks('handle_sys', 'handle')->findAllByAttributes(['template_id'=>$currentObjectTemplate->primaryKey]);
	$arrayObjectsHandlesKeysCodenameHandle = $arrayObjectsHandlesKeysViewId =[];
	foreach($arrayObjectsHandles as $objHandle) {
		$arrayObjectsHandlesKeysCodenameHandle[$objHandle->codename] = $arrayObjectsHandlesKeysViewId[$objHandle->view_id] = $objHandle;
	}
	unset($arrayObjectsHandles);
}

//сохранение конфигурации навигации хендлов
$savedHandle = false;
if($objRequest->getPost('submit_handle_config')) {
	foreach($objRequest->getRestParams() as $nameParamHandleName => $idView) {
		if($handleName = strstr($nameParamHandleName, '_select_config_handle_name', true)) {
			//если есть хендл для этого шаблона
			if(isset($arrayObjectsHandlesKeysCodenameHandle[$handleName])) {
				$objHandle = $arrayObjectsHandlesKeysCodenameHandle[$handleName];
				//если представление изменилось
				if(false==isset($arrayObjectsHandlesKeysViewId[$idView])) {
					$objHandle->view_id = $idView;
					$objHandle->save();
				}
				elseif($idView==0) { //если установил пустой нужно удалить
					$THIS_NAVIGATE->editlinks('remove', 'handle_sys', array($objHandle->primaryKey), 'handle');
					$objHandle->delete();
				}
			}
			elseif($idView!=0) { //создаем новый хендл если нет такого
				$objHandle = $classHandle->initobject();
				$objHandle->setAttributes([
					'codename'=>$handleName,
					'template_id'=>$currentObjectTemplate->primaryKey,
					'view_id'=>$idView,
				]);
				$objHandle->save();
				$THIS_NAVIGATE->editlinks('add', 'handle_sys', array($objHandle->primaryKey), 'handle');
			}
		}
	}
	if($THIS_NAVIGATE->validate()) {
		$this->redirect(Yii::app()->request->url);
	}
}

if(empty($objHandle)) {
	echo CHtml::errorSummary($REND_model, '<div class="alert alert-danger">', '</p>');
}
?>
<form id="handle_config" name="handle_config" method="post">
<p>
	<code>template:</code>
	<select name="select_template_id" onchange="$('#handle_config').submit()">
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
</form>


<?php
//если выбран шаблон или есть по умолчанию покажем его хендлеры для настройки
if($currentObjectTemplate) {
	echo '<form method="post">';
	echo CHtml::hiddenField('select_template_id', $currentObjectTemplate->primaryKey);
	$contentTemplate = file_get_contents(yii::getPathOfAlias('MYOBJ.views').DIR_TEMPLATES_SITE .$currentObjectTemplate->path.'_content'.'.php');
	preg_match_all('~->renderHandle\(\s*\'(.+)\',~', $contentTemplate, $arrayPreg_Match);
	if (count($arrayPreg_Match)) {
		echo '<table  class="table table-condensed"><tr><td>name</td><td>view</td></tr>';
		foreach ($arrayPreg_Match[1] as $codenameHandle) {
			$codenameHandle = trim($codenameHandle);
			?>
			<tr>
				<td><?php echo $codenameHandle ?></td>
				<td>
					<p><select name="<?=$codenameHandle?>_select_config_handle_name">
						<?php
						//всегда даем возможность удалить хендлер
						echo '<option value="0">none</option>';
						/** @var $objView ViewSystemObjHeaders */
						foreach ($classView->initobject()->findAll() as $objView) {
							$select = '';
							if(isset($arrayObjectsHandlesKeysCodenameHandle[$codenameHandle]) && $arrayObjectsHandlesKeysCodenameHandle[$codenameHandle]->view_id==$objView->primaryKey) {
								$select = 'selected="selected"';
							}
							echo '<option value="'.$objView->primaryKey.'" '. $select.'>'.'('.$objView->primaryKey.') '.$objView->name.' - '.$objView->path.'.php</option>';
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