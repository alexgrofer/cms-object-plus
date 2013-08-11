<?php
$cs = Yii::app()->getClientScript();
$cs->coreScriptPosition=CClientScript::POS_HEAD; //load into tag
$assetsFolder=Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.myobj.assets'));
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');

$cs->registerScriptFile($assetsFolder.'/js/main.js',CClientScript::POS_END);
$cs->registerCssFile($assetsFolder.'/css/main.css');
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
.padding_10 {padding:10px}
</style>
<body>
<div class="padding_10">
<?php if(!Yii::app()->user->isGuest) { ?>

	<div class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<div class="nav-collapse collapse navbar-responsive-collapse">
					<?php
					$this->widget('zii.widgets.CMenu',array(
						'id'=>'myMenu',
						'htmlOptions'=>array('class'=>'nav'),
						'items'=>yii::app()->appcms->config['controlui']['menu'],
					));
					?>
				</div><!-- /.nav-collapse -->
			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->

<?php
$this->renderClip('header');
}?>
<?php echo $content; ?>
</div>
</body>
</html>
