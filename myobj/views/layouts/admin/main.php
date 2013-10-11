<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" >
</head>
<style>
form .row {padding:0;margin-left:0}
input, select {height: auto !important; }
.padding_10 {padding:10px}
</style>
<body>
<div class="padding_10">
<?php if(!Yii::app()->user->isGuest) { ?>
<script>
$(function() {
    $( "#myMenu" ).menu();
});
</script>
<div class="horizontal-menu">
<?php
$this->widget('zii.widgets.CMenu',array(
    'id'=>'myMenu',
    //'htmlOptions'=>array('class'=>'nav'),
    'items'=>yii::app()->appcms->config['controlui']['menu'],
    'activateParents'=>true,
));
?>
</div>
<?php
$this->renderClip('header');
}?>
<?php echo $content; ?>
</div>
</body>
</html>
