<?php
class linksObjectsAllMy extends AbsBaseLinksObjects
{
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

}
