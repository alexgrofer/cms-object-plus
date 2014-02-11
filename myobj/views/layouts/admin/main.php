<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" >
</head>
<body>
<div class="padding_10">
<?php if(!Yii::app()->user->isGuest) { ?>
<script>
$(function() {
    $( "#myMenu" ).menu();
});
</script>
<style>
.myMenuSub > li {display: none}
.myMenuSub .active {display: block}
</style>
<div class="row-fluid">
    <div class="span6">
<div class="horizontal-menu">
<?php
$objMenu = $this->widget('zii.widgets.CMenu',array(
    'id'=>'myMenu',
    //'htmlOptions'=>array('class'=>'nav'),
    'items'=>yii::app()->appcms->config['controlui']['menu'],
    'activateParents'=>true,
));
?>
</div>
</div>
<div class="span6">
<?php
$arrSubMenu = array();
foreach($objMenu->items as $keyElem => $arItem) {
    if($arItem['active']) {
        $arrSubMenu = $arItem['items'];
        break;
    }
}
if($arrSubMenu) {
    $this->widget('zii.widgets.CMenu',array(
        'id'=>'myMenuSub',
        'htmlOptions'=>array('class'=>'myMenuSub'),
        'submenuHtmlOptions'=>array('class'=>'nav nav-pills'),
        'items'=>$arrSubMenu,
        'activateParents'=>true,
    ));
}
?>
    </div>
</div>
<?php
$this->renderClip('header');
}?>
<?php echo $content; ?>
</div>
</body>
</html>
