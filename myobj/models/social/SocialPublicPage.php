<?php

/**
 * Публичные страницы
 */
class SocialPublicPage extends AbsSocialCommunity {
	public $type;

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
			//редакторы
			'editors'=>array(self::MANY_MANY, 'User', 'cmsplus_community_rights_editors(community_id, user_id)'),
			//бан лист
			'ban_list'=>array(self::MANY_MANY, 'User', 'cmsplus_community_ban_list(community_id, user_id)'),
		);
	}
}