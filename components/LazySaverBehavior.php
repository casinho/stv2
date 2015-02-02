<?php

Yii::import('AttributesBackupBehavior');

/**
 * Prevent save() command from being executed if no attributes have changed
 * The AttributesBackupBehavior class is available here:
 * http://www.yiiframework.com/extension/attributesbackupbehavior/
 */

class LazySaverBehavior extends AttributesBackupBehavior {

    /**
     * Prevent save() command from being executed if no attributes have changed
     * @param CModelEvent $event
     */
    public function beforeSave($event) {
        $owner = $this->getOwner();
        if (!$owner->getIsNewRecord() && !$this->attributesChanged()) {
            $event->isValid = false;
        }
        parent::beforeSave($event);
    }
}
