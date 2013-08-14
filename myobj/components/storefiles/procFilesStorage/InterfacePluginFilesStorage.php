<?php
interface InterfaceFilesStorage {
	/**
	 * Создать что либо
	 * @param $params
	 * @return mixed
	 */
	public static function newobj($params);

	/**
	 * Получить что либо
	 * @param $params
	 * @return mixed
	 */
	public static function get($params);

	/**
	 * Редактировать что либо
	 * @param $params
	 * @return mixed
	 */
	public static function editobj($params);

	/**
	 * Удалить что либо
	 * @param $params
	 * @return mixed
	 */
	public static function delobj($params);
}
