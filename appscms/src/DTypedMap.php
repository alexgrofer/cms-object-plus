<?php
namespace MYOBJ\appscms\src;

class DTypedMap extends \CTypedMap {
	public static function create($type) {
		return new static($type);
	}

	public function add($index,$item) {
		parent::add($index,$item);
		return $this;
	}
}
