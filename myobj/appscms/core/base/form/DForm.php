<?php
namespace MYOBJ\appscms\core\base\form;

/**
 * Динамическая форма
 * Class DForm
 * @package MYOBJ\appscms\src
 */
class DForm extends \CFormModel {
	private $_custom_rules=array();
	public function rules() {
		return $this->_custom_rules;
	}

	private $_custom_attributeLabels=array();
	public function attributeLabels() {
		return $this->_custom_attributeLabels;
	}

	private $_custom_HTMLElements=array();
	public function HTMLElements() {
		return $this->_custom_HTMLElements;
	}

	public static function create(PrimitiveFormList $elementsConf, $scenario='') {
		return new static($scenario);
	}
}
