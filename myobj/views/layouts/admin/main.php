<?php
$cs = Yii::app()->getClientScript();
$cs->coreScriptPosition=CClientScript::POS_HEAD; //load into tag
$assetsFolder=Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.myobj.assets'));
$cs->registerCoreScript('jquery');

//$cs->registerScriptFile($assetsFolder.'/js/admin.main.js',CClientScript::POS_END);
//$cs->registerCssFile($assetsFolder.'/css/admin.main.css');
$cs->registerScriptFile($assetsFolder.'/bootstrap/js/bootstrap.min.js');
$cs->registerCssFile($assetsFolder.'/bootstrap/css/bootstrap.min.css');

$urladm = Yii::app()->createUrl('myobj/admin');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
</head>
<style>
form .row {padding:0;margin-left:0}
input, select {height: auto !important; }
</style>
<body>
<div style="padding: 10px">
<?php if(!Yii::app()->user->isGuest) {?>
<div class="well">
    <a href="<?php echo $urladm?>/objects/models/classes/">classes</a> | 
    <a href="<?php echo $urladm?>/objects/models/properties/">properties</a> |-----| 
<div class="btn-group">
  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    mvc
    <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="<?php echo $urladm?>/objects/class/templates_sys/">templates</a></li>
    <li><a href="<?php echo $urladm?>/objects/class/views_sys/">views</a></li>
    <li><a href="<?php echo $urladm?>/objects/class/navigation_sys/">nav</a></li>
    <li><a href="<?php echo $urladm?>/objects/class/controllersnav_sys/">controllers</a></li>
  </ul>
</div>
    <a href="<?php echo $urladm?>/objects/class/groups_sys/">groups</a> |-----|
    <?php echo $this->getMenuhtml(); ?>
    <a href="<?php echo $urladm?>/logout/">logout (<?php echo Yii::app()->user->name;?>)</a>
</div>
<?php echo $this->getMenuhtmlSub(); ?>
<?php
$this->renderClip('header');
}?>
<?php echo $content; ?>
</div>
<script>

</script>
</body>
</html>
