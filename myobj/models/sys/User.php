<?php
class User extends AbsBaseModel
{
	public $login;
	public $password;
	public $email;

	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
	public function relations()
	{
		return array(
			'userpasport'=>array(self::HAS_ONE, 'UserPasport', 'user_id'), // test
			'groups'=>array(self::MANY_MANY, 'Ugroup', 'setcms_user_ugroup(user_id, group_id)'),
		);
	}
	protected function defaultRules() {
		$rules = parent::defaultRules();
		return array_merge($rules, array(
			array('login, password, email', 'required'),
			array('login, password, email', 'length', 'max'=>255),
			array('email', 'email'),
			array('login, email', 'unique', 'className'=>get_class($this), 'allowEmpty'=>false, 'allowEmpty'=>false),
		));
	}
	protected function beforeSave() {
		if(parent::beforeSave()!==false) {
			if(!$this->isNewRecord) {
				$objuser = $this->model()->findByPk($this->primaryKey);
				if($objuser->password!=$this->password) {
					$this->password = md5($this->password);
				}
			}
			else {
				$this->password = md5($this->password);
			}
			return true;
		}
		else return parent::beforeSave();
	}
	protected function defaultElementsForm() {
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