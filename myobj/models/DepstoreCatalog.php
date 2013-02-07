<?php
class DepstoreCatalog extends AbsModel
{
    public $name;
    public $top;
    
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    public function rules() {
        return array(
            array('name', 'required'),
            array('top', 'default', 'value'=>0),
        );
    }
    public function ElementsForm() {
        return array(
            'name'=>array(
                'type'=>'text',
            ),
            'top'=>array(
                'type'=>'text',
            ),
        );
    }
}