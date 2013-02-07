<?php
//Yii::import('CMSAbstractClasses',true);

class systemObjLines extends AbsBaseLines
{
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    
}
