<?php
echo CHtml::link('list obj', yii::app()->createUrl('myobj/test/header_list')).'<br><br>';
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/test/header_edit')).'<br><br>';

$nameClassObjEdit = get_class($objEdit);


$functionSetStrJS_AJAX_FIELD_EDIT = function($orherIsDisabled) use($nameClassObjEdit) {
	return '
	//если необходимо отправить при ajax поля по отдельности
	if(1) {
		attributeName = "'.$nameClassObjEdit.'["+attribute.name+"]";
		form.find("input, select, textarea").each(function() {
			//только если это не текущий элемент
			//ищем только по safe параметрам
			if(this.name != attributeName && spaceMyFormEdit_'.$nameClassObjEdit.'.startParamsForm[this.name] != undefined) {
				$(this).prop("disabled", '.$orherIsDisabled.');
			}
		});
	}
	';
};
$idForm = 'form'.$nameClassObjEdit;
$configForm = array(
	'elements'=>$objEdit->elementsForm(),
	'activeForm' => array(
		//'class' => 'CActiveForm',
		'id'=>$idForm,
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'clientOptions'=>array(
			//event submit form
			'validateOnSubmit'=>true,
			//event field
			'validateOnType'=>true,
			'validateOnChange'=>false,

			//events
			'beforeValidateAttribute'=>'js:function(form, attribute) {
				'.$functionSetStrJS_AJAX_FIELD_EDIT('true').'
				return true;
			}',

			'afterValidateAttribute'=>'js:function(form, attribute, data, hasError) {
				'.$functionSetStrJS_AJAX_FIELD_EDIT('false').'

				return true;
			}',

			'beforeValidate'=>'js:function(form) {
				return true;
			}',

			'afterValidate'=>'js:function(form, data, hasError) {
				return true;
			}',
		),
	),
);
$form = new CForm($configForm, $objEdit);
$form->attributes = array(
	'enctype' => 'multipart/form-data',
	'autocomplete' => 'off',
);
echo $form->renderBegin();

echo CHtml::errorSummary($objEdit);

foreach($form->getElements() as $element) {
	echo $element->render().'<br>';
}

//start user edit form
echo CHtml::textField($nameClassObjEdit.'[validate_params]', $validate_params_value);
/**
 * данные будут сохраняться сразе при редактировании люблго поля объекта
 * по сути submit не нужен в таких формах, к примеру при редактировании личных данных
 */
echo CHtml::textField($nameClassObjEdit.'[save_event]', 1);
//end

echo '<p>'.CHtml::submitButton('save').'</p>';

echo $form->renderEnd();
?>
<script>
//start user edit form
var spaceMyFormEdit_<?php echo $nameClassObjEdit?> = {};
<?php
foreach($objEdit->attributes as $k=>$v) {
	if($objEdit->isAttributeSafe($k)) {
		$arrJS_StartParamsForm[] = "'".$nameClassObjEdit."[".$k."]':'".$v."'";
	}
}
?>
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.startParamsForm = {<?php echo implode(', ', $arrJS_StartParamsForm)?>};
//end
</script>
