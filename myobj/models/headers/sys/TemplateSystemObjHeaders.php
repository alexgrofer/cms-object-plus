<?php

/**
 * Класс заголовка который хранится в отдельной таблице, А НЕ В systemobjheaders
 * Возможно расширить класс отдельной бизнес логикой свойстванной только этому классу(типу) заголовка
 * Class TemplateSystemObjHeaders
 */
class TemplateSystemObjHeaders extends AbsBaseHeaders {
	public $is_independent = true;
	public $uclass_id=3;
	//columns DB
	public $name;
	public $desc;
	public $path;
	// end

	public $isitlines = false;

	protected function defaultRules() {
		return array(
			array('name, path', 'required'),
			array('name, path, desc', 'length', 'max'=>255),
		);
	}
}

