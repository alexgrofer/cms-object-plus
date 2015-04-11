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
	public $group_id;
	// end

	public $isitlines = false;

	public function rules() {
		return array(
			array('name, path', 'required'),
			array('name, path, desc', 'length', 'max'=>255),
			array('path', 'match', 'not' => true, 'pattern' => '/\.php\s*$/'),
		);
	}

	public function relations() {
		$relations = parent::relations();

		$relations['handles'] = array(self::HAS_MANY, 'HandleSystemObjHeaders', 'view_id');
		$relations['group'] = array(self::BELONGS_TO, 'Ugroup', 'group_id');

		return $relations;
	}

	public function elementsForm() {
		return array(
			'name'=>array(
				'type'=>'text',
			),
			'path'=>array(
				'type'=>'text',
			),
			'desc'=>array(
				'type'=>'textarea',
			),
		);
	}
}

