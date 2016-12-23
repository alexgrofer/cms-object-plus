<?php
//echo '<br/>'.$this->params['testParam'];
echo CHtml::link('create new obj', yii::app()->createUrl('myobj/tests/header/edit')).'<br><br>';
/**
 * -Тестируем постраничность
 * --стандартная
 * --аякс
 * --аякс другой тип
 * -опробовать dataprovider со стандартной pagination и Grid
 */
$objects = $modelTestHeader->findAll();
foreach($objects as $objHeaderTest) {
	echo CHtml::link('obj - '.$objHeaderTest->id, $this->createUrl('edit', array('id'=>$objHeaderTest->id))).'<br>';
}