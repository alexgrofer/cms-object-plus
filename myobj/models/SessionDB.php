<?php
class SessionDB extends AbsModel
{
    public $session_key;
    public $session_data;
    public $expire_date;
    
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    public function primaryKey() {
        return 'session_key';
    }
    public function rules() {
        return array(
            array('session_key, session_data, expire_date', 'required'),
        );
    }
    public function ElementsForm() {
        return array(
            'session_key'=>array(
                'type'=>'text',
            ),
            'session_data'=>array(
                'type'=>'textarea',
            ),
            'expire_date'=>array(
                'type'=>'text',
            ),
        );
    }
}