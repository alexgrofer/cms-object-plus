<?php
class UTestTableHM extends AbsBaseModel
{
	public $name;
	public $obj_id;

	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
	public function relations()
	{
		return array(
			'myobjheader'=>array(self::BELONGS_TO, 'myObjHeaders', 'obj_id'),
		);
	}
	public function customRules() {
		return array(
			array('name', 'required'),
		);
	}
	public function customElementsForm() {
		return array(
			'name'=>array(
				'type'=>'text',
			)
		);
	}
}