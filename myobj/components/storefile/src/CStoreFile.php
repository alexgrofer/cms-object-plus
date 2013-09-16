<?php
class CStoreFile {
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

	/**
	 * @param $name имя файла
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function name($name,$key=0) {
		$this->_arrayNameTitleUrl[$key]['name'] = $name;
	}

	/**
	 * @param $title описание файла
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function title($title,$key=0) {
		$this->_arrayNameTitleUrl[$key]['title'] = $title;
	}

	/**
	 * Относительный путь к файлу
	 * @param $selfUrl
	 * @param int $key
	 */
	public function url($selfUrl,$key=0) {
		$this->_arrayNameTitleUrl[$key]['url'] = $selfUrl;
	}

	/**
	 * Установить внутреннюю сортировку
	 * @param $sort
	 * @param int $key
	 */
	public function sort($sort,$key=0) {
		$this->_arrayNameTitleUrl[$key]['sort'] = $sort;
	}

	/**
	 * @param $path путь к файлу
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function file($path,$key=0) {
		$this->_tmpPathFiles[$key] = $path;
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
