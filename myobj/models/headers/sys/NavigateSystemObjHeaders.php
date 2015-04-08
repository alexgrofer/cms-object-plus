<?php

/**
 * Класс заголовка который хранится в отдельной таблице, А НЕ В systemobjheaders
 * Возможно расширить класс отдельной бизнес логикой свойстванной только этому классу(типу) заголовка
 * Class NavigateSystemObjHeaders
 */
class NavigateSystemObjHeaders extends AbsBaseHeaders {
	public $is_independent = true;
	public $uclass_id=5;
	//columns DB
	public $name;
	public $desc;
	public $controller;
	public $action;
	public $sort;
	public $show;
	//key
	public $parent_id;
	public $template_default_id;
	// end

	public $isitlines = false;

	public function rules() {
		return array(
			array('name, controller', 'required'),
			array('name, desc, controller, action', 'length', 'max'=>255),
			array('action', 'default', 'value'=>null),
			array('sort', 'default', 'value'=>0),
			array('show', 'boolean'),

			array('parent_id', 'exist', 'className' => get_class($this), 'attributeName'=>'id', 'allowEmpty'=>true),
			array('parent_id', 'default', 'value'=>null),

			array('template_default_id', 'exist', 'attributeName'=>TemplateSystemObjHeaders::model()->primaryKey(), 'className' => 'TemplateSystemObjHeaders', 'allowEmpty'=>true),
			array('template_default_id', 'default', 'value'=>null),

			array('template_mobile_default_id', 'exist', 'attributeName'=>TemplateSystemObjHeaders::model()->primaryKey(), 'className' => 'TemplateSystemObjHeaders', 'allowEmpty'=>true),
			array('template_mobile_default_id', 'default', 'value'=>null),
		);
	}

	public function relations() {
		$relations = parent::relations();

		$relations['parent'] = array(self::BELONGS_TO, get_class($this), 'parent_id');

		$relations['templateDefault'] = array(self::BELONGS_TO, 'TemplateSystemObjHeaders', 'template_default_id');
		$relations['templateMobileDefault'] = array(self::BELONGS_TO, 'TemplateSystemObjHeaders', 'template_mobile_default_id');
		$relations['params'] = array(self::HAS_MANY, 'ParamSystemObjHeaders', 'navigate_id');

		return $relations;
	}

	protected function foreign_on_delete_cascade() {
		return array(
			'params',
		);
	}
}

