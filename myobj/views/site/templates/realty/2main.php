<?php
//может это вынести в отдельный файл? но тогда нельзя добавлять какието то скрипты в зависимости от страницы
//в любом случае
$cs = Yii::app()->getClientScript();
$cs->coreScriptPosition=CClientScript::POS_HEAD; //load into tag
$assetsFolder=Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.myobj.assets'));
$cs->registerCoreScript('jquery');
$cs->registerScriptFile($assetsFolder.'/bootstrap/js/bootstrap.min.js');
$cs->registerCssFile($assetsFolder.'/bootstrap/css/bootstrap.min.css');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta content="text/html; charset=utf-8" http-equiv="content-type">
	<?php
	echo '<title>'.'test'.'</title>';
	/*
	if($description=Yii::app()->appcms_>nav->getParam('description') echo '<meta content="" name="description">';
	if($keywords=Yii::app()->appcms_>nav->getParam('keywords') echo '<meta content="" name="keywords">';
	*/
	?>
</head>
<body>
<?php echo $content?>
</body>
</html>