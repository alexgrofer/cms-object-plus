<?php
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - Error';
?>
<p class="bg-danger">
<?php echo CHtml::encode($message); ?>
</p>
