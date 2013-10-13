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
.myMenuSub .active .active > a {color: red}
</style>
<div class="horizontal-menu">
<?php
$objMenu = $this->widget('zii.widgets.CMenu',array(
    'id'=>'myMenu',
    //'htmlOptions'=>array('class'=>'nav'),
    'items'=>yii::app()->appcms->config['controlui']['menu'],
    'activateParents'=>true,
));
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
        'items'=>$arrSubMenu,
        'activateParents'=>true,
    ));
}
?>
</div>
<?php
$this->renderClip('header');
}?>
<?php echo $content; ?>
</div>
</body>
</html>
