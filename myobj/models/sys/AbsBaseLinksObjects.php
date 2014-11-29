<?php
abstract class AbsBaseLinksObjects extends AbsBaseModel {
	public $from_obj_id;
	public $from_class_id;
	public $to_obj_id;
	public $to_class_id;

	public function primaryKey() {
		return array(
			'from_obj_id',
			'from_class_id',
			'to_obj_id',
			'to_class_id',
		);
	}

	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

	public function relations() {
		return array(
			'uclass_to'=>array(self::BELONGS_TO, 'uClasses', 'from_class_id'),
			'uclass_from'=>array(self::BELONGS_TO, 'uClasses', 'to_class_id'),
		);
	}

	public function customRules()
	{
		return array(
			array('from_obj_id, from_class_id, to_obj_id, to_class_id ', 'unsafe'),
		);
	}
}
