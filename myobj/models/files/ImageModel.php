<?php

/**
 * Картинки
 * Class AbsFiles
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
