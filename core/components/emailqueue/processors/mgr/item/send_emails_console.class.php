<?php
class SendEmailConsoleProcessor extends modProcessor {

    public function process() {
		set_time_limit(3600);
		$o = '';
		$this->modx->getService('lexicon','modLexicon');
		$this->modx->lexicon->load($this->modx->config['manager_language'].':emailqueue:default');

		$this->modx->addPackage('emailqueue', $this->modx->getOption('core_path') . 'components/emailqueue/model/');
		if(isset($_POST['send_count'])){
			$send_count = (int)$_POST['send_count'];
		}else{
			$send_count = 30;
		}
		$this->modx->log(modX::LOG_LEVEL_INFO,'Отправлять по '.$send_count.' писем');
		$c = $this->modx->newQuery('EmailQueueItem');
		$c->where(array('status'=>1));
		$c->limit($send_count);
		$emails = $this->modx->getCollection('EmailQueueItem',$c);

		$count = 0;	

		//перед отправкой установить статус Отправлется, чтобы крон не отправил дважды
		foreach($emails as $email){
			$email->status = 4;
			$email->save();
		}

		foreach($emails as $email){
			
			if ($email->send()) {
				$this->modx->log(modX::LOG_LEVEL_INFO,'Письмо '.$email->to.' отправлено');
				$count++;
			}
			$this->modx->mail->reset();
		}
		$this->modx->log(modX::LOG_LEVEL_ERROR,'Отправлено '.$count.' писем!');
		$this->modx->log(modX::LOG_LEVEL_INFO,'COMPLETED');
		sleep(1);
		return $this->success($o);
	}
}

return 'SendEmailConsoleProcessor';	
?>