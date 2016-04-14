<?php

abstract class AbsStoreFilesModel extends AbsBaseModel {
	public $code_name;
	public $folder;
	public $size;
	public $date_time_create;
	public $hash;

	public function beforeSave() {
		if(!parent::beforeSave()) return false;

		if($this->isNewRecord) {
			$this->date_time_create = new CDbExpression('NOW()');
		}
		return true;
	}
}
