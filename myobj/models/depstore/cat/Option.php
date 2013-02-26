<?php
class DepstoreOption extends AbsModel
{
    public $name;
    public $type;
    public $range;
    public function gettypeAr() {
        return array('1'=>'val','2'=>'bool','3'=>'select');
    }
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    public function rules() {
        return array(
            array('name, type', 'required'),
            array('range', 'default', 'value'=>''),
        );
    }
    public function relations()
    {
        return array(
            'params'=>array(self::HAS_MANY, 'DepstoreOptionParams', 'id_option'),
        );
    }
    public function ElementsForm() {
        return array(
            'name'=>array(
                'type'=>'text',
            ),
            'type'=>array(
                'type'=>'dropdownlist',
                'items'=>$this->gettypeAr(),
            ),
            'range'=>array(
                'type'=>'text',
            ),
        );
    }
}