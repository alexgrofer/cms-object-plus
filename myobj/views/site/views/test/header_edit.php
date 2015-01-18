<?php
echo CHtml::link('list obj', yii::app()->createUrl('myobj/test/header_list')).'<br><br>';
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/test/header_edit')).'<br><br>';

$nameClassObjEdit = get_class($objEdit);


$functionSetStrJS_AJAX_FIELD_EDIT = function($orherIsDisabled) use($nameClassObjEdit) {
	return '
	//обработчик только для поля которое должно работать с ajax
	if($.inArray(attribute.name, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxFieldsEvent)) {
		//при ajax отправляю только значение текущего редакрируемого поля
		attributeName = "'.$nameClassObjEdit.'["+attribute.name+"]";
		form.find("input, select, textarea").each(function() {
			if(this.name!=attributeName) $(this).prop("disabled", '.$orherIsDisabled.');
		});
		//на сервере в vilidate должно проверятся только данное поле т.е только оно ушло в запросе

		//нормальная обработка validate

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
//параметры которые изменялись (Для существующих объектов)
echo CHtml::textField($nameClassObjEdit.'[edit_params]', '');
//end

echo '<p>'.CHtml::submitButton('save').'</p>';

echo $form->renderEnd();
?>
<script>
//start user edit form
var spaceMyFormEdit_<?php echo $nameClassObjEdit?> = {};
<?php
//поля которые необходимо проверять ajax при событиях type, change
$ajaxFieldsEvent = array(
	"'param2'",
);

foreach($objEdit->attributes as $k=>$v) {
	if($objEdit->isAttributeSafe($k)) {
		$arrJS_StartParamsForm[] = $k.":'".$v."'";
	}
}
?>
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.ajaxFieldsEvent = [<?php echo implode(', ', $ajaxFieldsEvent)?>];
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.startParamsForm = {<?php echo implode(', ', $arrJS_StartParamsForm)?>};
//end
</script>
