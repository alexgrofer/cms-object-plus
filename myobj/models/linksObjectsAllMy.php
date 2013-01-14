<?php
class linksObjectsAllMy extends AbsBaseLinksObjects
{
    
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
 
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    
}
