<?php
echo '<h4>'.get_class($REND_model).'</h4>';
if($REND_model instanceof AbsBaseHeaders) {
	echo '<h5>class header: '.$REND_model->uclass->codename.'</h5>';
}

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

$htmlElementsAddForm = array();
$addForm = MYOBJ\appscms\core\base\form\DForm::create();
if($REND_model instanceof AbsBaseHeaders) {
	//uProperties
	$namepConst_UPop = '__uprop';
	foreach($REND_model->getUProperties() as $name => $val) {
		$dName=$name.$namepConst_UPop;
		$addForm->addAttributeRule($dName, array('safe'), $val);
		$htmlElementsAddForm[$dName] = array('type'=>'text');
	}
}
else {
	//MTM PARAM
	if($this->dicturls['paramslist'][5]=='relationobjonly' && $REND_selfobjrelationElements) {
		$array_names_v_mtm = array();
		$nameps_mtm = '_col_mtm_model';

		//смотрим в конфиге какие колонки из дочерней таблице показываем при selfobjrelation
		foreach($REND_selfobjrelationElements[$this->dicturls['paramslist'][8]] as $namer) {
			$SelectArr = $REND_model->links_edit('select',$this->dicturls['paramslist'][8],array($this->dicturls['paramslist'][6]),$namer);

			$nameElem = $namer.$nameps_mtm;
			$addForm->addAttributeRule($nameElem, array('safe'), $SelectArr[$namer]);
			$htmlElementsAddForm[$nameElem] = array('type'=>'text');

			$array_names_v_mtm[$namer] = $SelectArr[$namer];
		}
	}
	//EArray
	$namepConst_EArray = '__earray';
	$param_sep = '_nameearray_';
	$confEA = $REND_model->confEArray();
	foreach($confEA as $nameParamEA => $confParam) {
		if (isset($confParam['paramsRules'])) {
			foreach ($confParam['paramsRules'] as $namePropEA => $rules) {
				$dName = $nameParamEA . $param_sep . $namePropEA . $namepConst_EArray;
				$addForm->addAttributeRule($dName, array('safe'), $REND_model->getEArray($nameParamEA, $namePropEA));
				$htmlElementsAddForm[$dName] = array('type' => 'text');
			}
		}
	}
}

if($REND_AttributeLabels) {
	$REND_model->customAttributeLabels = array_merge($REND_model->attributeLabels(), $REND_AttributeLabels);
}

$isValidated=false;
$paramsQueryPostModel = yii::app()->getRequest()->getPost(get_class($REND_model));
if($paramsQueryPostModel) {

	$addForm->attributes = yii::app()->getRequest()->getPost('MYOBJ_appscms_core_base_form_DForm');

	if($addForm->validate()) {

		if($REND_model instanceof AbsBaseHeaders) {
			foreach($addForm->getAttributes() as $name => $val) {
				//UProperties
				if($pos = strpos($name,$namepConst_UPop)) {
					$NormNameUProp = substr($name,0,$pos);
					$REND_model->uProperties = array($NormNameUProp, $val);
				}

			}
		}
		else {
			foreach($addForm->getAttributes() as $name => $val) {
				//MTM PARAM
				if (isset($array_names_v_mtm) && count($array_names_v_mtm)) {
					$array_edit_post_mtmparam = array();
					if (($pos = strpos($name, $nameps_mtm))) {
						$name_norm = substr($name, 0, $pos);
						$array_edit_post_mtmparam[$name_norm] = trim($val);
					}
				}
				//EArray
				if($pos = strpos($name,$namepConst_EArray)) {
					$NormNameUEArray = substr($name,0,$pos);
					list($EAPropName, $EAPropParamName) = explode($param_sep, $NormNameUEArray);
					$REND_model->editEArray($EAPropName, array($EAPropParamName=>$val));
				}
			}
		}

		$REND_model->attributes = $paramsQueryPostModel;
		$isValidated = $REND_model->validate();
	}
}

//если есть настройка то только по ней
$htmlElementsModel = $REND_model->elementsForm() ?: array_fill_keys($REND_model->getSafeAttributeNames(), ['type'=>'text']);
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

$form = new CForm(array('elements'=>array(
	'model'=>array(
		'type'=>'form',
		'title'=>'model',
		'elements'=>$htmlElementsModel,
	),

	'DForm'=>array(
		'type'=>'form',
		'title'=>'additionally',
		'elements'=>$htmlElementsAddForm,
	),
)));
$form->attributes = array('enctype' => 'multipart/form-data');

$form['model']->model = $REND_model;
$form['DForm']->model = $addForm;

echo $form->renderBegin();

echo CHtml::errorSummary(array($addForm, $REND_model),'<div class="alert alert-danger">','</p>');

foreach($form->getElements() as $element) {
	echo $element->render();
}

echo '<p>'.CHtml::submitButton('save').'</p>';

echo $form->renderEnd();
//END

if($isValidated) {
	$REND_model->save();

	if(isset($relation_relationobjonly_one_m) && $relation_relationobjonly_one_m==false) {
		$objrelated->links_edit('add', $nameRelatThis, array($REND_model->primaryKey));
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