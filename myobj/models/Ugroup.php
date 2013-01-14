<?php
class Ugroup extends CActiveRecord
{
    public $name;
    public $guid;
    
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
        );
    }
    public function ElementsForm() {
        return array(
            'name'=>array(
                'type'=>'text',
            ),
        );
    }
    protected function beforeSave() {
        if($this->isNewRecord) {
            $this->guid = apicms\utils\GUID();
        }
        return parent::beforeSave();
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