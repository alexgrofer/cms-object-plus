<?php
namespace MYOBJ\appscms\src;

class DTypedList extends \CTypedList {
	public static function create($type=null) {
		if(!$type) $type = static::TYPE;
		return new static($type);
	}

	public function add($item) {
		parent::add($item);
		return $this;
	}
}
