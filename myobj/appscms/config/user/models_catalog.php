<?php
$models = array(
	//STORE
	'depcat' => array(
		'namemodel' => 'DepCatCategory',
		'relation' => array('depcatoption'),
	),
	'depcatoption' => array(
		'namemodel' => 'DepCatOption',
		'relation' => array('depcatoptionparams'),
	),
	'depcatoptionparams' => array(
		'namemodel' => 'DepCatOptionParam',
	),
	'graphic_sale' => array(
		'controller' => 'admin/dep_store/graphic_sale.php',
	),
);
Yii::app()->params['api_conf_models'] =  array_merge(Yii::app()->params['api_conf_models'],$models);
