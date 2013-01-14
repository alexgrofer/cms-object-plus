<?php
class User extends CActiveRecord
{
    public $login;
    public $password;
    public $email;
    
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return 'setcms_'.strtolower(get_class($this));
    }
    public function relations()
    {
        return array(
            'userpasport'=>array(self::BELONGS_TO, 'UserPasport', 'userpasport_id'), // test
            'group'=>array(self::MANY_MANY, 'Ugroup', 'setcms_user_ugroup(user_id, group_id)'),
        );
    }
    public function rules() {
        return array(
            array('login, password, email', 'required'),
            array('login, password, email', 'length', 'max'=>128),
            array('email', 'email'),
            array('login, email', 'unique')
        );
    }
    protected function beforeSave() {
        if(!$this->isNewRecord) {
            $objuser = $this->model()->findByPk($this->id);
            if($objuser->password!=$this->password) {
                $this->password = md5($this->password);
            }
        }
        else $this->password = md5($this->password);
        return parent::beforeSave();
    }
    public function ElementsForm() {
        return array(
            'login'=>array(
                'type'=>'text',
            ),
            'email'=>array(
                'type'=>'text',
            ),
            'password'=>array(
                'type'=>'password',
            ),
    );
    }
    public function behaviors()
    {
        return array(
            'UserRelated'=>array(
                'class'=>'ext.behaviors.model.RelatedBehavior',
            ),
            'UserFormModel'=>array(
                'class'=>'application.modules.myobj.extensions.behaviors.model.FormModel',
            ),
        );
    }

}