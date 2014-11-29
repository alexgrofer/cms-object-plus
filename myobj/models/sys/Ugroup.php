<?php
class Ugroup extends AbsBaseModel
{
	public $name;
	public $guid;

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
	public function customRules() {
		return array(
			array('name', 'required'),
		);
	}
	public function defaultElementsForm() {
		return array(
			'name'=>array(
				'type'=>'text',
			),
		);
	}
	protected function beforeSave() {
		if(parent::beforeSave()!==false) {
			if($this->isNewRecord) {
				$this->guid = apicms\utils\GUID();
			}
			return true;
		}
		else return parent::beforeSave();
	}

}