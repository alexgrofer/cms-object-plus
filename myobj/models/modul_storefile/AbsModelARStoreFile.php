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
	 * @var DefaultPluginStoreFile Название класса плагина для обработки
	 */
	public $pluginLoader;
	/**
	 * @var array Конфигурация плагина
	 */
	public $pluginConstructLoaderParamsConf;
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
		parent::beforeDelete();
		//build
		$objClassPlugin = $this->pluginLoader($this->pluginLoaderParamsConf);
		$file = $objClassPlugin->buildStoreFile($this);
		$file->del();

		return true;
	}

	protected function beforeSave() {
		if(parent::beforeSave()!==false) {
			//build
			$objClassPlugin = $this->pluginLoader($this->pluginLoaderParamsConf);
			$file = $objClassPlugin->buildStoreFile($this);
			$file->save();
			return true;
		}
		else return parent::beforeSave();
	}
}
