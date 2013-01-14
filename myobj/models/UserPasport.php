<?php
class UserPasport extends CActiveRecord
{
    public $firstname;
    public $lastname;
    
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
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