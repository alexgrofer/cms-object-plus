<?php
class DepCatOption extends AbsModel
{
    public function tableName()
    {
        return 'setcms_dep_cat_option';
    }
    public function relations()
    {
        return array(
            'depcatoptionparams'=>array(self::HAS_MANY, 'DepCatOptionParam', 'id_option'),
        );
    }
}