<?php
abstract class AbsCStoreFile extends CComponent {
	/**
	 * @var DefaultPluginStoreFile объект плагина
	 */
	protected  $_objPlugin;
	public function __construct($objPlugin,$arObj) {
		$this->_objPlugin = $objPlugin;
		$this->_objAr = $arObj;
	}
	private $_id;

	public function getNamePlugin() {
		return get_class($this->_objPlugin);
	}
	public function getId() {
		return $this->_id;
	}

	private $_realArrayConfObj=array();

	private $_tmpArrayConfObj=array();

	protected function _getDefParam($name,$key) {
		return (isset($this->_realArrayConfObj[$key]) && isset($this->_realArrayConfObj[$key][$name]))?
			$this->_realArrayConfObj[$key][$name]
			:
			null;
	}
	protected function _setDefParam($name,$key,$val) {
		$this->_realArrayConfObj[$key][$name] = $val;
	}

	//custom methods
	/**
	 * @var string папка сохранения для всех новых объектов
	 */
	private $_folderAll;
	public function setFolderAll($folder) {
		$this->_folderAll = $folder;
	}
	public function getFolderAll() {
		return ($this->_folderAll)?$this->_folderAll:'';
	}

	private $_isRandAll;
	public function setIsRandAll($bool) {
		$this->_isRandAll = $bool;
	}
	public function getIsRandAll() {
		return $this->_isRandAll;
	}
	//end custom methods

	public function set_name($name,$key=0) {
		$this->_setDefParam('name',$key,$name);
	}
	public function get_name($key) {
		return $this->_getDefParam('name',$key);
	}
	public function setName($name) {
		$this->set_name($name,0);
	}
	public function getName() {
		return $this->get_name(0);
	}

	public function set_title($title,$key=0) {
		$this->_setDefParam('title',$key,$title);
	}
	public function get_title($key) {
		return $this->_getDefParam('title',$key);
	}
	public function setTitle($title) {
		$this->set_title($title,0);
	}
	public function getTitle() {
		return $this->get_title(0);
	}

	public function set_path($path,$key=0) {
		$this->_setDefParam('path',$key,$path);
	}
	public function get_path($key) {
		return $this->_getDefParam('path',$key);
	}
	public function setPath($path) {
		$this->set_path($path,0);
	}
	public function getPath() {
		return $this->get_path(0);
	}

	public function set_sort($sort,$key=0) {
		$this->_setDefParam('sort',$key,$sort);
	}
	public function get_sort($key) {
		return $this->_getDefParam('sort',$key);
	}
	public function setSort($sort) {
		$this->set_sort($sort,0);
	}
	public function getSort() {
		return $this->get_sort(0);
	}

	public function set_ex($ex,$key=0) {
		$this->_setDefParam('ex',$key,$ex);
	}
	public function get_ex($key) {
		return $this->_getDefParam('ex',$key);
	}
	public function setEx($path) {
		$this->set_ex($path,0);
	}
	public function getEx() {
		return $this->get_ex(0);
	}
	//not real
	public function set_file($path,$key=0) {
		$this->_tmpArrayConfObj[$key]['file'] = $path;
	}
	public function get_file($key) {
		return $this->_tmpArrayConfObj[$key]['file'];
	}
	public function setFile($path) {
		$this->set_file($path,0);
	}
	public function getFile() {
		return $this->get_file(0);
	}

	public function set_isRand($bool,$key=0) {
		$this->_tmpArrayConfObj[$key]['rand'] = $bool;
	}
	public function get_isRand($key) {
		$this->_tmpArrayConfObj[$key]['rand'];
	}
	public function setIsRand($bool) {
		$this->set_isRand($bool,0);
	}
	public function getIsRand() {
		return $this->get_isRand(0);
	}
	//end not real
}
