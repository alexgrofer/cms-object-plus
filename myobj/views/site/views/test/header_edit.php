<?php
echo CHtml::link('list obj', yii::app()->createUrl('myobj/test/header_list')).'<br><br>';
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/test/header_edit')).'<br><br>';

$nameClassObjEdit = get_class($objEdit);

//conf
//сохранять объект AR при изменении поля по событиям type, change (для существующих объектов)
$is_save_event = false;
$is_validate_save_ajax = true;

$functionSetStrJS_AJAX_FIELD_EDIT = function($orherIsDisabled) use($nameClassObjEdit, $is_save_event) {
	return '
	attributeName = "'.$nameClassObjEdit.'["+attribute.name+"]";
	isBeforeValidateAttribute = '.$orherIsDisabled.';

	//не отправлять каждый раз ajax
	if(isBeforeValidateAttribute==true) { //beforeValidateAttribute
		this.enableAjaxValidation = false;
	}

	//после валидации нужно
	if(isBeforeValidateAttribute==false) {
		//сделать поле с валидаторым пустым т.к при validate может уйти при сабмите, так как при событии валидации ajax предыдущего поля прописало туда свой параметр
		$("#TestObjHeaders_validate_params").val("");
	}

	//отправляем данные на сервер ЕСЛИ это старый объект (для онлайн сохранения)
	//ИЛИ в случае с новым объектом параметры которые точно указаны в списке или если списк ajaxPropValidate пуст
	if(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==false
		|| (spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate.length == 0 || $.inArray(attribute.name, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate)!=-1)
	) {
		//установим признак что поле должно отправлятся ajax всегда, достаточно делать в beforeValidateAttribute
		if(isBeforeValidateAttribute==true) {
			this.enableAjaxValidation = true;
		}

		form.find("input, select, textarea").each(function() {
			//только если это текущее свойство
			if(this.name != attributeName) {
				//только для safe свойств
				if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) {
					$(this).prop("disabled", '.$orherIsDisabled.');
				}
			}
			else{ //добавить в массив поле которое должно уйти в редактирование - ЭТО текущее поле
				$("#TestObjHeaders_validate_params").val(attribute.name);
			}
		});
	}

	//после проверки ставить на место, так как submit в конечном этоге отправить все данные для сохранения на сервере
	if(isBeforeValidateAttribute==false) { //afterValidateAttribute
		this.enableAjaxValidation = true;
	}
	';
};
$idForm = 'form'.$nameClassObjEdit;
$configForm = array(
	'elements'=>$objEdit->elementsForm(),
	'activeForm' => array(
		//'class' => 'CActiveForm',
		'id'=>$idForm,
		'enableAjaxValidation' => $is_validate_save_ajax,
		'enableClientValidation' => true,
		'clientOptions'=>array(
			//event submit form
			'validateOnSubmit'=>true,
			//event field
			'validateOnType'=>true,
			'validateOnChange'=>false,

			//events
			'beforeValidateAttribute'=>'js:function(form, attribute) {
				//событие на свойстве (type или change) до отправки submit
				'.$functionSetStrJS_AJAX_FIELD_EDIT('true').'
				return true;
			}',

			'afterValidateAttribute'=>'js:function(form, attribute, data, hasError) {
				//событие на свойстве (type или change) после нажатия, получаем результат
				'.$functionSetStrJS_AJAX_FIELD_EDIT('false').'

				return true;
			}',

			'beforeValidate'=>'js:function(form) {
				//событие до отправки submit
				arr = [];
				form.find("input, select, textarea").each(function() {
					//только для safe свойств
					if(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==false && spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) {
						//если значение поля отлично от ранее сохраненного
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name]!=this.value) {
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
				//событие после нажатия сабмита, получаем результат
				form.find("input, select, textarea").each(function() {
					//вернем все поля к disabled=false
					$(this).prop("disabled", false);

					//если ошибок нет обновить стартовые параметры новыми данными
					if(hasError==false) {
						//только для safe свойств
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) {
							spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] = this.value;
						}
					}
				});

				return true; //если false порма не будет перезагруженна на контроллер
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
	echo CHtml::hiddenField($nameClassObjEdit . '[save_event]', 1);
}
//если объект сохранятся при событии на свойстве формы type, change то ему не нужна кнопка уже, но это не принципиально можно и оставить при желании
//
echo '<p>' . CHtml::submitButton('save') . '</p>';
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
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.isNewObj = <?php echo ($objEdit->isNewRecord) ?'true':'false'; ?>;
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.startSafeParamsForm = {<?php echo implode(', ', $arrJS_startSafeParamsForm)?>};
//-в случае если мы знаем что только определенные свойства должны проверяться(отправляться) ajax при создании нового объекта
//--в случае если идет online редактиравание ajax свойства уже не нужно учитывать так как запрос отправляется всегда при редактировании любого свойтва
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.ajaxPropValidate = [<?php echo implode(', ', array("'param2'"))?>];
//end
</script>
