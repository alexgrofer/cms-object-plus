<?php
$cs = Yii::app()->getClientScript();
$cs->coreScriptPosition=CClientScript::POS_HEAD; //load into tag
$assetsFolder=Yii::app()->assetManager->publish(Yii::getPathOfAlias('MYOBJ.assets'));
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');
$cs->registerCssFile(
    Yii::app()->clientScript->getCoreScriptUrl().
    '/jui/css/base/jquery-ui.css'
);
//custom jquery-ui
$cs->registerCssFile($assetsFolder.'/jquery-ui/user-jquery-ui.css');
//$cs->registerCssFile($assetsFolder.'/jquery-ui/user-jquery-ui.js');

$cs->registerScriptFile($assetsFolder.'/bootstrap/js/bootstrap.min.js');
$cs->registerCssFile($assetsFolder.'/bootstrap/css/bootstrap.min.css');

$cs->registerScriptFile($assetsFolder.'/js/main.js');
$cs->registerCssFile($assetsFolder.'/css/main.css');
?>
<?php $this->beginContent('/admin/layouts/main')?>

<?php echo $content?>

<?php $this->endContent()?>