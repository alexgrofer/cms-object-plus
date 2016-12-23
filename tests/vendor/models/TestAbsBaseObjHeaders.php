<?php
class TestAbsBaseObjHeaders extends AbsBaseHeaders {
	public $param1;
	public $param2;

	public function rules() {
		return array(
			array('param1, param2', 'safe'),
		);
	}
}
