<?php
class navigation_sys_SystemObjHeaders_ extends SystemObjHeaders {
	public $top;
	public $codename;
	public $action;
	public $visible;

	/**
	 * @return array
	 */
	private function aliasPropertyHeader() {
		return array(
			'top' => 'vp1',
			'codename' => 'vp2',
			'action' => 'vp3',
			'visible' => 'bp1',
		);
	}

	/**
	 * @return array
	 */
	public function customRulesAlias()
	{
		return array(
			/*
			 * название контроллера но не обязателен так как если контроллера нет то можно найти по id
			 */
			array('codename', 'default', 'value'=>''),
			/*
			 * экшен который будет использован в контроллере
			 */
			array('action', 'default', 'value'=>null),
			/*
			 * разрешать вызывать это навигацию
			 */
			array('visible', 'default', 'value'=>false),

		);
	}

	/**
	 * @return array
	 */
	public function customElementsFormAlias() {
		return array(
			'top'=>array(
				'type'=>'text',
			),
			'codename'=>array(
				'type'=>'text',
			),
			'action'=>array(
				'type'=>'text',
			),
			'visible'=>array(
				'type'=>'checkbox',
			)
		);
	}
}

