<?php
/**
 * Class FormModelTest
 * Тестируем форму редактирования добавления и другие возможности формы (ajax)
 * http://www.yiiframework.com/doc/guide/1.1/ru/test.functional
 */
class FormModelTest extends AbsWebTestCase {

	public function testShow()
	{
		$this->open('post/1');
		// проверяем наличие заголовка некой записи
		$this->assertTextPresent('ssss');
		// проверяем наличие формы комментария
		$this->assertTextPresent('Leave a Comment');
	}
}
