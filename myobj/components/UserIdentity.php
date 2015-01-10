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
		else {
			$this->_id=$record->primaryKey;
			$this->setState('title', ''); //https://ru.wikipedia.org/wiki/HMAC
			$this->errorCode=self::ERROR_NONE;
		}
		return !$this->errorCode;
	}

	public function getId() {
		return $this->_id;
	}
}