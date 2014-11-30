<?php
abstract class AbsBaseLines extends AbsBaseModel
{
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

	public $uptextfield; //models.TextField(blank=True)
	public $upcharfield; //models.CharField(max_length=255,blank=True)
	public $uptimefield; //models.TimeField(blank=True,null=True)
	public $updatefield; //models.DateField(blank=True,null=True)
	public $upintegerfield; //models.IntegerField(blank=True,null=True)
	public $upfloatfield; //models.FloatField(blank=True,null=True)
	public $property_id;
	public $header_id;

	public function relations()
	{
		$namemodellines = str_replace('Lines','',get_class($this));

		$arr_relationsdef = array('property'=>array(self::BELONGS_TO, 'objProperties', 'property_id'));

		$arr_relationsdef['header']=array(self::BELONGS_TO, $namemodellines.'Headers', 'header_id');

		return $arr_relationsdef;
	}

	protected function defaultRules()
	{
		return array(
			array('upcharfield', 'length', 'max'=>255),
			array('uptextfield, upcharfield', 'default', 'value'=>''),
			array('uptimefield, updatefield, upintegerfield, upfloatfield', 'default', 'value'=>null),
		);
	}
}
