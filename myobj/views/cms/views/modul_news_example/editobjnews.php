<?php
$this->pageTitle='edit info page';

$model_obj = uClasses::getclass('news_example')->objects()->findByAttributes(array('name'=>'test_news1'));

if(!$model_obj) {
	echo '<p>not find element news</p>';
	return;
}

$paramsQueryPostModel = yii::app()->getRequest()->getPost(get_class($model_obj));
if($paramsQueryPostModel) {
	$model_obj->attributes = $paramsQueryPostModel;
	//важный фактор только после этой конструкции форма $form начинает обрабатывать ошибки
	$model_obj->validate();
}


$model_obj->customElementsForm['image'] = array('type'=>'CMultiFileUpload');

$model_obj->customRules[] = array('image', 'file', 'maxFiles'=>10, 'maxSize'=>((1024*1024)*16), 'allowEmpty'=>true, 'safe'=>true);


$form = new CForm(array('elements'=>$model_obj->elementsForm()), $model_obj);
$form->attributes = array('enctype' => 'multipart/form-data');

if(count($_POST) && $form->validate()) {
	//вначале сохраним объект
	$model_obj->save();
	//создадим файлы если пользователь их загрузил
	if(isset($_FILES['EmptyForm[image]'])) {
		//start load file
		/* @var CStoreFile $initFile */
		$initFile = yii::app()->storeFile->obj(EnumerationPluginStoreFile::DEF); //инициализируем новый объект нужным плагином(определит поведение)
		//методы общие как для мн. так и для отд. загрузкой
		$initFile->isRandAll = true; //все файлы будут названны рандомно
		//end
		//метод нужно использовать только при мн. хранении,загрузит файлы пачкой
		$initFile->filesMany = $_FILES['EmptyForm[image]']; //загрузит пачкой все файлы если EmptyForm[image] массив
		//метод
		$initFile->path = $model_obj->id; //логическая папка(news-папка плагина будет учтена/id объекта это название) файлов новости $_FILES['EmptyForm[image]'] если это множество файлов

		$initFile->save(); //сохранит объект и файл
		//end load file
		Yii::app()->user->setFlash('savemodel','save file id='.$initFile->id.' OK');
	}
	Yii::app()->user->setFlash('savemodel','save model OK');
	//$this->redirect(Yii::app()->request->url);
}


if(Yii::app()->user->hasFlash('savemodel')) {
	echo Yii::app()->user->getFlash('savemodel').'<br/>';
	echo Yii::app()->user->getFlash('savefile');
}
/*
 * end controller - в реальной работе контроллер подключить отдельным файлом require
 */


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