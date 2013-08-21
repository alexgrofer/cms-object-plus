<?php
/*
 * start controller - в реальной работе контроллер вынести или использовать контроллер по умолчанию
 */

$model_obj = uClasses::getclass('news_example')->objects()->findbypk($this->dicturls['paramslist'][1]); //найти
//$model = uClasses::getclass('news_example')->initobject(); //создать новый
$addelem = array();
$addelem[] = array('name'=>'image', 'def_value'=>'dfdf', 'elem'=>array('type'=>'file'));
$addrules = array();
$addrules[] = array('image','file', 'types'=>'jpg, png');
$form = $model_obj->UserFormModel->initform($_POST,array('name'=>'name2','annotation_news_exampleprop_'=>'annotation prop','image'=>'файлы'),$addelem,$addrules);


if(count($_POST) && $form->validate()) {
	$model_obj->save();
	Yii::app()->user->setFlash('savemodel','save model OK');
	$this->redirect(Yii::app()->request->url);
}

// end controller

/*
 * создать новый объект добавить фотографии
 * редактировать объект добавить фотографии
 * удалить отдельные фото
 * отсортировать фото
 */


if(Yii::app()->user->hasFlash('savemodel')) {
	echo Yii::app()->user->getFlash('savemodel');
}

$form->attributes = array('enctype' => 'multipart/form-data');
$form->activeForm = array(
	'enableClientValidation'=>false,
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