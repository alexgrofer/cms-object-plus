<?php
if(!$REND_acces_write) {
	//должно делаться в контроллере - или добавить мини контроллер ?
	echo '<p class="alert">not acces edit</p>';
}
$htmldiv = '<div%s>%s</div>';
$htmlp='<p class="%s">%s</p>';
$htmlspan='<span class="%s">%s</span>';
$htmlinput='<input type="%s" name="%s" value="%s" class="%s" />';

//не показывать те элементы которых нет в настройке для этого типа
if($REND_editForm) {
	foreach($REND_model->elementsForm() as $nameElem => $nameAlias) {
		if(!in_array($nameElem,$REND_editForm)) unset($REND_model->customElementsForm[$nameElem]);
	}
}
if($REND_AttributeLabels) {
	$REND_model->customAttributeLabels = array_merge($REND_model->customAttributeLabels, $REND_AttributeLabels);
}

if($this->dicturls['paramslist'][5]=='relationobjonly' && $REND_selfobjrelationElements) {
	$array_names_v_mtm = array();
	$nameps_mtm = '_col_mtm_model';

	//смотрим в конфиге какие колонки из дочерней таблице показываем при selfobjrelation
	foreach($REND_selfobjrelationElements[$this->dicturls['paramslist'][8]] as $namer) {
		//убрать из цикла
		$SelectArr = $REND_model->getMTMcol($this->dicturls['paramslist'][8],$this->dicturls['paramslist'][6],$namer);

		$nameElem = $namer.$nameps_mtm;
		$REND_model->addElemClass($nameElem, $SelectArr[$namer]);
		$REND_model->customElementsForm[$nameElem] = array('type'=>'text');
		$REND_model->customRules[] = array($nameElem, 'safe');

		$array_names_v_mtm[$namer] = $SelectArr[$namer];
	}
}
//редактирование представлений и шаблонов
//опять же перенести все в контроллер, или создать отдельный контроллер еси нужно и присоеденить к текущему контроллеру возможно присоединять контроллеры?
if(in_array($this->param_contr['current_class_name'],array('templates_sys','views_sys'))) {
	$namefilderfile = ($this->param_contr['current_class_name']=='templates_sys')?'templates':'views';
	$namefile = dirname(__FILE__).'/../cms/'.$namefilderfile.'/'.$REND_model->vp1.'.php';
	if(file_exists($namefile)) {
		$contenttext=file_get_contents($namefile);
	}
	$snamefile = 'edit_file_template';

	$REND_model->addElemClass($snamefile, isset($contenttext)?$contenttext:'');
	$REND_model->customElementsForm[$snamefile] = array('type'=>'textarea');
	$REND_model->customRules[] = array($snamefile, 'safe');
}
//work EArray
$typesEArray = $REND_model->typesEArray();
if(count($typesEArray)) {
	foreach($typesEArray as $nameCol => $setting) {
		$valuetypesEArray = $REND_model->get_EArray($nameCol);
		if(isset($setting['elements']) && count($setting['elements'])) {
			if($setting['conf']['isMany']) {
				$index = count($valuetypesEArray)?count($valuetypesEArray):0;
				foreach($setting['elements'] as $nameE) {
					$REND_model->generate_EArray(null,$nameCol,$nameE,$index);
				}
			}
		}
		else {
			//если нет элементов можно добавить свои task
		}
	}
}
//end EArray

$paramsQueryPostModel = yii::app()->getRequest()->getPost(get_class($REND_model));
if($paramsQueryPostModel) {
	$REND_model->attributes = $paramsQueryPostModel;
	//важный фактор только после этой конструкции форма $form начинает обрабатывать ошибки
	$REND_model->validate();
}

$form = new CForm(array('elements'=>$REND_model->elementsForm()), $REND_model);
$form->attributes = array('enctype' => 'multipart/form-data');
echo $form->renderBegin();

foreach($form->getElements() as $element) {
	echo $element->render();
}

echo '<p>'.CHtml::submitButton('save').'</p>';

echo $form->renderEnd();
//END

if(count($_POST) && $form->validate()) {
	$REND_model->save();

	if($this->dicturls['paramslist'][5]=='relationobjonly' && $this->dicturls['actionid']=='0') {
		$params_modelget = apicms\utils\normalAliasModel($this->dicturls['paramslist'][1]);
		$nameRelatConfModel = $params_modelget['relation'][$this->dicturls['paramslist'][8]][0];
		$nameRelatThis = $params_modelget['relation'][$this->dicturls['paramslist'][8]][1];
		$params_modelgetRelat = apicms\utils\normalAliasModel($nameRelatConfModel);

		$objrelated = $params_modelgetRelat['namemodel']::model()->findByPk($this->dicturls['paramslist'][6]);
		$objrelated->links_edit('add', $nameRelatThis, array($REND_model->primaryKey));
	}


	foreach($paramsQueryPostModel as $key => $val) {
		if(isset($array_names_v_mtm) && count($array_names_v_mtm)) {
			$array_edit_post_mtmparam = array();
			if(($pos = strpos($key,$nameps_mtm)) && array_key_exists(($name_norm = substr($key,0,$pos)),$array_names_v_mtm) && (array_key_exists($name_norm, $array_names_v_mtm) && $array_names_v_mtm[$name_norm]!=$val)) {
				$array_edit_post_mtmparam[$name_norm] = trim($val);
			}
		}
		//редактирование файлов task так надоли это ?
		if(isset($namefile) && strpos($key,$snamefile)!==false) {
			//file_put_contents($namefile, $val);
		}

	}
	if(isset($array_edit_post_mtmparam) && count($array_edit_post_mtmparam)) {
		$REND_model->setMTMcol($this->dicturls['paramslist'][8],array($this->dicturls['paramslist'][6]),$array_edit_post_mtmparam);
	}



	if($this->dicturls['actionid']=='0') {
		//print_r($_SERVER);exit;Yii::app()->request->getUrlReferrer();
		$urlRedirect = $this->getUrlBeforeAction();
		if($this->dicturls['paramslist'][5]=='relationobjonly') {
			$urlRedirect = $urlRedirect.'action/'.$this->dicturls['paramslist'][5].'/'.$this->dicturls['paramslist'][6].'/add/'.$this->dicturls['paramslist'][7].'/'.$this->dicturls['paramslist'][8];
		}
		$this->redirect($urlRedirect);
	}
	else {
		$this->redirect(Yii::app()->request->url);
	}
}

?>
<style>
.errorMessage {color: red;padding-bottom: 15px}
</style>