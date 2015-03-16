<?php
if(!$REND_acces_write) {
	echo '<p class="alert">not acces edit</p>';
}
if(!$REND_acces_read) {
	echo '<p class="alert">not acces read</p>';
	return;
}
$htmldiv = '<div%s>%s</div>';
$htmlp='<p class="%s">%s</p>';
$htmlspan='<span class="%s">%s</span>';
$htmlinput='<input type="%s" name="%s" value="%s" class="%s" />';

if($REND_AttributeLabels) {
	$REND_model->customAttributeLabels = array_merge($REND_model->attributeLabels(), $REND_AttributeLabels);
}

if($this->dicturls['paramslist'][5]=='relationobjonly' && $REND_selfobjrelationElements) {
	$array_names_v_mtm = array();
	$nameps_mtm = '_col_mtm_model';

	//смотрим в конфиге какие колонки из дочерней таблице показываем при selfobjrelation
	foreach($REND_selfobjrelationElements[$this->dicturls['paramslist'][8]] as $namer) {
		$SelectArr = $REND_model->links_edit('select',$this->dicturls['paramslist'][8],array($this->dicturls['paramslist'][6]),$namer);

		$nameElem = $namer.$nameps_mtm;
		if(!$REND_model->isAddElemClass($nameElem)) {
			$REND_model->addElemClass($nameElem, $SelectArr[$namer]);
			$REND_model->customElementsForm[$nameElem] = array('type'=>'text');
			$REND_model->customRules[] = array($nameElem, 'safe');
		}

		$array_names_v_mtm[$namer] = $SelectArr[$namer];
	}
}

$paramsQueryPostModel = yii::app()->getRequest()->getPost('MYOBJ_appscms_core_base_form_DForm');
if($paramsQueryPostModel) {
	$REND_model->attributes = $paramsQueryPostModel;
	//важный фактор только после этой конструкции форма $form начинает обрабатывать ошибки
	$REND_model->validate();
}

//если есть настройка то только по ней
$elementsForm = $REND_model->elementsForm() ?: array_fill_keys($REND_model->getSafeAttributeNames(), ['type'=>'text']);
if($REND_editForm) {
	foreach($REND_model->elementsForm() as $nameElem => $nameAlias) {
		if(!in_array($nameElem,$REND_editForm)) unset($elementsForm[$nameElem]);
	}
}

if($this->dicturls['paramslist'][5]=='relationobjonly' && $this->dicturls['actionid']=='0') {
	$relation_relationobjonly_one_m = false;

	$params_modelget = MYOBJ\appscms\core\base\SysUtils::normalAliasModel($this->dicturls['paramslist'][1]);
	$nameRelatConfModel = $params_modelget['relation'][$this->dicturls['paramslist'][8]][0];
	$nameRelatThis = $params_modelget['relation'][$this->dicturls['paramslist'][8]][1];
	$params_modelgetRelat = MYOBJ\appscms\core\base\SysUtils::normalAliasModel($nameRelatConfModel);
	$objrelated = $params_modelgetRelat['namemodel']::model()->findByPk($this->dicturls['paramslist'][6]);

	$thisRelations = $objrelated->metaData->relations;
	$thisRelation = $thisRelations[$nameRelatThis];
	$typeThisRelation = get_class($thisRelation);

	if(in_array($typeThisRelation, array(CActiveRecord::HAS_ONE, CActiveRecord::HAS_MANY))) {
		$relation_relationobjonly_one_m = true;
		$REND_model->{$thisRelation->foreignKey} = $this->dicturls['paramslist'][6];
	}
}
$objForm = MYOBJ\appscms\core\base\form\DForm::createOfModel($REND_model); //может все же метод сделать может на простом сайте кто заюзает ну путь только тут вначале сделай

$form = new CForm(array('elements'=>$elementsForm));
$form->attributes = array('enctype' => 'multipart/form-data');
$form->setModel($objForm);
echo $form->renderBegin();

echo CHtml::errorSummary(array($objForm, $REND_model),'<div class="alert alert-danger">','</p>');

foreach($form->getElements() as $element) {
	echo $element->render();
}

echo '<p>'.CHtml::submitButton('save').'</p>';

echo $form->renderEnd();
//END

if(count($_POST) && $form->validate()) {
	$REND_model->save();

	if(isset($relation_relationobjonly_one_m) && $relation_relationobjonly_one_m==false) {
		$objrelated->links_edit('add', $nameRelatThis, array($REND_model->primaryKey));
	}

	foreach($paramsQueryPostModel as $key => $val) {
		if(isset($array_names_v_mtm) && count($array_names_v_mtm)) {
			$array_edit_post_mtmparam = array();
			if(($pos = strpos($key,$nameps_mtm))) {
				$name_norm = substr($key,0,$pos);
				$array_edit_post_mtmparam[$name_norm] = trim($val);
			}
		}
	}

	if(isset($array_edit_post_mtmparam) && count($array_edit_post_mtmparam)) {
		$REND_model->setMTMcol($this->dicturls['paramslist'][8],array($this->dicturls['paramslist'][6]),$array_edit_post_mtmparam);
	}

	if($this->dicturls['actionid']=='0') {
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