<?php
class DepCatCategory extends AbsModel
{
    public function tableName()
    {
        return 'setcms_dep_cat_category';
    }
    public function relations()
    {
        return array(
            'depcatoption'=>array(self::MANY_MANY, 'DepCatOption', 'setcms_dep_cat_category_option(category_id, option_id)'),
        );
    }

}