<?php
abstract class AbsClientMemory {
    protected $codename;
    protected $dateExpired; //example 1sec,1min,3hour,7day,5month,3year
    protected $format_db_date = 'Y-m-d H:i:s';
    protected $ident;
    protected $auto_expire; //auto update expire get element
    protected $timer;
    protected function getdateExpired() {
        $array_this_date = getdate();
        if($pos=strpos($this->timer,'year')) $array_this_date['year'] += substr($this->timer,0,$pos); 
        elseif($pos=strpos($this->timer,'month')) $array_this_date['mon'] += substr($this->timer,0,$pos); 
        elseif($pos=strpos($this->timer,'day')) $array_this_date['mday'] += substr($this->timer,0,$pos); 
        elseif($pos=strpos($this->timer,'hour')) $array_this_date['hours'] += substr($this->timer,0,$pos); 
        elseif($pos=strpos($this->timer,'min')) $array_this_date['minutes'] += substr($this->timer,0,$pos);
        elseif($pos=strpos($this->timer,'sec')) $array_this_date['seconds'] += substr($this->timer,0,$pos);        
        $mktime = mktime($array_this_date['hours'], $array_this_date['minutes'], $array_this_date['seconds'], $array_this_date['mon'], $array_this_date['mday'], $array_this_date['year']);
        return date($this->format_db_date,$mktime);
    }
    
    protected function is_delayed() {
        if((strtotime($this->dateExpired) - time())<=0) {
            return true;
        }
        return false;
    }
    
    protected $type;
    public $value;
    public function getCodename() {
        return $this->codename;
    }
    //Factory Methods
    public static function init($ident,$type,$timer,$auto_expire=true) {
        $objclientMemory = new static();
        $objclientMemory->type = $type;
        $objclientMemory->ident = $ident;
        $objclientMemory->timer = $timer;
        $objclientMemory->dateExpired = $objclientMemory->getdateExpired();
        $objclientMemory->auto_expire = $auto_expire;
        return $objclientMemory;
    }
    public function get($codename) {
        switch($this->type) {
            case 'session':
                $objclientMemory =  static::_GetSessionServer($this->ident,$codename);
                break;
            case 'cookie':
                $objclientMemory =  static::_GetCookie($this->ident,$codename);
                break;
            case 'db':
                $objclientMemory =  static::_GetSessionDB($this->ident,$codename);
                break;
        }
        if($objclientMemory) {
            $objclientMemory->type = $this->type;
            $objclientMemory->codename = $codename;
            $objclientMemory->format_db_date = $this->format_db_date;
            $objclientMemory->ident = $this->ident;
            $objclientMemory->auto_expire = $this->auto_expire;
            $objclientMemory->timer = $this->timer;
        }
        //delete is Expired
        if($objclientMemory) {
            if($objclientMemory->is_delayed()) {
                static::removeMemory($objclientMemory->ident, $objclientMemory->type);
                $objclientMemory = null;
            }
            else {
                $objclientMemory->save();
            }
        }
        return $objclientMemory;
    }
    public function create($codename) {
        $this->codename = $codename;
        return $this;
    }
    public function save() {
        switch($this->type) {
            case 'session':
                $this->_saveSessionServer();
                break;
            case 'cookie':
                $this->_saveCookie();
                break;
            case 'db':
                $this->_saveSessionDB();
                break;
        }
    }
    public function remove() {
        switch($this->type) {
            case 'session':
                static::_DelElemSessionServer($this->ident,$this->codename);
                break;
            case 'cookie':
                $this->_DelElemCookie();
                break;
            case 'db':
                $this->_DelElemSessionDB();
                break;
        }
    }
    public static function removeMemory($ident,$type) {
        switch($type) {
            case 'session':
                static::_DelSessionServer($ident);
                break;
            case 'cookie':
                static::_DelCookie($ident);
                break;
            case 'db':
                static::_DelSessionDB($ident);
                break;
        }
    }
    //Memory Cookie
    protected function _saveCookie() {
        $data_array = (array_key_exists($this->ident,$_COOKIE))?unserialize($_COOKIE[$this->ident]):array();
        $data_array[$this->codename] = $this->value;
        $data_array['expire_date'] = $this->getdateExpired();
        setcookie($this->ident,serialize($data_array),strtotime($this->getdateExpired()));
        //reload page save cookie !!!!!
    }
    protected static function _GetCookie($ident,$codename) {
        $objclientMemory = null;
        if(array_key_exists($ident, $_COOKIE)) {
            $data_array = unserialize($_COOKIE[$ident]);
            if(array_key_exists($codename, $data_array)) {
                $objclientMemory = new static();
                $objclientMemory->value = $data_array[$codename];
                $objclientMemory->dateExpired = $data_array['expire_date'];
            }
        }
        return $objclientMemory;
    }
    protected static function _DelElemCookie($ident,$codename) {
        $objclientMemory = static::_GetCookie($ident,$codename);
        $data_array = unserialize($_COOKIE[$ident]);
        unset($data_array[$codename]);
        setcookie($objclientMemory->ident,serialize($data_array),strtotime($objclientMemory->getdateExpired()));
    }
    protected static function _DelCookie($ident) {
        setcookie($ident,'');
    }
    //Memory Session Server
    protected function _saveSessionServer() {
        if(!array_key_exists($this->ident,$_SESSION)) {
            $_SESSION[$this->ident] = array();
        }
        $_SESSION[$this->ident][$this->codename] = $this->value;
        $_SESSION[$this->ident]['expire_date'] = $this->getdateExpired();
    }
    protected static function _GetSessionServer($ident,$codename) {
        $objclientMemory = null;
        if(array_key_exists($ident, $_SESSION) && array_key_exists($codename, $_SESSION[$ident])) {
            $objclientMemory = new static();
            $objclientMemory->value = $_SESSION[$ident][$codename];
            $objclientMemory->dateExpired = $_SESSION[$ident]['expire_date'];
        }
        return $objclientMemory;
    }
    protected static function _DelElemSessionServer($ident,$codename) {
        unset($_SESSION[$ident][$codename]);
    }
    protected static function _DelSessionServer($ident) {
        unset($_SESSION[$ident]);
    }
    //Memory DB
    protected function _saveSessionDB() { }
    protected static function _GetSessionDB($ident,$codename) { }
    protected static function _DelElemSessionDB($ident,$codename) { }
    protected static function _DelSessionDB($ident) { }
    //Memory Memcache 
    protected function _saveSessionMemcache() { }
    protected static function _GetSessionMemcache($ident,$codename) { }
    protected static function _DelElemSessionMemcache($ident,$codename) { }
    protected static function _DelSessionMemcache($ident) { }
}

