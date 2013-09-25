<?php
class filesStorage extends AbsModel
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
	//dynam rules
	public $_rules = array(
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
	protected function getNameClassProcDownload() {
		if(count($_POST)) {
			$attributes_form = $_POST['EmptyForm'];
			$nameClassProc = $attributes_form['classprocdownload'];
			return $nameClassProc;
		}
		elseif($this->classprocdownload) {
			return $this->classprocdownload;
		}
		return 'classFilesStorageDefault';
	}
	public function rules()
	{
		$nameClassProc = $this->getNameClassProcDownload();
		$this->_rules = $nameClassProc::setRules($this);

		return $this->_rules;
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
		$arr_ElementsForm = array(
			'classprocdownload'=>array(
				'type'=>'dropdownlist',
				'items'=>Yii::app()->appcms->config['ClassesFilesStorageProc'],
			),
			'file'=>array(
				'type'=>'CMultiFileUpload', //or file
			),
			'force_save'=>array(
				'type'=>'checkbox', //or file
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
			'descr'=>array(
				'type'=>'textarea',
			),
			'user_folder'=>array(
				'type'=>'text',
			),
		);

		$nameClassProc = $this->getNameClassProcDownload();
		$arr_ElementsForm = $nameClassProc::editelems($arr_ElementsForm,$this);
		return $arr_ElementsForm;
	}
	protected function beforeDelete() {
		$nameClassProc = $this->getNameClassProcDownload();
		$nameClassProc::befdel($this->url);
		return parent::beforeDelete();
	}

	protected function beforeSave() {
		if(parent::beforeSave()!==false) {
			$nameClassProc = $this->getNameClassProcDownload();
			return $nameClassProc::procFile($this);
		}
		else return parent::beforeSave();
	}
}
