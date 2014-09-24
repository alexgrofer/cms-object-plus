<?php
class UserIdentity extends CUserIdentity {
	private $_id;
	public function authenticate() {
		$record=User::model()->findByAttributes(array('login'=>$this->username));
		if($record===null) {
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		}
		elseif($record->password!==md5($this->password)) {
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		}
		else
		{
			$this->_id=$record->primaryKey;
			// WORK groupsident CMS SYSTEM GROUPS array USER
			$groupsident = array();
			foreach($record->groups as $objgroup) {
				$groupsident[] = $objgroup->guid; // id group - admincms, Vp1 = 1 - value of the group user
			}
			$this->setState('groupsident', $groupsident);
			/***********************************/
			$this->errorCode=self::ERROR_NONE;
		}
		return !$this->errorCode;
	}

	public function getId() {
		return $this->_id;
	}
}