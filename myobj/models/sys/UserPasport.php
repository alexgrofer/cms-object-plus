<?php
class UserPasport extends AbsBaseModel
{
	public $firstname;
	public $lastname;
	public $user_id;

	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
	public function relations()
	{
		return array(
			'user'=>array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}
	public function customRules() {
		return array(
			array('firstname, lastname', 'required'),
		);
	}
	public function customElementsForm() {
		return array(
			'firstname'=>array(
				'type'=>'text',
			),
			'lastname'=>array(
				'type'=>'text',
			),
		);
	}
}