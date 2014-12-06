<?php

/**
 * Класс заголовка который хранится в отдельной таблице, А НЕ В systemobjheaders
 * Возможно расширить класс отдельной бизнес логикой свойстванной только этому классу(типу) заголовка
 * Class HandleSystemObjHeaders
 */
class HandleSystemObjHeaders extends AbsBaseHeaders {
	public $is_independent = true;
	public $uclass_id=4;
	//columns DB
	public $name;
	//key
	public $template_id;
	public $view_id;
	// end

	public $isitlines = false;

	protected function defaultRules() {
		return array(
			array('name', 'required'),
			array('name', 'length', 'max'=>255),

			array('template_id', 'exist', 'className' => 'TemplateSystemObjHeaders', 'allowEmpty'=>false),
			array('view_id', 'exist', 'className' => 'ViewSystemObjHeaders', 'allowEmpty'=>false),
		);
	}

	public function relations() {
		$relations = parent::relations();

		$relations['template'] = array(self::BELONGS_TO, 'TemplateSystemObjHeaders', 'template_id');
		$relations['view'] = array(self::BELONGS_TO, 'ViewSystemObjHeaders', 'view_id');

		return $relations;
	}
}

