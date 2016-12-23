<?php
final class PageController extends \MYOBJ\appscms\src\AbsSiteController {
	public function actionObjNav($codename) {
		$this->render(DIR_VIEWS_SITE.'pageObjNav');
	}
}
