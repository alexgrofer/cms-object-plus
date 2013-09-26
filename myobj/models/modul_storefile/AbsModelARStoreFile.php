<?php
class AbsModelARStoreFile extends AbsModel
{
	/**
	 * @var string Хранит сериализованный массив c ключами
	 * обязательные:
	 * path => 'folder1/folder2', - папка может быть пустым
	 * name => 'file.pdf', - название файла
	 * sort => '0', - сортировка
	 * и любые другие:
	 *
	 */
	public $file;
	//build
	public $file_ui;
	public $force_save;
	public $is_randName;
	public $is_addFile;
	//conf
	/**
	 * @var string Название класса плагина для обработки
	 */
	protected $pluginLoader;
	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}

	public function rules()
	{
		return array(
			array('file', 'required'),
			//build

		);
	}

	public function attributeLabels() {
		return array(
			//переводы?
		);
	}

	public function ElementsForm() {
		return array(
			//сделать класс CArraySerializeElemAR, посмотреть как сделан CMultiFileUpload и где лежит, сделать возможность редактировать сериализованный одноммерный массив
			'file'=>array(
				'type'=>'text',
			),
			//build
			//может резать файлы при необходимости? архивировать?
			'file_ui'=>array(
				'type'=>'CMultiFileUpload',
			),
			'force_save'=>array(
				'type'=>'checkbox',
			),
			'is_randName'=>array(
				'type'=>'checkbox',
			),
			'is_addFile'=>array(
				'type'=>'checkbox',
			),
		);
	}
	protected function beforeDelete() {
		//build
		return parent::beforeDelete();
	}

	protected function beforeSave() {
		if(parent::beforeSave()!==false) {
			//build
			/*
			 * создать если новый или найти если старый объект CStoreFile
			 */
			return true;
		}
		else return parent::beforeSave();
	}
}
