<?php
echo CHtml::link('list obj', yii::app()->createUrl('myobj/test/header_list')).'<br><br>';
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/test/header_edit')).'<br><br>';

$nameClassObjEdit = get_class($objEdit);

//сохранять объект AR при изменении поля по событиям type, change (для существующих объектов)
$is_save_event = true;
//включить ajax
$enableAjaxValidation = true;

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
				//если свойство отмеченно в настройке ajaxPropValidate для валидации ajax ИЛИ если необходимо онлайн сохранение
				if($.inArray(attribute.name, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate)!=-1 || spaceMyFormEdit_'.$nameClassObjEdit.'.is_save_event==true) {
					this.enableAjaxValidation = true; //установим аякс на свойство
				}
				attributeName = "'.$nameClassObjEdit.'["+attribute.name+"]";
				form.find("input, select, textarea").each(function() {
					if(this.name == attributeName) { //только если это текущее свойство
						$("#TestObjHeaders_validate_params").val(attribute.name); //пометим поле для проверки на сервере, уйдет только оно
					}
					//для остальных
					else if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) { //только для safe свойств
						$(this).prop("disabled", true); //свойство не уйдет в запросе
					}
				});

				return true;
			}',

			'afterValidateAttribute'=>'js:function(form, attribute, data, hasError) { //событие на свойстве (type или change) после валидации, получаем результат
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

						if( //Отправим только определенные свойства на сервер в запросе
						//если объект СУЩЕСТВУЕТ в базе И значение свойства отличается от ранее сохраненного
						(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==false && spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name]!=$.trim(this.value))
							||
						//если объект НОВЫЙ И свойство должно быть проверенно ajax-ом
						//--после проверки будет получен результат и уже все данные уйдут на сохранение
						(spaceMyFormEdit_'.$nameClassObjEdit.'.isNewObj==true && (spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate.length == 0 || $.inArray(normalName, spaceMyFormEdit_'.$nameClassObjEdit.'.ajaxPropValidate)!=-1))
						) {
							arr.push(normalName); //добавить свойство в список на отправку в запросе
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
					if(hasError==false && spaceMyFormEdit_'.$nameClassObjEdit.'.is_save_event==true) { //если ошибок нет И это онлайн сохранение
						if(spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] != undefined) { //только для safe свойств
							spaceMyFormEdit_'.$nameClassObjEdit.'.startSafeParamsForm[this.name] = this.value; //обновить стартовые параметры новыми данными
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
//защита от неправильной настройки is_save_event в случае если это новый объект
if($is_save_event && $objEdit->isNewRecord) {
	$is_save_event = false;
}
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
		$arrJS_startSafeParamsForm[] = "'".$nameClassObjEdit."[".$k."]':'".trim($v)."'";
	}
}
?>
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.isNewObj = <?php echo ($objEdit->isNewRecord) ?'true':'false'; ?>;
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.is_save_event = <?php echo ($is_save_event) ?'true':'false'; ?>;
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.startSafeParamsForm = {<?php echo implode(', ', $arrJS_startSafeParamsForm)?>};
//-в случае если мы знаем что только определенные свойства должны проверяться(отправляться) ajax при создании нового объекта
//--в случае если идет online редактиравание ajax свойства уже не нужно учитывать так как запрос отправляется всегда при редактировании любого свойтва
spaceMyFormEdit_<?php echo $nameClassObjEdit?>.ajaxPropValidate = [<?php echo implode(', ', array("'param2'"))?>];
//end
</script>
