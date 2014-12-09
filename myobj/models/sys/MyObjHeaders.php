<?php
class MyObjHeaders extends AbsBaseHeaders
{
	public $name; //models.CharField(max_length=255)
	public $content; //models.TextField(blank=True) --
	public $sort; //models.IntegerField(blank=True,null=True,default=0)
	public $bpublic; //models.BooleanField(blank=True) --
	//table links
	public function relations()
	{
		$relations = parent::relations();
		$thisRelations = array(
			'test_relat_objcts'=>array(self::HAS_MANY, 'TestTableHM', 'obj_id'), // test
		);
		return array_merge($relations, $thisRelations);
	}

	protected function defaultRules() {
		$rules = parent::defaultRules();
		return $rules + array(
			array('name', 'required'),
			array('name', 'type', 'type'=>'string'),
			array('sort', 'default', 'value'=>0),
			array('bpublic', 'boolean'),
			array('bpublic', 'default', 'value'=>false),
			array('content', 'safe'),
		);
	}

	protected function defaultElementsForm() {
		return array(
			'name'=>array(
				'type'=>'text',
			),
			'sort'=>array(
				'type'=>'text',
			),
			'bpublic'=>array(
				'type'=>'checkbox',
			),
			'content'=>array(
				'type'=>'textarea',
			),
		);
	}

	/**
	 * Возможность хранить массивы в базе.
	 * @return array
	 */
	public function typesEArray() {
		return array(
			'content' => array(
				'elements' => array(
					'firstname',
					'lastname',
				),
				'conf' => array(
					'isMany'=>false,
				),
				'rules'=>array(
					'firstname'=>array(
						array('length', 'min'=>2,  'max'=>5),
					),
					'*'=>array(
						array('required'),
						array('length', 'min'=>3, 'max'=>12),
					),
				),
				'elementsForm' => array(
					'lastname'=>array(
						'type'=>'text',
					),
					'*'=>array(
						'type'=>'textarea',
					),
				),
			)
		);
	}
}

