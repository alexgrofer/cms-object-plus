<?php
/*
 * start controller - в реальной работе контроллер вынести в отдельное представление
 * и при событии $obj->save() - сохранить изобрадения переданные в псевдосвойстве
 * а если пользователь удаляет или редактирует фотографии значит делаем доп input параметры которые будут говорить что сделал пользователь
 * если удаляет - то в некотором параметре будет указано что он удаленн, возможно уже не к модели это будет относится, ну в модели саом собой убрать
 *
 */

$model_obj = uClasses::getclass('news_example')->objects()->findbypk(3); //найти
//$model_obj = uClasses::getclass('news_example')->initobject(); //создать новый
$addelem = array();
$addelem[] = array('name'=>'image', 'def_value'=>'dfdf', 'elem'=>array('type'=>'CMultiFileUpload'));
$addrules = array();
$addrules[] = array('image', 'file', 'maxFiles'=>10, 'maxSize'=>((1024*1024)*16), 'allowEmpty'=>true, 'safe'=>true); //task добавить в плагин
$form = $model_obj->UserFormModel->initform($_POST,array('name'=>'name2','annotation_news_exampleprop_'=>'annotation prop','image'=>'файлы'),$addelem,$addrules);

if(count($_POST) && $form->validate()) {
/*
 * -создать новый объект и добавить к нему фотки 12,14 добавить фотографии
 * -загружаем файлы если объект не создался удаляем эти файлы в исключении
 * в каком месте производить эти действия ??
 * -показать загруженные файлы
 * -возможность удаления файла
 */
	$model_obj->save();
	//сохраним файлы
	/* @var CStoreFile $initFile */
	$initFile = yii::app()->storeFile->obj();
	$initFile->setFolderAll('news'); //установить главную папку для загрузки

	$initFile->file = 'EmptyForm[image][0]';
	$initFile->save();
	//добавим эти файлы id в модель task все далаем тут или в модели?
	Yii::app()->user->setFlash('savemodel','save model OK');
	//$this->redirect(Yii::app()->request->url);
}


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