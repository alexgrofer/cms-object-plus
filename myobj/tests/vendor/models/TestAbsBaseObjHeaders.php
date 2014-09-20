<?php
class TestAbsBaseObjHeaders extends AbsBaseHeaders {
	public $param1;
	public $param2;

	public function customRules() {
		return array(
			array('param1, param2', 'safe'),
		);
	}
}
