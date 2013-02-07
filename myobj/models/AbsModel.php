<?php
abstract class AbsModel extends CActiveRecord
{
    public static function model($className=null)
    {
        if($className===null) {
            $class = new static();
            $className = get_class($class);
        }
        return parent::model($className);
    }
    public function behaviors()
    {
        return array(
            'UserRelated'=>array(
                'class'=>'ext.yii-model-related.RelatedBehavior',
            ),
            'UserFormModel'=>array(
                'class'=>'application.modules.myobj.extensions.behaviors.model.FormModel',
            ),
        );
    }

}
