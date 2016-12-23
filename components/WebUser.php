<?php
class WebUser extends CWebUser {
	protected $_groups = null;
	protected $_model = null;

	public function getGroups() {
		if ($this->_groups === null) {
			if ($user = $this->getObjUser()) {
				$this->_groups = $user->groups;
				if(!$this->_groups) { //если MTM нет
					if(Yii::app()->controller->id!='admin') { //только для сайта
						$this->_groups = array($user->group);
					}
				}
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