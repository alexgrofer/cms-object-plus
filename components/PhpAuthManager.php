<?php
class PhpAuthManager extends CPhpAuthManager {
	public function init() {
		if ($this->authFile === null) {
			if(Yii::app()->controller->id=='admin') {
				$this->authFile = Yii::getPathOfAlias('MYOBJ.data.authAdmin') . '.php';
			}
			else {
				$this->authFile = Yii::getPathOfAlias('MYOBJ.data.realty.auth') . '.php';
			}
		}

		parent::init();

		if (!Yii::app()->user->isGuest) {

			$existingRoles = $this->getRoles();

			if (Yii::app()->user->groups) {
				foreach (Yii::app()->user->groups as $objGroup) {
					if (isset($existingRoles[$objGroup->name])) {
						$this->assign($objGroup->name, Yii::app()->user->id);
					}
				}
			}
		}
	}
}