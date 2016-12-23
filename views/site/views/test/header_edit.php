<style>
	/*
	что бы убрать мелькания
	input, textarea, {background: #fff; border: 1px solid red}
	*/
</style>
<?php
echo CHtml::link('list obj', yii::app()->createUrl('myobj/tests/header/list')).'<br><br>';
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/tests/header/edit')).'<br><br>';

$nameClassObjEdit = get_class($objEdit);

//НАСТРОЙКИ

//сохранять объект AR при изменении поля по событиям type, change (для существующих объектов)
$is_save_event = true;
//включить ajax
$enableAjaxValidation = true;
//параметры которые будет проверяться с помощью ajax
$ajaxPropValidate = array("'param2'");
//параметры которые будет проверяться с помощью ajax динамически только при событиях (type или change), остальные будут отправленны аяксом только при событии submit
$ajaxParamsOnlyEventTypeChange = array("'param2'");

// -- //

$idForm = 'form'.$nameClassObjEdit;
$configForm = array(
	'elements'=>$objEdit->elementsForm(),
	'activeForm' => array(
		//'class' => 'CActiveForm',
		'id'=>$idForm,
		'enableAjaxValidation' => $enableAjaxValidation,
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

			'beforeValidateAttribute'=>'js:function(form, attribute) { //событие на свойстве (type или change) до валидации
				this.enableAjaxValidation = false; //отключим аякс со свойства, так как НЕ на каждое это может быть нужно
				//если включен enableAjaxValidation И (если свойство отмеченно в настройке ajaxPropValidate для валидации ajax ИЛИ если необходимо онлайн сохранение то для всех)
				if(
					(
						spaceMyFormEdit_'.$nameClassObjEdit.'.enableAjaxValidation==true //только если включен enableAjaxValidation
							&&
						($.inArray(attribute.name, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxParamsOnlyEventTypeChange)!=-1 //И если свойство есть в списке на проверку аяксом при событиях (type или change)
					)
					($.inArray(attribute.name, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate)!=-1 //если свойство есть в списке проверок ajax
						||
					spaceMyFormEdit_'.$nameClassObjEdit.'.is_save_event==true) //если работаем в ражиме онлайн сохранения
				) {
					this.enableAjaxValidation = true; //установим аякс на свойство
					attributeName = "'.$nameClassObjEdit.'["+attribute.name+"]";
					form.find("input, select, textarea").each(function() {
						if(this.name == attributeName) { //только если это текущее свойство
							$("#'.$nameClassObjEdit.'_validate_params").val(attribute.name); //пометим поле для проверки, проверится на сервере только оно
						}
						//для остальных
						else if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) { //только для safe свойств
							$(this).prop("disabled", true); //свойство не уйдет в запросе
						}
					});
				}

				return true;
			}',

			'afterValidateAttribute'=>'js:function(form, attribute, data, hasError) { //событие на свойстве (type или change) после валидации, получаем результат
				if(spaceMyFormEdit_'.$nameClassObjEdit.'.is_save_event==true) { //при онлайн редактировании
					if(hasError==true) { //если была ошибка
						if(spaceMyFormEdit_'.$nameClassObjEdit.'["errors_save_event"] == undefined) { //определим массив ошибок онлайн редактирования
							spaceMyFormEdit_'.$nameClassObjEdit.'.errors_save_event = {};
						}
						spaceMyFormEdit_'.$nameClassObjEdit.'.errors_save_event[attribute.name] = 1; //добавим в массив ошибоку
						$("#'.$nameClassObjEdit.'_save_event").val(0); // запретим онлайн редактирование
					}
					else if(spaceMyFormEdit_'.$nameClassObjEdit.'["errors_save_event"] != undefined) { //если ошибки нет И были на других полях
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.errors_save_event[attribute.name] != undefined) { //если на этом поле была ошибка
							delete(spaceMyFormEdit_'.$nameClassObjEdit.'.errors_save_event[attribute.name]); // удалим ее из массива
						}
						if($.isEmptyObject(spaceMyFormEdit_'.$nameClassObjEdit.'.errors_save_event)) { //если ошибок больше нет
							$("#'.$nameClassObjEdit.'_save_event").val(1); // включим онлайн редактировани
							$(form).trigger("submit"); //отправим форму целиком по сабмиту
							delete(spaceMyFormEdit_'.$nameClassObjEdit.'.errors_save_event); //удалим сам массив
						}
					}
				}
				form.find("input, select, textarea").each(function() {
					if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) { //только для safe свойств
						$(this).prop("disabled", false); //вернем свойства к нормальному состоянию
					}
				});

				return true;
			}',

			'beforeValidate'=>'js:function(form) { //событие до отправки submit
				arr = [];
				form.find("input, select, textarea").each(function() {
					if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) { //только для safe свойств
						normalName = this.name.replace("'.$nameClassObjEdit.'[", ""); normalName = normalName.replace("]", "");

						if(
							(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==false //старый объект
								&&
							spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name]!=$.trim(this.value)) //значение свойства отличается от ранее сохраненного
						||
							(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==true //новый объект
								&&
							(spaceMyFormEdit_'.$nameClassObjEdit.'.enableAjaxValidation==true && (spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate.length == 0 || $.inArray(normalName, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate)!=-1)))
						) {
							arr.push(normalName); //добавить свойство в список на отправку в запросе

							$(this).prop("disabled", false); //если при онлайн валидации происходили ошибки может быть disabled, нужно сделать видимым
						}
						else {
							$(this).prop("disabled", true);
						}
					}
				});
				$("#'.$nameClassObjEdit.'_validate_params").val(arr); //если arr пуст уйдет пустая строка

				return true;
			}',

			'afterValidate'=>'js:function(form, data, hasError) { //событие после отправки submit, получили результат
				form.find("input, select, textarea").each(function() {
					if(hasError==false) { //если ошибок нет
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) { //только для safe свойств
							spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] = this.value; //обновить стартовые параметры новыми данными
						}
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==true) {
							$(this).prop("disabled", false); //все данные уходят уже на сохранение
							$("#'.$nameClassObjEdit.'_validate_params").val(""); //все проверяется вместе
						}
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.is_save_event==true) {
							$(this).prop("disabled", false);
						}
					}
					else { //если были ошибки
						$(this).prop("disabled", false);
					}
				});

				if(hasError==false) {
					if(spaceMyFormEdit_'.$nameClassObjEdit.'.is_save_event==true) {
						//если не нужно перезагружать после удачного сохранения то:
						//alert("save ok");
						//return false;

						//если нужно перезагрузить то
						return true;
					}
				}

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
//защита от неправильной настройки is_save_event в случае если это новый объект ИЛИ если отключен enableAjaxValidation
if(($is_save_event && $objEdit->isNewRecord) || $enableAjaxValidation==false) {
	$is_save_event = false;
}
echo CHtml::hiddenField($nameClassObjEdit . '[save_event]', (int)$is_save_event); //передадим на сервер что будет ондайн сохранение при всех событиях
//
echo '<p>' . CHtml::submitButton('save') . '</p>';
//end

