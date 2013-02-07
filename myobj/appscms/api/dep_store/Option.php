<?php
Yii::import('application.modules.myobj.library.dep_store.AbsOption');
class Option extends AbsOption {
    public static function get($filterarr=array(),$limit=array('offset'=>0,'limit'=>100)) {
        //example $filterarr=array(['id'=>5[,'name'=>'nameelem'[,'type'=>0]]])
        //FACADE START
        $modelOption = DepstoreOption::model();
        $arr = $modelOption->findAllByAttributes($filterarr);
        return $arr;
        //FACADE END
    }
    public static function getParams($idoption=null) {
        //example $filterarr=array(['id'=>5[,'name'=>'nameelem']])
        //FACADE START
        $arr = array();
        if($idoption) {
            $objOption = DepstoreOption::model()->findByPk($idoption);
            $arr = $objOption->params;
        }
        else {
            $arr = DepstoreOptionParams::model()->findAll();
        }
        return $arr;
        //FACADE END
    }
    public static function edit_creat($p_array,$id) {
        //FACADE START
        $objOption = DepstoreOption::model()->findByPk($id) ?: (new DepstoreOption());
        foreach($p_array as $key => $val) {
                if(in_array($key,static::$nameparams)) {
                    $objOption->$key = $val;
                }
        }
        $objOption->save();
        //FACADE END
    }
    public static function edit_creat_param($p_array,$id) {
        //FACADE START
        $objOptionParam = DepstoreOptionParams::model()->findByPk($id) ?: (new DepstoreOptionParams());
        foreach($p_array as $key => $val) {
            $objOptionParam->$key = $val;
        }
        $objOptionParam->save();
        //FACADE END
    }
    public static function del($id) {
        //FACADE START
        DepstoreOption::model()->deleteByPk($id);
        //FACADE END
    }
    public static function delParam($id) {
        //FACADE START
        DepstoreOptionParams::model()->deleteByPk($id);
        //FACADE END
    }
}