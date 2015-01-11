<?php
class PhpAuthManager extends CPhpAuthManager {
	public function init() {
		if ($this->authFile === null) {
			$this->authFile = Yii::getPathOfAlias('MYOBJ.data.auth').'.php';
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