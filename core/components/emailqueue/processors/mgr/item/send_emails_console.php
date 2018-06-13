<?php
set_time_limit(3600);
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load($modx->config['manager_language'].':emailqueue:default');

$modx->addPackage('emailqueue', $modx->getOption('core_path') . 'components/emailqueue/model/');
if(isset($_POST['send_count'])){
	$send_count = $_POST['send_count'];
}else{
	$send_count = 30;
}
$modx->log(modX::LOG_LEVEL_INFO,'Отправлять по '.$send_count.' писем');
$c = $modx->newQuery('EmailQueueItem');
$c->where(array('status'=>1));
$c->limit($send_count);
$emails = $modx->getIterator('EmailQueueItem',$c);

$count = 0;	
foreach($emails as $email){
	
	if ($email->send()) {
		$modx->log(modX::LOG_LEVEL_INFO,'Письмо '.$email->to.' отправлено');
		$count++;
	}
	$modx->mail->reset();
}
$modx->log(modX::LOG_LEVEL_ERROR,'Отправлено '.$count.' писем!');
$modx->log(modX::LOG_LEVEL_INFO,'COMPLETED');
sleep(1);
return $modx->error->success();
?>