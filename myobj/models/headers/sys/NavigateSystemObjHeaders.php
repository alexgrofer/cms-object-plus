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
	public $codename;
	public $desc;
	public $controller;
	public $action;
	public $sort;
	public $show;

	public $is_smart_tmp;

	//key
	public $parent_id;
	public $template_default_id;
	// end

	public $isitlines = false;

	public function beforeValidate() {
		if(!parent::beforeValidate()) return false;

		//Когда контроллер указан экшен не должен быть пустым
		if($this->getAttribute('controller')!='') {
			$this->getValidatorList()->add(
				CValidator::createValidator('required', $this, 'action')
			);

			$this->setAttribute('codename', null);
		}
		else {
			$this->getValidatorList()->add(
				CValidator::createValidator('required', $this, 'codename')
			);

			$this->setAttribute('action', null);
		}

		return true;
	}

	public function rules() {
		return array(
			array('name', 'required'),
			array('codename, controller, action', 'default', 'value'=>null),

			array('name, codename, desc, controller, action', 'length', 'max'=>255),
			array('sort', 'default', 'value'=>0),
			array('show', 'boolean'),

			array('parent_id', 'default', 'value'=>null),

			array('template_default_id', 'exist', 'attributeName'=>TemplateSystemObjHeaders::model()->primaryKey(), 'className' => 'TemplateSystemObjHeaders', 'allowEmpty'=>true),
			array('template_default_id', 'default', 'value'=>null),

			array('template_mobile_default_id', 'exist', 'attributeName'=>TemplateSystemObjHeaders::model()->primaryKey(), 'className' => 'TemplateSystemObjHeaders', 'allowEmpty'=>true),
			array('template_mobile_default_id', 'default', 'value'=>null),

			array('is_smart_tmp', 'boolean'),
			array('is_smart_tmp', 'default', 'value'=>false),
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

