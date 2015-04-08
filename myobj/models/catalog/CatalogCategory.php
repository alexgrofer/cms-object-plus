<?php
class CatalogCategory extends AbsBaseModel
{
	public $name; //Телевизоры
	public $desc; //Телевизоры и плазменные панели
	public $codename;
	public $parent_id;

	public function tableName()
	{
		return 'cmsplus_catalog_category';
	}

	public function relations()
	{
		return array(
			'options'=>array(self::MANY_MANY, 'catalogOption', 'cmsplus_catalog_category_to_option(category_id, option_id)'),
			'parent'=>array(self::BELONGS_TO, get_class($this), 'parent_id'),
		);
	}

	public function rules() {
		return array(
			array('name, codename', 'required'),
			array('name, desc, codename', 'length', 'max'=>225),
			array('codename', 'match', 'not' => true, 'pattern' => '/\s+/'),
			array('parent_id', 'exist', 'attributeName'=>'id', 'className'=>get_class($this),'allowEmpty'=>true),
		);
	}

	public function defaultElementsForm() {
		return array(
			'name'=>array(
				'type'=>'text',
			),
			'desc'=>array(
				'type'=>'text',
			),
			'codename'=>array(
				'type'=>'text',
			),
			'parent_id'=>array(
				'type'=>'text',
			),
		);
	}
}