<?php
Yii::import('application.modules.myobj.src.procFilesStorage.*');
class filesStorage extends AbsModel
{
	public $title;
	public $descr;
	public $url;
	public $sizeof;
	public $w_img;
	public $h_img;
	public $sort;
	//build
    public $user_folder;
	public $file;
	public $classProcDownload;
    public $is_randName;
    public $is_addFile;
    
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
	//dynam rules
    public $_rules = array(
		array('title', 'length', 'max'=>60),
		array('descr, url', 'length', 'max'=>255),
		array('sizeof, w_img, h_img', 'length', 'max'=>10),
		array('descr, sizeof, w_img, h_img', 'default', 'value'=>''),
		array('sort', 'default', 'value'=>0),
		//build
		array('file', 'file', 'safe'=>true), // 'allowEmpty'=>true,
		array('classProcDownload, user_folder', 'safe'),
        array('is_randName', 'boolean'),
        array('is_addFile', 'boolean'),
	);
    protected static function getNameClassProcDownload() {
        if(count($_POST)) {
            $attributes_form = $_POST['EmptyForm'];
            $nameClassProc = $attributes_form['classProcDownload'];
            return $nameClassProc;
        }
        return '';
    }
    public function rules()
    {
        $nameClassProc = static::getNameClassProcDownload();
        if($nameClassProc) {
            $this->_rules = $nameClassProc::setRules($this);
        }
        return $this->_rules;
    }
	
	public function attributeLabels() {
        return array(
            'descr' => 'description and alt img',
			'url' => 'url file',
			'w_img' => 'weight image',
			'h_img' => 'height image',
			'sizeof' => 'size file',
       );
    }
	
    public function ElementsForm() {
        return array(
			'classProcDownload'=>array(
                'type'=>'dropdownlist',
                'items'=>UCms::getInstance()->config['ClassesFilesStorageProc'],
            ),
			'file'=>array(
                'type'=>'CMultiFileUpload', //or file
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
    }
    protected function beforeDelete() {
        if(!parent::beforeDelete()) return false;
        $this->deleteFiles();
        return true;
    }

    public function deleteFiles() {
        $dirhome = Yii::getPathOfAlias('webroot.'.UCms::getInstance()->config['homeDirStoreFile']).DIRECTORY_SEPARATOR;
        $files = json_decode($this->url);
        if(!is_array($files)) $files = array($this->url);
        foreach($files as $urlelem) {
            $FilesPath=$dirhome.$urlelem;
            if(is_file($FilesPath)) unlink($FilesPath);
        }
    }

	public function afterSave() {
        if(parent::afterSave()!==false) {
            $nameClassProc = static::getNameClassProcDownload();
            /* var @file CUploadedFile*/
            $files = CUploadedFile::getInstancesByName('EmptyForm[file]'); //or not Multiple getInstanceByName
            return $nameClassProc::procFile($files,$this);
		}
	}
}

