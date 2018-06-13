<?php

class EmailQueueItemCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'EmailQueueItem';
    public $classKey = 'EmailQueueItem';
    public $languageTopics = array('emailqueue');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
		$this->setProperty('date', date("Y-m-d H:i:s"));
        return parent::beforeSet();
    }

}

return 'EmailQueueItemCreateProcessor';