<?php
interface IOption {
    public static function get($filterarr);
    public static function getParams($filterarr);
    public static function edit_creat($id,$p_array);
    public static function edit_creat_param($id,$p_array);
    public static function del($id);
}

abstract class AbsOption implements IOption {
    protected static $nameparams = array('name','type','exp','range');
    public static function getNameparams() {
        return static::$nameparams;
    }
}