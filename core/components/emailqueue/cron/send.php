<?php

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$modx->addPackage('emailqueue', MODX_CORE_PATH . 'components/emailqueue/model/');

//send email
$q = $modx->newQuery('EmailQueueItem');
$q->where(array('status'=>1));
$q->limit($modx->getOption('emailqueue_limit', null, 50, true));

$queue = $modx->getCollection('EmailQueueItem', $q);

//перед отправкой установить статус Отправлется, чтобы крон не отправил дважды
foreach($queue as $email){
	$email->status = 4;
	$email->save();
}
/** @var EmailQueueItem $email */
foreach ($queue as $email) {
	$email->send();
}

//clear queue
$emailqueue_store_days = $modx->getOption('emailqueue_store_days', null, 30, true);
$q = $modx->newQuery('EmailQueueItem');
$q->where(array('date:<'=>date('Y-m-d', strtotime('-'.$emailqueue_store_days.' days'))));

$queue = $modx->getIterator('EmailQueueItem', $q);
/** @var EmailQueueItem $email */
foreach ($queue as $email) {
	$email->remove();
}

