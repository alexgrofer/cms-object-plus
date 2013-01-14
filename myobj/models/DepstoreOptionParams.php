<?php
class DepstoreOptionParams extends CActiveRecord
{
    public $val;
    
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
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