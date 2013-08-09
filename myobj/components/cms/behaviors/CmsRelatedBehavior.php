<?php
class CmsRelatedBehavior extends CActiveRecordBehavior
{
	public function links_edit($type, $name_releated_param, $arrobjects=Null, $fk=Null) { //$arrobjects = array(objmodel,objmodel) OR array(1,2,3,6,56,...) OR (int)2433
		$model = $this->getOwner();
		//print_r($model);exit;
		$objRelation = $model->getMetaData()->relations[$name_releated_param];
		$relation = $model->relations();
		$TYPE_relation = $relation[$name_releated_param][0];
		$className = $objRelation->className;
		if($TYPE_relation == CActiveRecord::MANY_MANY) {
			unset($relation);
			$arrDataM = preg_split('/[,()]/', $objRelation->foreignKey);
			$namemmtable = trim($arrDataM[0]);
			$mmname_this_key = trim($arrDataM[1]);
			$mmname_to_key = trim($arrDataM[2]);

			$PKslink_past = array();
			if(is_array($arrobjects) && count($arrobjects)) {
				foreach($arrobjects as $objrel) {
					$PKslink_past[] = (is_object($objrel))?$objrel->primaryKey:$objrel;
				}
			}
			else {
				$PKslink_past[] = (is_object($arrobjects))?$arrobjects->primaryKey:$arrobjects;
			}
		}
		elseif($TYPE_relation == CActiveRecord::HAS_MANY) {
			$namemmtable = $className::model()->tableSchema->name;
		}

		$command = Yii::app()->db->createCommand();
		$transaction=Yii::app()->db->beginTransaction();
		try {
		switch($type) {
			case 'set':
				$valuepk = ($arrobjects==Null) ? Null : ((is_object($arrobjects))?$arrobjects->primaryKey:$arrobjects);
				$command->update($model->tableName(), array($objRelation->foreignKey => $valuepk,),
					$model->tableSchema->primaryKey.'=:'.$model->tableSchema->primaryKey, array(':'.$model->tableSchema->primaryKey=>$model->primaryKey));
				break;
			//is CActiveRecord::MANY_MANY OR HAS_MANY
			case 'add':

					if($TYPE_relation == CActiveRecord::MANY_MANY) {
						foreach($PKslink_past as $PKobj) {
							$command->insert($namemmtable, array_merge(array($mmname_this_key=>$model->primaryKey,$mmname_to_key=>$PKobj),((is_array($fk))?$fk:array())));
						}
					}
					elseif($TYPE_relation == CActiveRecord::HAS_MANY) {
						$command->update($namemmtable, array($objRelation->foreignKey => $fk), array('in', $className::model()->tableSchema->primaryKey, $arrobjects));
					}

				break;
			case 'edit':
				foreach($PKslink_past as $PKobj) {
					$command->update($namemmtable, $fk, array('and', $mmname_this_key.'='.$model->primaryKey, $mmname_to_key.'='.$PKobj));
				}
				break;
			case 'select':
				if(!$model->isNewRecord) {
					return $command->select($fk)->from($namemmtable)->where(array('and', $mmname_this_key.'='.$model->primaryKey, $mmname_to_key.'='.$arrobjects))->queryRow();
				}
				return false;
				break;
			case 'remove':
				if($TYPE_relation == CActiveRecord::MANY_MANY) {
					$command->delete($namemmtable,array('and', $mmname_this_key.'='.$model->primaryKey, array('in', $mmname_to_key, $PKslink_past)));
				}
				elseif($TYPE_relation == CActiveRecord::HAS_MANY) {
					$command->update($namemmtable, array($objRelation->foreignKey => null), array('in', $className::model()->tableSchema->primaryKey, $arrobjects));
				}
				break;
			case 'clear':
				$command->delete($namemmtable,array('or', $mmname_this_key.'='.$model->primaryKey, $mmname_to_key.'='.$model->primaryKey));
				break;

		}
			$transaction->commit();
				}
		catch(Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	return true;
	}
}
