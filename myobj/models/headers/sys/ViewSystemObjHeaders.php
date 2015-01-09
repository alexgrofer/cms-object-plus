<?php

/**
 * Класс заголовка который хранится в отдельной таблице, А НЕ В systemobjheaders
 * Возможно расширить класс отдельной бизнес логикой свойстванной только этому классу(типу) заголовка
 * Class ViewSystemObjHeaders
 */
class ViewSystemObjHeaders extends AbsBaseHeaders {
	public $is_independent = true;
	public $uclass_id=2;
	//columns DB
	public $name;
	public $desc;
	public $path;
	// end

	public $isitlines = false;

	protected function defaultRules() {
		$rules = parent::defaultRules();
		return array_merge($rules, array(
			array('name, path', 'required'),
			array('name, path, desc', 'length', 'max'=>255),
		));
	}

	public function relations() {
		$relations = parent::relations();

		$relations['handles'] = array(self::HAS_MANY, 'HandleSystemObjHeaders', 'view_id');

		return $relations;
	}
}
