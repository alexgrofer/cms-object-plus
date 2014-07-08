<?php
class TestAbsBaseModel extends AbsBaseModel {
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
}
