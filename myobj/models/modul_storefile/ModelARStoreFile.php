<?php
class ModelARStoreFile extends AbsModel
{
	public $namefile;
	public $descr;
	public $url;
	public $w_img;
	public $h_img;
	public $sort;
	public $classprocdownload;
	//build
	public $user_folder;
	public $file;
	public $force_save;
	public $is_randName;
	public $is_addFile;

	public function tableName()
	{
		return 'setcms_'.strtolower(get_class($this));
	}
	
	public function rules()
	{
		array(
			array('namefile', 'length', 'max'=>60),
			array('descr, url', 'length', 'max'=>255),
			array('w_img, h_img', 'length', 'max'=>10),
			array('descr, w_img, h_img', 'default', 'value'=>''),
			array('sort', 'default', 'value'=>0),
			//build
			array('file', 'file', 'safe'=>true), // 'allowEmpty'=>true,
			array('classprocdownload, user_folder, force_save', 'safe'),
			array('is_randName', 'boolean'),
			array('is_addFile', 'boolean'),
		);
	}

	public function attributeLabels() {
		return array(
			'descr' => 'description and alt img',
			'url' => 'url file',
			'w_img' => 'weight image',
			'h_img' => 'height image',
		);
	}

	public function ElementsForm() {
		return array(
			'file'=>array(
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
			'url'=>array(
				'type'=>'text',
			),
			'desc'=>array(
				'type'=>'textarea',
			),
			'user_folder'=>array(
				'type'=>'text',
			),
		);
	}
	protected function beforeDelete() {
		//
		return parent::beforeDelete();
	}

	protected function beforeSave() {
		if(parent::beforeSave()!==false) {
			//
		}
		else return parent::beforeSave();
	}
}
