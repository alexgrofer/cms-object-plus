<?php
class Ugroup extends AbsBaseModel {
	public $name;
	public $codename;

	public function tableName()
	{
		return 'cmsplus_'.strtolower(get_class($this));
	}
	public function relations()
	{
		return array(
			'users'=>array(self::MANY_MANY, 'User', 'cmsplus_user_ugroup(group_id,user_id)'),
			'showView'=>array(self::HAS_MANY, 'ViewSystemObjHeaders', 'group_id'),
		);
	}
	public function rules() {
		return array(
			array('name', 'required'),
		);
	}
}