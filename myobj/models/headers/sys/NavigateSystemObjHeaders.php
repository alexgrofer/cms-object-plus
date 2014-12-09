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
	public $action_name;
	public $sort;
	public $parent_id;
	//key
	public $template_default_id;
	// end

	public $isitlines = false;

	protected function defaultRules() {
		$rules = parent::defaultRules();
		return $rules + array(
			array('name', 'required'),
			array('name, codename, action_name', 'length', 'max'=>255),
			array('codename, action_name', 'default', 'value'=>null),
			array('sort', 'default', 'value'=>0),

			array('parent_id', 'exist', 'className' => get_class($this), 'allowEmpty'=>true),
			array('parent_id', 'default', 'value'=>null),

			array('template_default_id', 'exist', 'className' => 'TemplateSystemObjHeaders', 'allowEmpty'=>true),
			array('template_default_id', 'default', 'value'=>null),
		);
	}

	public function relations() {
		$relations = parent::relations();

		$relations['templateDefault'] = array(self::BELONGS_TO, 'TemplateSystemObjHeaders', 'template_default_id');

		return $relations;
	}
}

