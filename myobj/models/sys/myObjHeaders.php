<?php
class myObjHeaders extends AbsBaseHeaders
{
	public $name; //models.CharField(max_length=255)
	public $content; //models.TextField(blank=True) --
	public $sort; //models.IntegerField(blank=True,null=True,default=0)
	public $bpublic; //models.BooleanField(blank=True) --
	//table links

	public function rules()
	{
		return array(
			array('name', 'required'),
			array('name', 'type', 'type'=>'string'),
			array('sort', 'default', 'value'=>0),
			array('bpublic', 'boolean'),
			array('bpublic', 'default', 'value'=>false),
			array('content', 'safe'),
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
			'bpublic'=>array(
				'type'=>'checkbox',
			)
		);
	}
}

