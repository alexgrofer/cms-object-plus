<?php
$components_user = array(
	'storeFiles'=>array(
		'class' =>'application.modules.myobj.components.storefiles.StoreFiles',
		'test8'=>'texttest'
	),
);

Yii::app()->params['api_conf_components'] =  array_merge_recursive(Yii::app()->params['api_conf_components'],$components_user);