echo $form->renderEnd();
?>
<script>
//start user edit form
var spaceMyFormEdit_<?=$nameClassObjEdit?> = {};
<?php
foreach($objEdit->attributes as $k=>$v) {
	if($objEdit->isAttributeSafe($k)) {
		$arrJS_startSafeParamsForm[] = "'".$nameClassObjEdit."[".$k."]':'".trim($v)."'";
	}
}
?>
spaceMyFormEdit_<?=$nameClassObjEdit?>.isNewObj = <?=($objEdit->isNewRecord) ?'true':'false'; ?>;
spaceMyFormEdit_<?=$nameClassObjEdit?>.enableAjaxValidation = <?=($enableAjaxValidation) ?'true':'false'; ?>;
spaceMyFormEdit_<?=$nameClassObjEdit?>.is_save_event = <?=($is_save_event) ?'true':'false'; ?>;
spaceMyFormEdit_<?=$nameClassObjEdit?>.startSafeParamsForm = {<?=implode(', ', $arrJS_startSafeParamsForm)?>};
//-в случае если мы знаем что только определенные свойства должны проверяться(отправляться) ajax при создании нового объекта
//--в случае если идет online редактиравание ajax свойства уже не нужно учитывать так как запрос отправляется всегда при редактировании любого свойтва
spaceMyFormEdit_<?=$nameClassObjEdit?>.ajaxPropValidate = [<?=implode(', ', $ajaxPropValidate)?>];
spaceMyFormEdit_<?=$nameClassObjEdit?>.ajaxParamsOnlyEventTypeChange = [<?=implode(', ', $ajaxParamsOnlyEventTypeChange)?>];
//end
</script>
