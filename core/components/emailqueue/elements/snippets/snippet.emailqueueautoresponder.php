<?php
/*
Cниппет для drop-in replacement замены хука FormItAutoResponder для FormIt/AjaxForm
Просто в вызове поменять хук на EmailQueueAutoResponder 
[[!Formit?
    &hooks=`EmailQueue,EmailQueueAutoResponder`
    &emailTpl=`feedbackEmail`
    &emailTo=`test@mail.ru`
    &emailSubject=`Сообщение с сайта`
    &replyTo=`[[+email]]`
    &emailFrom=`[[++emailsender]]`
	
	&fiarTpl=`feedbackEmail`
    &fiarSubject=`Сообщение с сайта`
    &fiarReplyTo=`[[+email]]`
    &fiarFrom=`[[++emailsender]]`
]]
Поддержку вложений не делал, т.к. не было необходимости.
И не все параметры FormIt поддерживает (например emailHtml, emailToName, emailCC, итд), т.к. компонент их не учитывает.
*/
$fields = $hook->getValues();

//переделка с FormItAutoResponder
$tpl = $modx->getOption('fiarTpl', $hook->formit->config, 'fiDefaultFiarTpl', true);
$mailFrom = $modx->getOption('fiarFrom', $hook->formit->config, $modx->getOption('emailsender'));
$mailFromName = $modx->getOption('fiarFromName', $hook->formit->config, $modx->getOption('site_name'));
$mailSender = $modx->getOption('fiarSender', $hook->formit->config, $modx->getOption('emailsender'));
$mailSubject = $modx->getOption('fiarSubject', $hook->formit->config, '[[++site_name]] Auto-Responder');
$mailSubject = str_replace(
	array('[[++site_name]]', '[[++emailsender]]'),
	array($modx->getOption('site_name'), $modx->getOption('emailsender')),
	$mailSubject
);
//$fiarFiles = $modx->getOption('fiarFiles', $hook->formit->config, false);
//$isHtml = $modx->getOption('fiarHtml', $hook->formit->config, true);
$toField = $modx->getOption('fiarToField', $hook->formit->config, 'email');
$multiSeparator = $modx->getOption('fiarMultiSeparator', $hook->formit->config, "\n");
$multiWrapper = $modx->getOption('fiarMultiWrapper', $hook->formit->config, '[[+value]]');
$required = $modx->getOption('fiarRequired', $hook->formit->config, true);
if (empty($fields[$toField])) {
	if ($required) {
		$modx->log(
			\modX::LOG_LEVEL_ERROR,
			'[FormIt] EmailQueueAutoResponder could not find field `'.$toField.'` in form submission.'
		);
		return false;
	} else {
		return true;
	}
}
/* handle checkbox and array fields */
foreach ($fields as $k => $v) {
	if (is_array($v) && !empty($v['name']) && isset($v['error']) && $v['error'] == UPLOAD_ERR_OK) {
		$fields[$k] = $v['name'];
	} elseif (is_array($v)) {
		$vOpts = array();
		foreach ($v as $vKey => $vValue) {
			if (is_string($vKey) && !empty($vKey)) {
				$vKey = $k.'.'.$vKey;
				$fields[$vKey] = $vValue;
			} else {
				$vOpts[] = str_replace('[[+value]]', $vValue, $multiWrapper);
			}
		}
		$newValue = implode($multiSeparator, $vOpts);
		if (!empty($vOpts)) {
			$fields[$k] = $newValue;
		}
	}
}
/* setup placeholders */
$placeholders = $fields;
$mailTo= $fields[$toField];
//$message = $formit->getChunk($tpl, $placeholders);
$message = $hook->formit->getChunk($tpl, $fields);

/* add attachments */
/*if ($fiarFiles) {
	$fiarFiles = explode(',', $fiarFiles);
	foreach ($fiarFiles as $file) {
		$modx->mail->mailer->AddAttachment($file);
	}
}*/
//переделка с FormItAutoResponder конец        

if (!$EmailQueue = $modx->getService('emailqueue', 'EmailQueue', $modx->getOption('emailqueue_core_path', null,
	$modx->getOption('core_path') . 'components/emailqueue/') . 'model/emailqueue/', array())) {
	$hook->addError('email', 'При отправке произошла ошибка');
	return false;
}

$data = array(
	'sender_package' => 'FormIt',
	'from' => $mailFrom,
	'from_name' => $mailFromName,
	'subject' => $mailSubject,
	'body' => $message,
	'date' => date("Y-m-d H:i:s"),
);
/* reply to */
$emailReplyTo = $modx->getOption('fiarReplyTo', $this->formit->config, $mailFrom);
$emailReplyTo = $hook->_process($emailReplyTo, $fields);

if (!empty($emailReplyTo)) {
    $date['replyto'] = $emailReplyTo;
}
$mailTo = array_map('trim', explode(',', $mailTo));

foreach ($mailTo as $to) {
    $to = $hook->_process($to, $fields);
    if (!empty($to)) {
        $data['to'] = $to;
        $queue_email = $modx->newObject('EmailQueueItem');
        $queue_email->fromArray($data);
        $queue_email->save();
    }
}

return true;