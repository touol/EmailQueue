<?php

/**
 * @package emailqueue
 */
class EmailQueueItem extends xPDOSimpleObject
{
	/**
	 * Sends an email
	 *
	 * @return bool
	 */
	public function send() {
		/** @var modPHPMailer $mail */
		$mail = $this->xpdo->getService('mail', 'mail.modPHPMailer');
		$mail->set(modMail::MAIL_BODY, $this->body);
		if($this->from){
			$mail->set(modMail::MAIL_FROM, $this->from);
		}else{
			$mail->set(modMail::MAIL_FROM, $this->xpdo->getOption('emailsender'));
		}
		if($this->from_name){
			$mail->set(modMail::MAIL_FROM_NAME, $this->from_name);
		}else{
			$mail->set(modMail::MAIL_FROM_NAME, $this->xpdo->getOption('site_name'));
		}
		$mail->set(modMail::MAIL_SUBJECT, $this->subject);
		$mail->address('to', $this->to);
		if($this->reply_to) $mail->address('reply-to', $this->reply_to);
		$mail->setHTML(true);
		//$this->modx->mail->attach($attachment);
		if($this->attachments){
			$files = explode(',',$this->attachments);
			foreach($files as $file) {
				if(is_file(trim($file))) $mail->attach(trim($file));
			}
		}
		if (!$mail->send()) {
			$this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: '.$mail->mailer->ErrorInfo);
			$mail->reset();
			$this->status = 3;
			$this->save();
			return false;
		}
		else {
			$mail->reset();
			$this->status = 2;
			$this->sentdate = date("Y-m-d H:i:s");
			$this->save();
			return true;
		}
	}
}