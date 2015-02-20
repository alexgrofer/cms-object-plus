<?php
namespace MYOBJ\appscms\src;

class DCMap extends \CMap {
	public static function create($data=null,$readOnly=false) {
		return new static($data,$readOnly);
	}

	public function add($key,$value) {
		parent::add($key,$value);
		return $this;
	}
}
