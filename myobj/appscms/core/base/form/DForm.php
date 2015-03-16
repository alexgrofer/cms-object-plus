<?php
namespace MYOBJ\appscms\core\base\form;

/**
 * Динамическая форма
 * Class DForm
 * @package MYOBJ\appscms\src
 */
class DForm extends \CFormModel {
	protected $attributeNamesCustom=array();

	private $_custom_rules=array();
	public function rules() {
		return $this->_custom_rules;
	}

	/**
	 * Добавляет новый атрибут к форме и правило для него
	 * @param $attributeName
	 * @param $rule
	 * @return $this
	 */
	public function addAttributeRule($name, $rule, $initVal=null) {
		$this->_custom_rules[] = array_merge(array($name), is_array($rule)?$rule:array($rule));

		$this->attributeNamesCustom[] = $name;
		$this->$name = $initVal;

		return $this;
	}
	public function __set($name, $value) {
		if(in_array($name, $this->attributeNamesCustom)) {
			$this->$name = $value;
		}
		else parent::__set($name, $value);
	}

	private $_custom_attributeLabels=array();
	public function attributeLabels() {
		return $this->_custom_attributeLabels;
	}
	public function addAttributeLabel($name,$lab) {
		$this->_custom_attributeLabels[$name] = $lab;
		return $this;
	}

	public static function create($scenario='') {
		$objDForm = new static($scenario);
		return $objDForm;
	}
	public static function createOfModel(\CModel $model) {
		$objDForm = new static($model->getScenario());
		foreach($model->rules() as $rule) {
			$names = explode(',',array_shift($rule));
			foreach($names as $name) {
				$normName = trim($name);$objDForm->addAttributeRule($normName, $rule, $model->$normName);
			}
		}
		$objDForm->_custom_attributeLabels = $model->attributeLabels();
		return $objDForm;
	}

	public function attributeNames() {
		return $this->attributeNamesCustom;
	}
}
