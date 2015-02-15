<?php
$ui = array(
	//Независимые интерфейсы, к примеру вывести график продаж или другой интерфейс. В контроллере можно организовать шаблон, представление и т.д.
	'graphic_sale' => array(
		'controller' => array('default'=>'admin/dep_store/graphic_sale.php'),
		'group_read' => 'administrator',
		'group_write' => 'administrator',
	),
);
Yii::app()->params['api_conf_UI'] =  array_merge(Yii::app()->params['api_conf_UI'],$ui);