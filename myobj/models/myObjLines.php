<?php
class myObjLines extends AbsBaseLines
{
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

}
