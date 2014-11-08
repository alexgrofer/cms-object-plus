<?php
class SystemObjHeaders extends AbsBaseHeaders
{
	public $name;
	public $content;
	public $sort;
	public $vp1;
	public $vp2;
	public $vp3;
	public $bp1;

	public function customRules()
	{
		return array(
			array('name', 'required'),
			array('sort', 'default', 'value'=>0),
			array('vp1, vp2, vp3, content', 'default', 'value'=>''),
			array('bp1', 'boolean'),
			array('bp1', 'default', 'value'=>false),

		);
	}
	public function customElementsForm() {
		return array(
			'name'=>array(
				'type'=>'text',
			),
			'content'=>array(
				'type'=>'textarea',
			),
			'sort'=>array(
				'type'=>'text',
			),
			'vp1'=>array(
				'type'=>'text',
			),
			'vp2'=>array(
				'type'=>'text',
			),
			'vp3'=>array(
				'type'=>'text',
			),
			'bp1'=>array(
				'type'=>'checkbox',
			)
		);
	}
}

