<?php
echo CHtml::link('list obj', yii::app()->createUrl('myobj/test/header_list')).'<br><br>';
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/test/header_edit')).'<br><br>';

$nameClassObjEdit = get_class($objEdit);

//conf
//сохранять объект AR при изменении поля по событиям type, change (для существующих объектов)
$is_save_event = false;
//валидация по отдельным полям ajax
$is_validate_save_ajax = false;

$functionSetStrJS_AJAX_FIELD_EDIT = function($orherIsDisabled) use($nameClassObjEdit) {
	return '
	attributeName = "'.$nameClassObjEdit.'["+attribute.name+"]";
	isBeforeValidateAttribute = '.$orherIsDisabled.';

	//не отправлять каждый раз ajax для этого свойства
	if(isBeforeValidateAttribute==true && spaceMyFormEdit_'.$nameClassObjEdit.'.is_validate_save_ajax==true) { //beforeValidateAttribute
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
		|| (spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==true && spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate.length == 0 || $.inArray(attribute.name, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate)!=-1)
	) {
		//установим признак что поле должно отправлятся ajax всегда, достаточно делать в beforeValidateAttribute
		if(isBeforeValidateAttribute==true && spaceMyFormEdit_'.$nameClassObjEdit.'.is_validate_save_ajax==true) {
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

			/**
			 * Все событийные скрипты работают только в случае если включен enableClientValidation !!!!!!!!!!!!
			 */

			//events
			'beforeValidateAttribute'=>'js:function(form, attribute) {
				//событие на свойстве (type или change) до валидации

				return true;
			}',

			'afterValidateAttribute'=>'js:function(form, attribute, data, hasError) {
				//событие на свойстве (type или change) после валидации, получаем результат


				return true;
			}',

			'beforeValidate'=>'js:function(form) { //событие до отправки submit
				arr = [];
				form.find("input, select, textarea").each(function() {
					if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) { //только для safe свойств
						normalName = this.name.replace("'.$nameClassObjEdit.'[", ""); normalName = normalName.replace("]", "");

						if( //Отправим только определенные свойства на сервер в запросе
						//если объект СУЩЕСТВУЕТ в базе И значение свойства отличается от ранее сохраненного
						(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==false && spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name]!=this.value)
							||
						//если объект НОВЫЙ И свойство должно быть проверенно ajax-ом
						//--после проверки будет получен результат и уже все данные уйдут на сохранение
						(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==true && (spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate.length == 0 || $.inArray(normalName, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate)!=-1))
						) {
							arr.push(normalName);
						}
						else { //остальные сделаем disabled
							$(this).prop("disabled", true);
						}
					}
				});
				$("#TestObjHeaders_validate_params").val(arr); //если arr пуст уйдет пустая строка

				return true;
			}',

			'afterValidate'=>'js:function(form, data, hasError) { //событие после отправки submit, получили результат
				form.find("input, select, textarea").each(function() {
					//вернем свойства к нормальному состоянию
					$(this).prop("disabled", false);
					//очистим поле свойств для выборочной валидации
					$("#TestObjHeaders_validate_params").val("");

					if(hasError==false) { //если ошибок нет обновить стартовые параметры новыми данными
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) { //только для safe свойств
							spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] = this.value;
						}
					}
				});

				return true; //если false не будет отправленна на контроллер
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


echo CHtml::hiddenField($nameClassObjEdit . '[save_event]', (int)$is_save_event);
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
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.is_validate_save_ajax = <?php echo ($is_validate_save_ajax) ?'true':'false'; ?>;
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.startSafeParamsForm = {<?php echo implode(', ', $arrJS_startSafeParamsForm)?>};
//-в случае если мы знаем что только определенные свойства должны проверяться(отправляться) ajax при создании нового объекта
//--в случае если идет online редактиравание ajax свойства уже не нужно учитывать так как запрос отправляется всегда при редактировании любого свойтва
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.ajaxPropValidate = [<?php echo implode(', ', array("'param2'"))?>];
//end
</script>
