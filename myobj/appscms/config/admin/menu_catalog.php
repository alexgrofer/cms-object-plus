<?php
return array(
	'catalog'=>array('label'=>'catalog',
		'items'=>array(
			'category'=>array('label'=>'category', 'url'=>array('admin/objects/models/catalog_category')),
			'option'=>array('label'=>'option', 'url'=>array('admin/objects/models/catalog_option'),
				'items'=>array(
					'option params'=>array('label'=>'option params', 'url'=>array('admin/objects/models/catalog_option_param'))
				),
			),
			'graphic_sale'=>array('label'=>'graphic sales', 'url'=>array('admin/objects/ui/graphic_sale')),
		),
	),
);
