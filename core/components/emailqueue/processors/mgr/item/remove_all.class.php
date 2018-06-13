<?php

/**
 * Remove an Queue
 */
class EmailQueueItemRemoveAllProcessor extends modProcessor {
	public $objectType = 'EmailQueueItem';
	public $classKey = 'EmailQueueItem';


	/** {inheritDoc} */
	public function process() {
		$this->modx->removeCollection($this->classKey, array());

		return $this->success();
	}
}

return 'EmailQueueItemRemoveAllProcessor';
