<?php
namespace MYOBJ\appscms\src;

abstract class AbsEnumeration extends CComponent {
	//const PARAM_1 = 0;

	private $_id;

	final public static function create($name) {
		$obj = new static;
		$obj->_id = $name;
		return $obj;
	}

	abstract static public function fields();
	/*
	public function fields() {
		return array(
			self::PARAM_1 => 'param1',
		);
	}
	*/

	final public function getId() {
		$this->_id;
	}
}
