<?php
class TestAbsBaseModel extends AbsBaseModel {

	public $param1;

	public function confEArray() {
		return array(
			'param1' => array(
				'type'=>'serialize', //json
				'paramsRules'=>array(
					'p_EArray_1' => array(
						array('required'),
						array('length', 'max'=>5),
					),
				)
			),
		);
	}
}
