<?php

/**
 * Класс заголовка который хранится в отдельной таблице, А НЕ В systemobjheaders
 * Возможно расширить класс отдельной бизнес логикой свойстванной только этому классу(типу) заголовка
 * Class testObjHeaders
 */
class TestObjHeaders extends AbsBaseHeaders {
	public $is_independent = true;
	public $uclass_id=11;
	//columns DB
	public $param1;
	public $param2;
	public $param3;
	// end

	public $isitlines = false;

	public function rules() {
		return array(
			array('param1, param2, param3', 'required'),
			array('param2', 'length', 'min'=>3),
			array('param2', 'checkParam2', 'my_param'=>'test'),
			array('param3', 'length', 'min'=>4),
			array('param3', 'default', 'value'=>'12345'),
		);
	}

	public function checkParam2($attributeName, $paramsConf) {
		Yii::trace('checkParam2', 'MYOBJ_CMS_PLUS');

		if(strlen($this->$attributeName) < 7) {
			$this->addError($attributeName, 'error checkParam2 func');
		}
	}

	protected function defaultElementsForm() {
		return array(
			'param1'=>array(
				'type'=>'text',
			),
			'param2'=>array(
				'type'=>'text',
			),
			'param3'=>array(
				'type'=>'textarea',
			),
		);
	}
}

