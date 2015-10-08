<?php

/**
 * раздичные социальные группы
 * Class AbsCommunityModel
 */
class AbsCommunityModel extends AbsBaseModel {
	/**
	 * Тип сообщества (личная страница пользователя,группа,компания)
	 * @var
	 */
	public $type; //ссылка на енум?
	//columns DB
	public $name;
	public $codename;
	//key
	public $user_id;
	public $view_id;
	// end
}

