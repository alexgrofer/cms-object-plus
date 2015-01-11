<?php
class Ugroup extends AbsBaseModel {
	public $name;

	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
	public function relations()
	{
		return array(
			'users'=>array(self::MANY_MANY, 'User', 'setcms_user_ugroup(group_id,user_id)'),
		);
	}
	protected function defaultRules() {
		$rules = parent::defaultRules();
		return array_merge($rules, array(
			array('name', 'required'),
		));
	}
}