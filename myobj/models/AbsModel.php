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
    public function getMTMcol($model,$pkelem,$exp_select) {
        return $this->UserRelated->links_edit('select',$model,$pkelem,$exp_select);
    }
    public function setMTMcol($model,$array_elems,$array_value) {
        return $this->UserRelated->links_edit('edit',$model,$array_elems,$array_value);
    }

    public function addSelfObjects($model,$elems,$fk=Null) {
        $relations = $this->relations();
        $type = 'add';
        if(($relations[$model][0]!=CActiveRecord::MANY_MANY)) {
            $type='set';
            $elems = is_array($elems)?$elems[0]:$elems;
        }
        return $this->UserRelated->links_edit($type,$model,$elems,$fk);
    }
}
