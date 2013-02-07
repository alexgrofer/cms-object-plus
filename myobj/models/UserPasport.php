<?php
class UserPasport extends AbsModel
{
    public $firstname;
    public $lastname;
    
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    public function rules() {
        return array(
            array('firstname, lastname', 'required'),
        );
    }
    public function ElementsForm() {
        return array(
            'firstname'=>array(
                'type'=>'text',
            ),
            'lastname'=>array(
                'type'=>'text',
            ),
        );
    }
}