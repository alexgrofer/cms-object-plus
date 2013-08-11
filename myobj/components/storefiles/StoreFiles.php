<?php
class StoreFiles extends CComponent {
	private $test8;
	public function  setTest8($val) {
		$this->test8 = $val;
	}
	public function getTest8() {
		return $this->test8;
	}
	public function init() {
		//import
		Yii::import('application.modules.myobj.components.storefiles.classes.*');
	}
	/**
	 * Получает массив объектов @link filesStorage с массивом файлов класса CFileStor
	 * @param array $ids массив ключей для поиска объектов
	 * @param string $model название модели в которой хранятся файлы
	 * @return array массив объектов CFilesStor
	 */
	public function getobj($ids,$model) {

	}
	/**
	 * Добавляет и сохраняет новый файл к объекту
	 * @param array $ids массив ключей для поиска объектов
	 * @param string $name название файла
	 * @param string $title описание файла
	 * @return CFilesStor объект файла
	 */
	public function addobj($ids,$name,$title) {

	}
	/**
	 * Удаляет объекты @link CFilesStor, включая сами файлы
	 * @param array $ids массив ключей для поиска объектов
	 */
	public function delobj($ids,$model) {

	}
}
