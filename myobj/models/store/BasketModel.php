<?php

/**
 * Корзина пользователей
 * Class StoreModel
 */
class BasketModel extends AbsBasketModel
{
	protected function configManyRelationGoods() {
		return 'cmsplus_basket_goods(to_basket_id, from_goods_id)';
	}
	protected function getNameModelUser() {
		return 'User';
	}
}
