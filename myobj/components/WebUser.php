<?php
class WebUser extends CWebUser {
	protected $_groups = null;
	protected $_model = null;

	public function getGroups() {
		if ($this->_groups === null) {
			if ($user = $this->getObjUser()) {
				$this->_groups = $user->groups;
			}
		}
		return $this->_groups;
	}

	public function getObjUser() {
		if (!$this->isGuest && $this->_model === null) {
			if(Yii::app()->controller->id=='admin') {
				$this->_model = UserAdmin::model()->findByPk($this->id);
			}
			else {
				$this->_model = User::model()->findByPk($this->id);
			}
		}
		return $this->_model;
	}
}