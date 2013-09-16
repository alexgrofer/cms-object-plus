<?php
class AbsCStoreFile extends CComponent {
	/** @var null объект */
	private $_id;
	private $_arrayNameTitleUrl=array();
	private $_namePlugin;
	private $_tmpPathFiles;
	public static function init($nameClassPlugin, $objAR) {
		return new self($nameClassPlugin, $objAR);
	}
	public function getNamePlugin() {
		return $this->_namePlugin;
	}
	private function __construct($nameClassPlugin, $objAR) {
		$this->_namePlugin = $nameClassPlugin;
	}
	public function  getId() {
		return $this->_id;
	}

	public function set_name($name,$key=0) {
		$this->_arrayNameTitleUrl[$key]['name'] = $name;
	}
	public function get_name($key) {
		$this->_arrayNameTitleUrl[$key]['name'];
	}
	public function setName($name) {
		$this->set_name($name,0);
	}
	public function getName() {
		return $this->get_name(0);
	}

	public function set_title($title,$key=0) {
		$this->_arrayNameTitleUrl[$key]['title'] = $title;
	}
	public function get_title($key) {
		$this->_arrayNameTitleUrl[$key]['title'];
	}
	public function setTitle($title) {
		$this->set_title($title,0);
	}
	public function getTitle() {
		return $this->get_title(0);
	}

	public function set_url($url,$key=0) {
		$this->_arrayNameTitleUrl[$key]['url'] = $url;
	}
	public function get_url($key) {
		$this->_arrayNameTitleUrl[$key]['url'];
	}
	public function setUrl($url) {
		$this->set_url($url,0);
	}
	public function getUrl() {
		return $this->get_url(0);
	}

	public function set_sort($sort,$key=0) {
		$this->_arrayNameTitleUrl[$key]['sort'] = $sort;
	}
	public function get_sort($key) {
		$this->_arrayNameTitleUrl[$key]['sort'];
	}
	public function setSort($sort) {
		$this->set_sort($sort,0);
	}
	public function getSort() {
		return $this->get_sort(0);
	}

	public function set_file($path,$key=0) {
		$this->_tmpPathFiles[$key] = $path;
	}
	public function get_file($key) {
		$this->_tmpPathFiles[$key];
	}
	public function setFile($path) {
		$this->set_file($path,0);
	}
	public function getFile() {
		return $this->get_file(0);
	}

	/**
	 * @param $name имя файла
	 * Сохранить файл в базе и на сервере (может работать через сокет если это описано в плагине)
	 */
	public function save() {
		//сохранить файл если он изменялся - делает плагин
		//переписать базу - делает плагин
		//если исключение удалить файл - делает плагин
	}

	/**
	 * Удалить файл и изменить объект
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function del($key) {
		unset($this->_arrayNameTitleUrl[$key]);
		$this->save();
	}
}
