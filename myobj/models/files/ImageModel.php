<?php

/**
 * Картинки
 * Class ImageModel
 */
abstract class ImageModel extends AbsFilesModel
{
	/**
	 * Имя
	 * @var
	 */
	public $name;
	public $description;
	/**
	 * Альбом
	 * @var
	 */
	public $album_id;
}
