<?php

/**
 * Класс заголовка который хранится в отдельной таблице, А НЕ В systemobjheaders
 * Возможно расширить класс отдельной бизнес логикой свойстванной только этому классу(типу) заголовка
 * Class ViewSystemObjHeaders
 */
class ParamSystemObjHeaders extends AbsBaseHeaders {
	public $is_independent = true;
	public $uclass_id=6;
	//columns DB
	public $name;
	public $content;
	//key
	public $navigate_id;
	// end

	public $isitlines = false;

	protected function defaultRules() {
		$rules = parent::defaultRules();
		return array_merge($rules, array(
			array('name', 'required'),
			array('name', 'length', 'max'=>255),
			array('content', 'safe'),

			array('navigate_id', 'exist', 'className' => 'NavigateSystemObjHeaders', 'attributeName'=>NavigateSystemObjHeaders::model()->primaryKey(), 'allowEmpty'=>false),
		));
	}

	public function relations() {
		$relations = parent::relations();

		$relations['navigate'] = array(self::BELONGS_TO, 'NavigateSystemObjHeaders', 'navigate_id');

		return $relations;
	}
}