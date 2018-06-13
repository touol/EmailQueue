<?php

class EmailQueueEmailsSendProcessor extends modObjectProcessor
{
    public $objectType = 'EmailQueueItem';
    public $classKey = 'EmailQueueItem';
    public $languageTopics = array('emailqueue');
    //public $permission = 'save';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }
		
		$emails = $this->modx->getIterator($this->classKey,array('status'=>3));
		foreach($emails as $email){
			$email->remove();
		}
        return $this->success();
    }

}

return 'EmailQueueEmailsSendProcessor';
