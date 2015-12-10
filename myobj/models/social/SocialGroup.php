<?php

class SocialGroup extends AbsSocialCommunity {
	public $type_enter; //тип публичности (открытая, зактывая, скрытая - доступ по приглашению)

	public function relations()
	{
		return array(
			//контакты
			'contacts'=>array(self::MANY_MANY, 'CommunityUser', 'cmsplus_community_users(community_id, user_id)'),
			//ссылки
			'links'=>array(self::MANY_MANY, get_class($this), 'cmsplus_community_links(community_id, community_id_to)'),
			//администраторы
			'admins'=>array(self::MANY_MANY, 'User', 'cmsplus_community_rights_admins(community_id, user_id)'),
			//модераторы
			'moderators'=>array(self::MANY_MANY, 'User', 'cmsplus_community_rights_moderators(community_id, user_id)'),
		);
	}
}