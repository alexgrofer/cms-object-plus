<?php
echo CHtml::link('list obj', yii::app()->createUrl('myobj/test/header_list')).'<br><br>';
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/test/header_edit')).'<br><br>';

$nameClassObjEdit = get_class($objEdit);

//conf
//сохранять объект AR при изменении поля по событиям type, change (для существующих объектов)
$is_save_event = false;


$functionSetStrJS_AJAX_FIELD_EDIT = function($orherIsDisabled) use($nameClassObjEdit, $is_save_event) {
	return '
	attributeName = "'.$nameClassObjEdit.'["+attribute.name+"]";
	isBeforeValidateAttribute = '.$orherIsDisabled.';

	//

	//если необходимо отправить при ajax
	if(1) {
		form.find("input, select, textarea").each(function() {
			//только если это не текущий элемент делаем его disabled
			//ищем только по safe параметрам
			if(this.name != attributeName) {
				if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) {
					$(this).prop("disabled", '.$orherIsDisabled.');
				}
			}
			else{ //добавить в массив поле которое должно уйти в редактирование - ЭТО текущее поле
				$("#TestObjHeaders_validate_params").val(attribute.name);
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
				arr = [];
				form.find("input, select, textarea").each(function() {
					//если это safe поле и изменилось добавить его в список на валидацию(отправку)
					if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) {
						if($.inArray(this.value, spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm)==false) {
							normalName = str.replace("'.$nameClassObjEdit.'[", ""); normalName = str.replace("]", "");
							arr.push(normalName);
						}
						//поля которые не изменились не уходять и не проверяются на сервере
						else {
							$(this).prop("disabled", true);
						}
					}
				});
				$("#TestObjHeaders_validate_params").val(arr);

				return true;
			}',

			'afterValidate'=>'js:function(form, data, hasError) {
				form.find("input, select, textarea").each(function() {
					//вернем все поля к disabled=false
					$(this).prop("disabled", false);

					//если ошибок нет обновить стартовые параметры новыми данными ()
					if(hasError==false) {
						//если это safe поле
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) {
							spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] = this.value;
						}
					}
				});

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

//для объектов AR
if($objEdit->isNewRecord==false) {
	echo CHtml::textField($nameClassObjEdit . '[save_event]', 1);
}
//если объект сохранятся в онлайне ему не нужна кнопка уже, но это не принципиально можно и оставить
if($is_save_event==false) {
	echo '<p>' . CHtml::submitButton('save') . '</p>';
}
//end

echo $form->renderEnd();
?>
<script>
//start user edit form
var spaceMyFormEdit_<?php echo $nameClassObjEdit?> = {};
<?php
foreach($objEdit->attributes as $k=>$v) {
	if($objEdit->isAttributeSafe($k)) {
		$arrJS_startSafeParamsForm[] = "'".$nameClassObjEdit."[".$k."]':'".$v."'";
	}
}
?>
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.startSafeParamsForm = {<?php echo implode(', ', $arrJS_startSafeParamsForm)?>};
//end
</script>
