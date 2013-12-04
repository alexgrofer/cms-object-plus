<?php
$idnews = 3;
$model_obj = uClasses::getclass('news_example')->objects()->findbypk($idnews); //найти

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
	/*
	 * показать загруженные файлы
	 * загрузка единичего файла если передаю не массив
	 * показать сами файлы виджет ниже формы
	 */
	$model_obj->save();
	//сохраним файлы если они были добавленны
	if(isset($_FILES['EmptyForm[image]'])) {
		/* @var CStoreFile $initFile */
		$initFile = yii::app()->storeFile->obj();
		$initFile->setFolderAll('news'); //установить главную папку для загрузки (относительно настройки плагина)
		$initFile->isRandAll = true; //все файлы будут названны рандомно

		$initFile->fileAll = 'EmptyForm[image]'; //загрузит пачкой все файлы если EmptyForm[image] массив
		$initFile->path = $model_obj->id; //логическая папка файлов новости $_FILES['EmptyForm[image]'] если это множество файлов
		$initFile->save();
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
 * end controller - в реальной работе контроллер вынести в отдельное представление
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