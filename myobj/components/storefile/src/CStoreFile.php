<?php
class CStoreFile {
	/** @var null объект */
	private $_id;
	private $_arrayNameTitle;
	private $_namePlugin;
	public static function init($nameClassPlugin, $objAR) {
		return new self($nameClassPlugin, $objAR);
	}
	public function getNamePlugin() {
		return $this->_namePlugin;
	}
	private function __construct($nameClassPlugin, $objAR) {
		$this->_namePlugin = $nameClassPlugin;
	}

	/**
	 * @param $name имя файла
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function name($name,$key=0) {

	}

	/**
	 * @param $title описание файла
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function title($title,$key=0) {

	}

	/**
	 * @param $path путь к файлу
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function file($path,$key=0) {

	}

	/**
	 * @param $name имя файла
	 * Сохранить файл в базе и на сервере (может работать через сокет если это описано в плагине)
	 */
	public function save() {

	}

	/**
	 * Удалить файл и изменить объект
	 * @param integer $key по умолчанию 0 для нового элемента
	 */
	public function del($key) {

	}
}
