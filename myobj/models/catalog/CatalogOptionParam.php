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
			'option'=>array(self::BELONGS_TO, 'catalogOption', 'id_option'),
		);
	}
	public function rules()
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