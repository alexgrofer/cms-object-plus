<?php
class DepstoreOptionParams extends AbsModel
{
    public $val;
    
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    
    public function relations()
    {
        return array(
            'id_option'=>array(self::BELONGS_TO, 'DepstoreOption', 'id_option')
        );
    }
    
    public function rules() {
        return array(
            array('val', 'required'),
        );
    }
    public function ElementsForm() {
        return array(
            'val'=>array(
                'type'=>'text',
            ),
        );
    }
}