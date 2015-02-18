<?php
class TestAbsBaseModel extends AbsBaseModel {
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

	public function typesEArray() {
		return array(
			'content_e_array_1' => array(
				'elements' => array(
					'param_1',
					'param_2',
				),
				'conf' => array(
					'isMany'=>false,
				),
				'rules'=>array(
					'param_1'=>array(
						array('length', 'min'=>2,  'max'=>5),
					),
					'*'=>array(
						array('required'),
						array('length', 'min'=>3, 'max'=>12),
					),
				),
				'elementsForm' => array(
					'param_2'=>array(
						'type'=>'text',
					),
					'*'=>array(
						'type'=>'textarea',
					),
				),
			)
		);
	}
}
