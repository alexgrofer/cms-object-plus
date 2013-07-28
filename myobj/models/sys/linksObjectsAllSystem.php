<?php
class linksObjectsAllSystem extends AbsBaseLinksObjects
{
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

}