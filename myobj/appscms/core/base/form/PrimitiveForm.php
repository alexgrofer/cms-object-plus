<?php
namespace MYOBJ\appscms\core\base\form;

class PrimitiveForm extends \CComponent {
	private $_name;
	public function getName() {
		return $this->_name;
	}

	public static function create($name) {
		$obj = new self(null,null);
		$obj->_name = $name;
		return $obj;
	}

	private $_rules;
	public function getRules() {return $this->_rules;}
	public function rules($arrRules) {
		$this->_rules = $arrRules;
		return $this;
	}

	private $_html;
	public function getHtml() {return $this->_html;}
	public function html() {
		return $this;
	}

	private $_labels;
	public function getLabels() {return $this->_labels;}
	public function labels() {
		return $this;
	}
}
