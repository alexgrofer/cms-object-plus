<?php
//echo '<br/>'.$this->params['testParam'];
/**
 * -Тестируем постраничность
 * --стандартная
 * --аякс
 * --аякс другой тип
 * -опробовать dataprovider со стандартной pagination и Grid
 */
$objects = $modelTestHeader->findAll();
foreach($objects as $objHeaderTest) {
	echo CHtml::link('obj - '.$objHeaderTest->id, $this->createUrl('header_edit', array('id'=>$objHeaderTest->id))).'<br>';
}