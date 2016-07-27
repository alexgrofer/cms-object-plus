<?php
class TestAbsBaseModel extends AbsBaseModel {

	public $param1;

	public function confEArray() {
		return array(
			'param1' => array(
				'nameProp'=>'p_EArray_1',
				'type'=>'serialize', //json
				'rules'=>array(
					array('length', 'max'=>5),
					array('required'),
				),
			),
		);
	}
}
