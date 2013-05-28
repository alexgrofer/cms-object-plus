<?php
class DepCatOptionParam extends AbsModel
{
    public function tableName()
    {
        return 'setcms_dep_cat_option_params';
    }
    public function relations()
    {
        return array(
            'depcatoption'=>array(self::HAS_ONE, 'DepCatOption', 'id_option'),
        );
    }
}