<?php
interface ICartalog {
    public static function get($filterarr);
    public static function edit_creat($id,$p_array);
    public static function del($id);
}

abstract class AbsCatalog implements ICartalog {
    protected static $nameparams = array('name','top');
    public static function getNameparams() {
        return static::$nameparams;
    }
}