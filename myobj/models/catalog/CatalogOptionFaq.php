<?php
class CatalogOptionFaq extends AbsBaseModel
{
	public $text;

	public function tableName()
	{
		return 'cmsplus_catalog_option_faq';
	}
	public function relations()
	{
		return array(
			'option'=>array(self::HAS_ONE, 'catalogOption', 'id_option'),
		);
	}

	public function defaultRules()
	{
		return array(
			array('text', 'required'),
		);
	}

	public function defaultElementsForm() {
		return array(
			'text'=>array(
				'type'=>'textarea',
			),
		);
	}
}