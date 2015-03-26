<?php
class CatalogOptionParam extends AbsBaseModel
{
	public $val;
	public $id_option;

	public function tableName()
	{
		return 'cmsplus_catalog_option_param';
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
			array('val', 'required'),
			array('val', 'length', 'max'=>225),
		);
	}

	public function defaultElementsForm() {
		return array(
			'val'=>array(
				'type'=>'text',
			),
		);
	}
}