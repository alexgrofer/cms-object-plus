<?php
class UserPasport extends AbsBaseModel
{
	public $firstname;
	public $lastname;
	public $user_id;

	public function relations()
	{
		return array(
			'user'=>array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}
	public function rules() {
		return array(
			array('firstname, lastname', 'required'),
		);
	}
}