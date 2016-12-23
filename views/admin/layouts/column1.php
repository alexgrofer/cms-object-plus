<?php
$cs = Yii::app()->getClientScript();
$cs->coreScriptPosition=CClientScript::POS_HEAD; //load into tag
$assetsFolder=Yii::app()->assetManager->publish(Yii::getPathOfAlias('MYOBJ.assets.admin'));
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');
$cs->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl().
    '/jui/css/base/jquery-ui.css'
);
$cs->registerScriptFile($assetsFolder.'/bootstrap/js/bootstrap.min.js');
$cs->registerCssFile($assetsFolder.'/bootstrap/css/bootstrap.min.css');

$cs->registerScriptFile($assetsFolder.'/js/main.js');
$cs->registerCssFile($assetsFolder.'/css/main.css');
?>
<?php $this->beginContent('/admin/layouts/main')?>

<?=$content?>

<?php $this->endContent()?>