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
	public $codename;
	//key
	public $template_id;
	public $view_id;
	// end

	public $isitlines = false;

	public function rules() {
		return array(
			array('codename', 'required'),
			array('codename', 'length', 'max'=>255),
			array('codename', 'match', 'not' => true, 'pattern' => '/\s+/'),

			array('template_id', 'exist', 'className' => 'TemplateSystemObjHeaders', 'attributeName'=>TemplateSystemObjHeaders::model()->primaryKey(), 'allowEmpty'=>false),
			array('view_id', 'exist', 'className' => 'ViewSystemObjHeaders', 'attributeName'=>ViewSystemObjHeaders::model()->primaryKey(), 'allowEmpty'=>false),
		);
	}

	public function relations() {
		$relations = parent::relations();

		$relations['template'] = array(self::BELONGS_TO, 'TemplateSystemObjHeaders', 'template_id');
		$relations['view'] = array(self::BELONGS_TO, 'ViewSystemObjHeaders', 'view_id');

		return $relations;
	}
}

