<?php

abstract class AbsStoreFilesModel extends AbsBaseModel {
	public $code_name;
	public $folder;
	public $size;
	public $date_time_create;
	public $hash;

	public function uBeforeSave() {
		parent::uBeforeSave();

		if($this->isNewRecord) {
			$this->date_time_create = date('Y-m-d H:i:s');
		}
	}
}
