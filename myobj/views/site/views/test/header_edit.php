<?php
echo CHtml::link('list obj', yii::app()->createUrl('myobj/test/header_list')).'<br><br>';
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/test/header_edit')).'<br><br>';

$configForm = array(
	'elements'=>$objEdit->elementsForm(),
	'activeForm' => array(
		'class' => 'CActiveForm',
		'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'clientOptions'=>array(
			'validateOnSubmit'=>true,
			'validateOnType'=>true,

			'validateOnChange'=>false,
		),
	),
);
$nameClassObjEdit = get_class($objEdit);
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

echo CHtml::textField($nameClassObjEdit.'[validate_params]', $validate_params_value);

echo '<p>'.CHtml::submitButton('save').'</p>';

echo $form->renderEnd();
