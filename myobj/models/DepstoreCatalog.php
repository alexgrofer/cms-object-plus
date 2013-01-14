<?php
class DepstoreCatalog extends CActiveRecord
{
    public $name;
    public $top;
    
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