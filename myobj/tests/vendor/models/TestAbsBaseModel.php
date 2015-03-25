<?php
class TestAbsBaseModel extends AbsBaseModel {
	public function tableName()
	{
		return 'cmsplus_'.strtolower(get_class($this));
	}
}
