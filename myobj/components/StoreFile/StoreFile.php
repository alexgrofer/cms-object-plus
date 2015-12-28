<?php
class StoreFile extends CComponent {
	const TYPE_IMAGE = 1;
	const TYPE_DOC = 2;
	const TYPE_VIDEO = 3;
	const TYPE_MUSIC = 4;

	const MAX_FILES_FOLDER = 10;

	public static $last_folder;

	public function init() {

	}

	public function loadFile(CUploadedFile $file, $type) {
		$code_name = \MYOBJ\appscms\core\base\SysUtilsString::read_str(10);

		if(static::$last_folder) {
			$folder = static::$last_folder;
		}
		else {
			$last_obj = current(ImageModel::model()->findAll(array('order'=>'id DESC', 'limit'=>1)));
			$folder = $last_obj->folder;
			$absFolder = (FOLDER_UPLOAD).$folder;

			if(file_exists($absFolder)==false || (count(scandir($absFolder)) - 2)>=static::MAX_FILES_FOLDER) {
				$folder = \MYOBJ\appscms\core\base\SysUtilsString::read_str(25);
				static::$last_folder = $folder;
				CFileHelper::createDirectory((FOLDER_UPLOAD).$folder);
			}
		}

		$file->saveAs((FOLDER_UPLOAD).$folder.'/'.$code_name.'.'.$file->getExtensionName());

		$objModel = new ImageModel();
		//base
		$objModel->code_name = $code_name;
		$objModel->folder = $folder;
		$objModel->size = $file->getSize();

		if($objModel->validate()) {
			$objModel->save();
		}
		else {
			//кинуть исключение перехватить в контроллере и написать там что системная ошибка отправить в логи админу
		}

		return $objModel;
	}
}
