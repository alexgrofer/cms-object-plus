<?php

/**
 * Класс заголовка который хранится в отдельной таблице, А НЕ В systemobjheaders
 * Возможно расширить класс отдельной бизнес логикой свойстванной только этому классу(типу) заголовка
 * Class HandleSystemObjHeaders
 */
class GroupSystemObjHeaders extends AbsBaseHeaders {
	public $is_independent = true;
	public $uclass_id=1;
	//columns DB
	public $name;
	public $identifier_role;
	// end

	public $isitlines = false;

	public function rules() {
		return array(
			array('name, identifier_role', 'required'),
			array('name, identifier_role', 'length', 'max'=>255),
		);
	}
}

