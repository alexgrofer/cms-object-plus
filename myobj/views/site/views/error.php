<?php
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - Error';
?>
<p class="bg-danger">
<?=CHtml::encode($message); ?>
</p>
