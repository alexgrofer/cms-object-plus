<?php
class classFilesStorageDefault
{
    CONST URL_HOME = 'filescms';
	public static function setRules(filesStorage $model) {
        $files = CUploadedFile::getInstancesByName('EmptyForm[file]');
        if(count($files)>1) {
            $model->_rules[] = array('title','required');
        }
        elseif(count($files)==0) {
            $model->_rules[] = array('title','required');
            $model->_rules[] = array('url','required');
        }
		foreach($model->_rules as $key => $elem) {
			if($elem[0]=='file') {
                $model->_rules[$key] = array('file', 'file', 'maxFiles'=>10, 'maxSize'=>((1024*1024)*16), 'allowEmpty'=>true, 'safe'=>true); //'types' => 'zip, rar',
				break;
			}
		}
        return $model->_rules;
	}
    protected static function randName() {
        $varStr = 'qwertyuiopasdfghjklzxcvbnm1234567890';
        return substr(str_shuffle($varStr.$varStr),-22);
    }
    public static function deleteFiles(filesStorage $model) {
        $dirhome = Yii::getPathOfAlias('webroot.'.UCms::getInstance()->config['homeDirStoreFile']).DIRECTORY_SEPARATOR;
        foreach(json_decode($model->url) as $urlelem) {
            $FilesPath=$dirhome.$urlelem;
            if(is_file($FilesPath)) unlink($FilesPath);
        }
    }
    public static function procFile($files,filesStorage $model) {
        //action file
        /*
         * удалить старый файл (и миниатюры если имеются)
         * --название рандом
         * .zip
         * сделать миниатюры
         */
        //удалить старые файлы




        $dirhome = Yii::getPathOfAlias('webroot.'.UCms::getInstance()->config['homeDirStoreFile']).DIRECTORY_SEPARATOR.(static::URL_HOME).DIRECTORY_SEPARATOR;
        $model->user_folder = trim($model->user_folder);
        $url_dir = static::URL_HOME.'/';
        if($model->user_folder) {
            $url_dir .= $model->user_folder.((substr($model->user_folder,-1)=='/')?'':'/');
            $dirhome .= $model->user_folder.((substr($model->user_folder,-1)==DIRECTORY_SEPARATOR)?'':DIRECTORY_SEPARATOR);
        }

        //rename
        if(!count(json_decode($model->url)) && !$files && trim($model->title)!='' && !$model->isNewRecord && ($model->url && !json_decode($model->url))) {
            $name = $model->title;
            if($model->is_randName) {
                $name = static::randName().(substr(strrchr($files[0]->name,'.'),0));
                $model->title = $name;
            }
            rename(Yii::getPathOfAlias('webroot.'.UCms::getInstance()->config['homeDirStoreFile']).DIRECTORY_SEPARATOR.$model->url, $dirhome.$name);
            $model->url = $url_dir.$name;
            $model->updateByPk($model->id,$model->attributes);
            return true;
        }

        if(!$model->isNewRecord && !$model->is_addFile && !count(json_decode($model->url))) {
            $model->deleteFiles();
        }
        //action AbsModel
        /*
         *
         */
        if(count($files)==1) {
            $name = $files[0]->name;
            if($model->is_randName) {
                $name = static::randName().(substr(strrchr($files[0]->name,'.'),0));
            }

            $model->title = $name;

            if($model->is_addFile || count(json_decode($model->url))) {
                if(count(json_decode($model->url))) {
                    $decode_url = json_decode($model->url);
                    $decode_url[] = $url_dir.$name;
                    $model->url = json_encode($decode_url);
                }
                else $model->url = $url_dir.$name;

                $model->sizeof += $files[0]->size;
            }
            else {
                $model->url = $url_dir.$name;
                $model->sizeof = $files[0]->size;
            }


            $files[0]->saveAs($dirhome.$name);
        }
        else {
            $names_url = array();
            $sumsize = 0;

            foreach($files as $file) {
                $name = $file->name;
                if($model->is_randName) {
                    $name = static::randName().(substr(strrchr($file->name,'.'),0));
                }
                $names_url[] = $url_dir.$name;
                $sumsize += $file->size;
                $file->saveAs($dirhome.$name);
            }
            if($model->is_addFile || count(json_decode($model->url))) {
                $decode_url = (count(json_decode($model->url)))?json_decode($model->url):array($model->url);
                $names_url = array_merge($names_url, $decode_url);
                $model->sizeof += $sumsize;
            }
            else {
                $model->sizeof = $sumsize;
            }
            $model->url = json_encode($names_url);
        }

        $model->updateByPk($model->id,$model->attributes);
        return true;
    }
}
