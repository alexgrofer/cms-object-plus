<?php
$cs = Yii::app()->getClientScript();
$cs->coreScriptPosition=CClientScript::POS_HEAD; //load into tag
$assetsFolder=Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.myobj.assets'));
$cs->registerCoreScript('jquery');
//$cs->registerScriptFile($assetsFolder.'/js/main.js',CClientScript::POS_END); //load end tag body
//$cs->registerCssFile($assetsFolder.'/css/main.css');

$urladm = Yii::app()->createUrl('myobj/admin');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
</head>
<style>
form .row {padding:0;margin-left:0}
input, select {height: auto !important; vertical-align:top !important;}
.cgreen {color: green}
.cred {color: red}
.errorMessage {color: red}
.phor2px {padding: 5px !important}
</style>
<body>
<div style="padding: 10px">
<?php if(!Yii::app()->user->isGuest) {?>
<p class="well"><a href="<?=$urladm?>/objects/models/classes/">classes</a> | <a href="<?=$urladm?>/objects/models/properties/">properties</a> |-----| <a href="<?=$urladm?>/objects/class/templates_sys/">templates</a> | <a href="<?=$urladm?>/objects/class/views_sys/">views</a> | <a href="<?=$urladm?>/objects/class/navigation_sys/">nav</a> | <a href="<?=$urladm?>/objects/class/groups_sys/">groups</a> |-----| <a href="<?=$urladm?>/objects/models/">MODELS</a> | <a href="<?=$urladm?>/objects/ui/">UI</a> | ---- | <a href="<?=$urladm?>/logout/">logout (<?php echo Yii::app()->user->name;?>)</a></p>

<?php
$this->renderClip('header');
}?>
<?php echo $content; ?>
</div>
</body>
</html>