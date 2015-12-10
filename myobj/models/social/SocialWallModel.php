<?php

class SocialWallModel extends AbsBaseModel {
	public $user_id;
	public $group_id;
	public $content;
	public $date_time;

	public function relations()
	{
		return array(
			//прикрепленные файлы - изображения, музыка, видео
		);
	}
}