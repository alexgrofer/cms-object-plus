<?php
/*
 * создать новый объект добавить фотографии
 * редактировать объект добавить фотографии
 * удалить отдельные фото
 * отсортировать фото
 */

//$model = uClasses::getclass('news_example')->objects()->findByAttributes(); //найти
$model = uClasses::getclass('news_example')->initobject(); //создать новый
$addelem = array();
$addelem[] = array('name'=>'file', 'def_value'=>'dfdf', 'elem'=>array('type'=>'file'));
$form = $model->UserFormModel->initform($_POST,array('name'=>'name2','file'=>'файлы'),$addelem);
$form->attributes = array('enctype' => 'multipart/form-data');

$form->activeForm = array(
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
);
echo $form->renderBegin();

foreach($form->getElements() as $element) {
	echo $element->render();
}


echo '<p>'.CHtml::submitButton('save').'</p>';


echo $form->renderEnd();