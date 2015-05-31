<?php
class UserAdmin extends AbsBaseModel
{
	public $login;
	public $password;
	public $email;

	public function tableName()
	{
		return 'cmsplus_user_admin';
	}

	public function relations()
	{
		return array(
			'groups'=>array(self::MANY_MANY, 'UgroupAdmin', 'cmsplus_user_ugroup_admin(user_id, group_id)'),
		);
	}
	public function rules() {
		return array(
			array('login, password, email', 'required'),
			array('login, email', 'length', 'max'=>255),

			array('login', 'unique', 'className'=>get_class($this), 'attributeName'=>'login', 'allowEmpty'=>false),
			array('email', 'unique', 'className'=>get_class($this), 'attributeName'=>'email', 'allowEmpty'=>false),
		);
	}

	public function elementsForm() {
		return array(
			'login'=>array(
				'type'=>'text',
			),
			'email'=>array(
				'type'=>'text',
			),
			'password'=>array(
				'type'=>'password',
			),
	);
	}
}