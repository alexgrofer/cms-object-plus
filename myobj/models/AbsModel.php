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
    public function setuiparam($array,$save_dbCriteria=null) {
        if($save_dbCriteria===null) {
            $save_dbCriteria = $this->dbCriteria;
        }
        if(array_key_exists('condition',$array)) {
            $textsql = '';
            $i = 1;
            foreach($array['condition'] as $cond) {
                $typecond = (count($cond)<4)?'AND':$cond[3];
                if($i == count($array['condition'])) $typecond = '';
                $textsql .= $this->tableAlias.'.'.$cond[0].' '.$cond[1].' '.$cond[2].' '.$typecond;
                $i++;
            }
            $this->dbCriteria->condition .= ' AND '.$textsql;
        }
        if(array_key_exists('order',$array) && count($array['order'])) {
            $textsql = '';
            $i=1;
            foreach($array['order'] as $arpropelem) {
                $typf = (count($arpropelem)==2)?$arpropelem[1]:'asc';
                $textsql .= $this->tableAlias.'.'.$arpropelem[0].' '.$typf.((count($array['order'])!=$i)?',':'');
                $i++;
            }
            $this->dbCriteria->order .= ($this->dbCriteria->order?',':'').$textsql;
        }
        if(array_key_exists('limit',$array) && count($array['limit'])) {
            $save_dbCriteria->limit = $array['limit']['limit'];
            $save_dbCriteria->offset = $array['limit']['offset'];;
        }
        $this->setDbCriteria($save_dbCriteria);
        return $this;
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
