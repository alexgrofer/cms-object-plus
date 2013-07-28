<?php
class UserIdentity extends CUserIdentity
{
	public function authenticate()
	{
		$users=array(
			'admin'=>'admin',
		);
		if(!isset($users[$this->username])) {
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		}
		elseif($users[$this->username]!==$this->password) {
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		}
		else {
			// WORK GROUP CMS SYSTEM GROUPS array USER
			$groupsident = array();
			$groupsident[] = 'CC99CD08-A1BF-461A-B1FE-3182B24D2812'; // id group - admincms, Vp1 = 1 - value of the group user
			$this->setState('groupsident', $groupsident);
			/***********************************/
			$this->errorCode=self::ERROR_NONE;
	   }
		return !$this->errorCode;
	}
}