class clientMemory extends AbsClientMemory {
    //Memory DB
    protected function _saveSessionDB() {
        $objSessionDB = SessionDB::model()->findByPk($this->ident);
        if(empty($objSessionDB)) {
            $objSessionDB = new SessionDB();
            $objSessionDB->session_key = $this->ident;
        }
        $objSessionDB->expire_date = $this->getdateExpired();
        $data_array = (($objSessionDB->session_data)!='')?unserialize($objSessionDB->session_data):array();
        $data_array[$this->codename] = $this->value;
        $objSessionDB->session_data = serialize($data_array);
        $objSessionDB->save();
    }
    protected static function _GetSessionDB($ident,$codename) {
        $objclientMemory = null;
        $objSessionDB = SessionDB::model()->findByPk($ident);
        if($objSessionDB) {
            $objclientMemory = new static();
            $objclientMemory->value = unserialize($objSessionDB->session_data)[$codename];
            $objclientMemory->dateExpired = $objSessionDB->expire_date;
        }
        return $objclientMemory;
    }
    protected static function _DelElemSessionDB($ident,$codename) {
        $objSessionDB = SessionDB::model()->findByPk($ident);
        if($objSessionDB) {
            $data_array = unserialize($objSessionDB->session_data);
            unset($olddata_array[$codename]);
            $objSessionDB->session_data = serialize($data_array);
            $objSessionDB->save();
        }
    }
    protected static function _DelSessionDB($ident) {
        $objSessionDB = SessionDB::model()->findByPk($ident);
        if($objSessionDB) $objSessionDB->delete();
    }
    //END Memory DB
}


/*
$ident = 'd2sfk34dflk34sdsss'; //идентификатор сесии клиента

$objclientMemoryCookie = clientMemory::init($ident,'db','2min'); //инизиализация сессии

$objclientMemoryCookieBasket = $objclientMemoryCookie->get('basket5');

if(!$objclientMemoryCookieBasket) {
    echo 'not ses';
    $objclientMemoryCookieBasket = $objclientMemoryCookie->create('basket5');
    $objclientMemoryCookieBasket->value = 'text OR array';
    //$objclientMemoryCookieBasket->save();
}
else {
    echo 'yes ses';
    print_r($objclientMemoryCookieBasket);
    
}
*/
