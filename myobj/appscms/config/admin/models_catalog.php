<?php
$models = array(
	'catalog_category' => array(
		'namemodel' => 'CatalogCategory',
		'relation' => array(
			'options'=>array('catalog_option','categories'),
			'parent'=>array('catalog_category','parent'),
		)
	),
	'catalog_option' => array(
		'namemodel' => 'CatalogOption',
		'relation' => array('params'=>array('catalog_option_param','option'),'categories'=>array('catalog_category','options'))
	),
	'catalog_option_param' => array(
		'namemodel' => 'CatalogOptionParam',
		'relation' => array('option'=>array('catalog_option','params'))
	),
	'graphic_sale' => array(
		'controller' => 'admin/dep_store/graphic_sale.php',
	),
);
Yii::app()->params['api_conf_models'] =  array_merge(Yii::app()->params['api_conf_models'],$models);
