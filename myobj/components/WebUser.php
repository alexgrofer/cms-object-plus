<?php
class WebUser extends CWebUser {
	protected $_groups = null;
	protected $_model = null;

	public function getGroups() {
		if ($this->_groups === null) {
			if ($user = $this->getObjlUser()) {
				$this->_groups = $user->groups;
			}
		}
		return $this->_groups;
	}

	public function getObjlUser() {
		if (!$this->isGuest && $this->_model === null) {
			$this->_model = User::model()->findByPk($this->id);
		}
		return $this->_model;
	}
}