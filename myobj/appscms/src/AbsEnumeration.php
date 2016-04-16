<?php
namespace MYOBJ\appscms\src;

/*
 * CategoryEnumeration::create(CategoryEnumeration::ID_PARAM_1)->getName();
 */
abstract class AbsEnumeration extends \CComponent {
	//const ID_PARAM_1 = 0;

	private $_id;
	private $_name;

	final public static function create($name) {
		$obj = new static;
		$obj->_id = $name;

		$fields = $obj->fields();
		$obj->_name = $fields[$obj->_id];
		return $obj;
	}

	/*
	public function fields() {
		return array(
			self::PARAM_1 => 'param1',
		);
	}
	*/
	abstract public function fields();

	final public function getId() {
		return $this->_id;
	}

	final public function getName() {
		return $this->_name;
	}
}
