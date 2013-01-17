<?php
Yii::import('application.modules.myobj.appscms.api.MyWidgetRender');
class UCms {
    public $config;
    private $_controller;
    public $setparams = array();
    static private $_instance = null;
    private $_paramsgetnav = array();
    public function getparams() {
        if(!count($this->_paramsgetnav)) {
            Yii::import('application.modules.myobj.appscms.api.utils',true);
            $this->_paramsgetnav = apicms\utils\uiparamnav($this->setparams['OBJNAV']->content);
        }
        return $this->_paramsgetnav;
    }
    static function getInstance($currentcontroller=null, $conf=null) {
        if (self::$_instance === null) {
            self::$_instance = new UCms($currentcontroller, $conf);
        }
        return self::$_instance;
    }
    public function geturlpage($name='',$addelem='',$params=array()) {
        $parse = array_slice(explode('/', $this->_controller->actionParams['r']),1);
        //last search array_keys($parse,$name)
        $keylast = (count(($_=array_keys($parse,$name))))?$_[count($_)-1]:count($parse)-1;
        $str_slice = implode('/',array_slice($parse,0,$keylast+1));
        //
        return $this->_controller->createUrl($str_slice.'/'.$addelem,$params);
    }
    function isLastUrl($strlsturl) {
        if($strlsturl!='' && preg_match('~./'.$strlsturl.'/?$~',$this->_controller->actionParams['r'])) {
            return true;
        }
        return false;
    }
    public function runProject($id_project) {
        //каждому проекто в каком то конфиге задаются параметры  {'IDKLKSLSK' => array('nameconfuser'=>'sdsdsd.php', 'confsys'=>'dssdsd.php')}
        // static::getInstance(); - первый вызов getInstance - потом будет только возвр текущий объект
        //т. е переделать на if (self::$_instance !== null) {
        
    }
    private function __construct($currentcontroller=null, $conf=null) {
        $this->config = require(dirname(__FILE__).($conf ?: '/config/main.php'));
        $this->_controller = $currentcontroller;
    }
    
    private $_thisnavhandles = null;
    public function handle($name, $id) {
        if($this->_thisnavhandles==null) {
            $this->_thisnavhandles = array();
            $handles = $this->setparams['OBJNAV']->getobjlinks('handle_sys')->findAll();
            $idsview = array();
            foreach($handles as $handle) {
                $idsview[] = $handle->vp1; //vp1 = idview , sort = handletmplid
            }
            $CRITERIA = new CDbCriteria();
            $CRITERIA->addInCondition('id', $idsview);
            $listview = uClasses::getclass('views_sys')->objects()->findAll($CRITERIA);
            $elemsview = array();
            foreach($listview as $view) {
                $elemsview[$view->id] = array('patchview' => $view->vp1, 'objview' => $view); //vp1 = patchview
            }
            foreach($handles as $handle) {
                $this->_thisnavhandles[$handle->sort] = $elemsview[$handle->vp1];
            }
        }
        //return $this->controller->widget('MyWidgetRender',array('idtmplhandl'=>$id, 'apcms'=>$this, 'listtshihandles'=>$this->_thisnavhandles, 'controller' => $this->controller));
        $patchview = apicms\utils\URender($id,$this->_thisnavhandles);
        if($patchview!='') {
            Yii::beginProfile('Loade_handle_v:'.$patchview);
            $render =  $this->_controller->render('..\..\views\user\views\\'.$patchview);
            Yii::endProfile('Loade_handle_v:'.$patchview);
            return $render;
            
        }
    }
}
