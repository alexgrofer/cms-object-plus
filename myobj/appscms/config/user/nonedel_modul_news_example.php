<?php
$nonedel_user = array(
	'classes' => array(
		'news_example',
		'news_section_example',
	),
	'prop' => array('text_news_example','annotation_news_example','codename_news_section_example'),
);
Yii::app()->params['api_conf_nonedel'] =  array_merge_recursive(Yii::app()->params['api_conf_nonedel'],$nonedel_user);