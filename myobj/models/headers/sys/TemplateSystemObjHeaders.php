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

	public function rules() {
		return array(
			array('name, path', 'required'),
			array('name, path, desc', 'length', 'max'=>255),
			array('path', 'match', 'not' => true, 'pattern' => '/\.php\s*$/'),
		);
	}

	public function relations() {
		$relations = parent::relations();

		$relations['navigationsDef'] = array(self::HAS_MANY, 'NavigateSystemObjHeaders', 'template_default_id');
		$relations['navigationsMobileDef'] = array(self::HAS_MANY, 'NavigateSystemObjHeaders', 'template_mobile_default_id');
		$relations['handles'] = array(self::HAS_MANY, 'HandleSystemObjHeaders', 'template_id');

		return $relations;
	}

	protected function defaultElementsForm() {
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

