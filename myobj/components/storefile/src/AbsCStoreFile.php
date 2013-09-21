<?php
class AbsCStoreFile extends CComponent {
	private $_id;
	/**
	 * @var array массив в который сохраняются все данные
	 */
	private $_arrayConfObj=array();
	/**
	 * @var DefaultPluginStoreFile объект плагина настройки
	 */
	private $_objPlugin;
	private $_tmpFiles;
	private $_tmpEx_s;
	private $_tmpRand_s;
	/**
	 * @var string папка сохранения для всех новых объектов
	 */
	private $_folderAll;

	public function setFolderAll($folder) {
		$this->_folderAll = $folder;
	}
	public function getFolderAll() {
		return ($this->_folderAll)?$this->_folderAll.DIRECTORY_SEPARATOR:'';
	}

	public function getNamePlugin() {
		return get_class($this->_objPlugin);
	}
	public function __construct($objPlugin, $objAR) {
		$this->_objPlugin = $objPlugin;
		//собрать переменную $this_arrayConfObj если есть $objAR
	}
	public function  getId() {
		return $this->_id;
	}

	public function set_name($name,$key=0) {
		$this->_arrayConfObj[$key]['name'] = $name;
	}
	public function get_name($key) {
		$this->_arrayConfObj[$key]['name'];
	}
	public function setName($name) {
		$this->set_name($name,0);
	}
	public function getName() {
		return $this->get_name(0);
	}

	public function set_title($title,$key=0) {
		$this->_arrayConfObj[$key]['title'] = $title;
	}
	public function get_title($key) {
		$this->_arrayConfObj[$key]['title'];
	}
	public function setTitle($title) {
		$this->set_title($title,0);
	}
	public function getTitle() {
		return $this->get_title(0);
	}

	public function set_path($path,$key=0) {
		//не отрубить в конце слеш
		$this->_arrayConfObj[$key]['path'] = $path;
	}
	public function get_path($key) {
		($this->_arrayConfObj[$key]['path'])?$this->_arrayConfObj[$key]['path'].DIRECTORY_SEPARATOR:'';
	}
	public function setPath($path) {
		$this->set_path($path,0);
	}
	public function getPath() {
		return $this->get_path(0);
	}

	public function set_sort($sort,$key=0) {
		$this->_arrayConfObj[$key]['sort'] = $sort;
	}
	public function get_sort($key) {
		$this->_arrayConfObj[$key]['sort'];
	}
	public function setSort($sort) {
		$this->set_sort($sort,0);
	}
	public function getSort() {
		return $this->get_sort(0);
	}

	public function set_file($path,$key=0) {
		$this->_tmpFiles[$key] = $path;
	}
	public function get_file($key) {
		return $this->_tmpFiles[$key];
	}
	public function setFile($path) {
		$this->set_file($path,0);
	}
	public function getFile() {
		return $this->get_file(0);
	}

	public function set_ex($path,$key=0) {
		$this->_tmpEx_s[$key] = $path;
	}
	public function get_ex($key) {
		$this->_tmpEx_s[$key];
	}
	public function setEx($path) {
		$this->set_ex($path,0);
	}
	public function getEx() {
		return $this->get_ex(0);
	}

	public function set_rand($path,$key=0) {
		$this->_tmpRand_s[$key] = $path;
	}
	public function get_rand($key) {
		$this->_tmpRand_s[$key];
	}
	public function setRand($path) {
		$this->set_rand($path,0);
	}
	public function getRand() {
		return $this->get_rand(0);
	}

	/**
	 * @param $name имя файла
	 * Сохранить файл в базе и на сервере (может работать через сокет если это описано в плагине)
	 */
	public function save() {
		//сохранить файл если он изменялся - делает плагин
		//переписать базу - делает плагин
		//если исключение удалить файл - делает плагин
		/* @var CFile $objCFile */
		$objPlugin = $this->_objPlugin;
		foreach($this->_tmpFiles as $k => $path) {
			$objCFile = Yii::app()->file->set($path);
			$confFolder = ($objPlugin::PATH_LOAD).DIRECTORY_SEPARATOR.$this->getFolderAll().$this->get_path($k);
			$objCFile->copy($confFolder.$objCFile->basename);
		}
	}

	/**
	 * Удалить файл и изменить объект
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function del($key) {
		unset($this->_arrayConfObj[$key]);
		$this->save();
	}
}
