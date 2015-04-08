<?php
class CatalogOption extends AbsBaseModel
{
	public $name;
	public $type; // булево (да,нет,неважно), чекбокс - список, радио - список, диапазон
	public $conf;

	public function tableName()
	{
		return 'cmsplus_catalog_option';
	}
	public function relations()
	{
		return array(
			'categories'=>array(self::MANY_MANY, 'catalogCategory', 'cmsplus_catalog_category_to_option(option_id, category_id)'),
			'params'=>array(self::HAS_MANY, 'catalogOptionParam', 'id_option'),
		);
	}

	public function rules()
	{
		return array(
			array('name, type', 'required'),
			array('name, conf', 'length', 'max'=>225),
			array('type', 'numerical', 'max'=>9, 'integerOnly'=>true),
		);
	}

	public function elementsForm() {
		return array(
			'name'=>array(
				'type'=>'text',
			),
			'type'=>array(
				'type'=>'text',
			),
			'conf'=>array(
				'type'=>'textarea',
			),
		);
	}
}