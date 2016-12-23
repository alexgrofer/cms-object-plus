<?php
namespace MYOBJ\appscms\src;

class DList extends \CList {
	public static function create($data=null,$readOnly=false) {
		return new static($data,$readOnly);
	}

	public function add($item) {
		parent::add($item);
		return $this;
	}
}
