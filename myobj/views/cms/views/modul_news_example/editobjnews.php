<?php
/*
 * start controller - в реальной работе контроллер вынести в отдельное представление
 * и при событии $obj->save() - сохранить изобрадения переданные в псевдосвойстве
 * а если пользователь удаляет или редактирует фотографии значит делаем доп input параметры которые будут говорить что сделал пользователь
 * если удаляет - то в некотором параметре будет указано что он удаленн, возможно уже не к модели это будет относится, ну в модели саом собой убрать
 *
 */

//$model_obj = uClasses::getclass('news_example')->objects()->findbypk($this->dicturls['paramslist'][1]); //найти
$model_obj = uClasses::getclass('news_example')->initobject(); //создать новый
$addelem = array();
$addelem[] = array('name'=>'image', 'def_value'=>'dfdf', 'elem'=>array('type'=>'file'));
$addrules = array();
$addrules[] = array('image','file', 'types'=>'jpg, png');
$form = $model_obj->UserFormModel->initform($_POST,array('name'=>'name2','annotation_news_exampleprop_'=>'annotation prop','image'=>'файлы'),$addelem,$addrules);
echo '<div style="border:1px solid red">';
/*
 *
 */
//$initFile->title(2,8); //новая сортировка, ключ элемента
//$file = yii::app()->storeFile->obj(array(34,35) или array(15));
/* @var CStoreFile $initFile */
$initFile = yii::app()->storeFile->obj(); //берет стандартный плагин
// ->name('name',0); по умолчанию элемент "0" если новый, или если существует "3"
//$initFile->name = 'name';
//$initFile->title = 'title';
//$initFile->file = '/tmp/patch';
//$initFile->save(); //сохранить все элементы
//echo $initFile->test;

//попробудем просто сохранить файл с момощью библиотеки
//получить объект файла

/* @var CFile $uploaded */
$uploaded = Yii::app()->file->set('EmptyForm[image]');
//echo $uploaded->getMimeType();
//echo $uploaded->copy('sdfsdf');
echo $uploaded->getBasename();
echo '</div>';


if(count($_POST) && $form->validate()) {
	//$model_obj->save();
	Yii::app()->user->setFlash('savemodel','save model OK');
	//$this->redirect(Yii::app()->request->url);
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