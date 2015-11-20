<?php
abstract class AbsFilesModel extends AbsBaseModel
{
	public $hash;
	public $server;
	public $folder;
	public $size;
	public $date_time;
	public $user_id;
	/**
	 * Подтвержденная загрузка файла. при онлайн загрузках
	 * @var
	 */
	public $is_activate;
}
