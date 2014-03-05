<?php

$id_edit_news = $this->dicturls['paramslist'][2];

$model_obj = uClasses::getclass('news_example')->objects()->findByPk($id_edit_news);


if(!$model_obj) {
	echo '<p>not find element news</p>';
	return;
}

$nameClassModel = get_class($model_obj);

$this->pageTitle='edit info page'.$model_obj->name;

$paramsQueryPostModel = yii::app()->getRequest()->getPost($nameClassModel);
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
	//возьмем загруженные файл
	$files = CUploadedFile::getInstancesByName($nameClassModel.'[image]');
	if(count($files)) {
		//start load file
		/* @var CStoreFile $initFile */
		$initFile = yii::app()->storeFile->obj(EnumerationPluginStoreFile::DEF, null); //инициализируем новый объект нужным плагином(определит поведение)

		foreach($files as $file) {
			//следующий индекс используем для добавления новых файлов
			$indexEdit = $initFile->objPlugin->getNextIndex();

			//будет рандомное название
			$initFile->set_IsRand(true,$indexEdit);

			//сам файл
			$initFile->set_File($file,$indexEdit);

			//логическая папка(news-папка плагина будет учтена/id объекта это название)
			$initFile->set_Path($model_obj->id,$indexEdit); //установить относительную папку

			//установить кроп

			//установить архивацию

			//следующий индекс файла
			$indexEdit++;
		}

		$initFile->save(); //сохранит объект файла (происходит измерение EArray b загрузка новых файлов)

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