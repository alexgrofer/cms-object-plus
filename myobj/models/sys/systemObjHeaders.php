<?php
class systemObjHeaders extends AbsBaseHeaders
{
	public $name; //models.CharField(max_length=255)
	public $content; //models.TextField(blank=True)
	public $sort; //models.IntegerField(blank=True,null=True,default=0)
	public $vp1; //models.CharField(max_length=255,blank=True)
	public $vp2; //models.CharField(max_length=255,blank=True)
	public $bp1; //models.BooleanField(blank=True) --

	public function customRules()
	{
		return array(
			array('name', 'required'),
			array('sort', 'default', 'value'=>0),
			array('vp1, vp2, content', 'default', 'value'=>''),
			array('bp1', 'boolean'),
			array('bp1', 'default', 'value'=>false),

		);
	}
	public function ElementsForm() {
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
			'bp1'=>array(
				'type'=>'checkbox',
			)
		);
	}
}

