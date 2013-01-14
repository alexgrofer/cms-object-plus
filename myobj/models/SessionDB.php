<?php
class SessionDB extends CActiveRecord
{
    public $session_key;
    public $session_data;
    public $expire_date;
    
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
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
    public function behaviors()
    {
        return array(
            'UserRelated'=>array(
                'class'=>'ext.behaviors.model.RelatedBehavior',
            ),
            'UserFormModel'=>array(
                'class'=>'application.modules.myobj.extensions.behaviors.model.FormModel',
            ),
        );
    }
}