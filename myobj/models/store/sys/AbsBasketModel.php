<?php

/**
 * Корзина пользователей
 * Class StoreModel
 */
abstract class AbsBasketModel extends AbsBaseModel
{
	/**
	 * Идентификатор пользователя
	 * @var
	 */
	public $user_id;

	abstract protected function configManyRelationGoods();
	abstract protected function getNameModelUser();

	public function relations() {
		return array(
			'goods'=>array(self::MANY_MANY, $this->getNameModelGoods(), $this->configManyRelationGoods()),
			'user'=>array(self::BELONGS_TO, $this->getNameModelUser(), 'user_id'),
		);
	}
}
