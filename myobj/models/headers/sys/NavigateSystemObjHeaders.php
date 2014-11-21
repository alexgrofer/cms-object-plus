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
	public $template_default_id;
	// end

	public $isitlines = false;

	public function customRules() {
		return array(
			array('name', 'required'),
			array('name, codename, action_name', 'length', 'max'=>255),
			array('codename, action_name', 'default', 'value'=>null),
			array('sort', 'default', 'value'=>0),

			array('parent_id', 'unique', 'className' => get_class(self)),
			array('parent_id', 'default', 'value'=>null),

			array('template_default_id', 'unique', 'className' => 'TemplateSystemObjHeaders'),
			array('template_default_id', 'default', 'value'=>null),
		);
	}

	public function relations() {
		$relations = parent::relations();

		$relations['templateDefault'] = array(self::BELONGS_TO, 'TemplateSystemObjHeaders', 'TemplateSystemObjHeaders');

		return $relations;
	}
}

