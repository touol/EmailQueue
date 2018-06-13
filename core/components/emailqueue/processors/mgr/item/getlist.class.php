<?php

class EmailQueueItemGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'EmailQueueItem';
    public $classKey = 'EmailQueueItem';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    //public $permission = 'list';


    /**
     * We do a special check of permissions
     * because our objects is not an instances of modAccessibleObject
     *
     * @return boolean|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = trim($this->getProperty('query'));
		$status = trim($this->getProperty('status'));
		if ($status) {
			$c->where(array(
				'`'.$this->classKey.'`.`status`' => "{$status}",
			));
		}
		if ($query) {
            $c->where(array(
                'to:LIKE' => "%{$query}%",
                'OR:subject:LIKE' => "%{$query}%",
				'OR:sender_package:LIKE' => "%{$query}%",
            ));
        }

        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();
        $array['actions'] = array();

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('emailqueue_item_update'),
            //'multiple' => $this->modx->lexicon('emailqueue_items_update'),
            'action' => 'updateItem',
            'button' => true,
            'menu' => true,
        );

		//send
		$array['actions'][] = array(
			'cls' => '',
			'icon' => 'icon icon-send',
			'title' => $this->modx->lexicon('emailqueue_email_send'),
			'multiple' => $this->modx->lexicon('emailqueue_emails_send'),
			'action' => 'sendEmail',
			'button' => true,
			'menu' => true,
		);

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('emailqueue_item_remove'),
            'multiple' => $this->modx->lexicon('emailqueue_items_remove'),
            'action' => 'removeItem',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }

}

return 'EmailQueueItemGetListProcessor';