<?php
include(ASH_ROOTPATH . '/lib/PHPMailer/class.phpmailer.php');

class Ashtree_Common_Sendmail extends PHPMailer {
	
	private $_debug;
	private $_params;
	
	// Set default variables for all new objects
	public $From     = "beta@Ashtree.co.za";
	public $FromName = "Ashtree Beta Testing";
	public $Host     = "mail.Ashtree.co.za";
	public $Mailer   = "smtp";
	#public $WordWrap = 75;
	


	public function __construct(){
		
		$this->_debug = new Ashtree_Common_Debug();
		
		$this->CharSet     = 'UTF-8';
		
		// SMTP Details
		$this->isSMTP();
		$this->SMTPDebug   = FALSE;
		$this->Host        = 'mail.mapaproject.org';
		$this->Port        = '25';
		
		$this->SMTPAuth    = TRUE;
		$this->Username    = 'admin@mapaproject.org';
		$this->Password    = 'password';
		
		#$mail->isSendmail();
		
		#$this->AddBCC('beta@Ashtree.co.za');
		
		
	}
	
	public function __set($key, $value) {
		switch($key) {
			case 'to':
			case 'cc':
			case 'bcc':
				$this->_params[$key][] = $value;
				break;
			case 'message':
				$this->_params['message_html'] = $value;
				$this->_params['message_text'] = strip_tags($value);
				break;
			case 'attach':
				$this->_params['attachment'][] = $value;
				break;
			default:
				$this->_params[$key] = $value;
		}
	}
	
	public function __get($key) {
		return array_key_exists($key, $this->_params) ? $this->_params[$key] : FALSE;
	}
	
	public function __isset($key) {
		return isset($this->_params[$key]);
	}
	
	
	public function __unset($key) {
		unset($this->_params[$key]);
	}
	
	public function __invoke() {
		$dbg = Ashtree_Common_Debug::instance();
		
		$dbg_message = "Sending email {$this->subject}";
		$this->_build_html_message();
		
		$this->From    = $this->from;
		if (substr_count($this->from, '<')) {
			$from = explode('<', str_replace('>', '', $this->from));
			$this->FromName = $from[0];
			$this->From = $from[1];
		}
		
		$this->Subject = $this->subject;
		$this->Body    = $this->message_html;
		$this->AltBody = $this->message_text;
		
		$dbg_message .= " to:";
		foreach((array)$this->_params['to'] as $to) {
			$this->AddAddress($to);
			$dbg_message .= " {$to},";
		}
		
		$dbg_message .= " cc:";
		foreach((array)$this->_params['cc'] as $cc) {
			$this->AddCC($cc);
			$dbg_message .= " {$cc},";
		}
		
		$dbg_message .= " bcc:";
		foreach((array)$this->_params['bcc'] as $bcc) {
			$this->AddBCC($bcc);
			$dbg_message .= " {$bcc},";
		}
		
		$dbg_message .= " attachment(s):";
		foreach((array)$this->_params['attachment'] as $attachment) {
			$this->AddAttachment($attachment);
			$dbg_message .= " {$attachment},";
		}
		
		//echo dump($this, 1);
		//exit;
		$sent = $this->Send();
		
		
		$dbg->log("INFO", $dbg_message);
	
		if($sent) {
			$dbg->status("OK");
			$dbg->log("INFO", dump($this->message_text, 1));
		} else {	
			$dbg->status("FAILURE");
		}
		
		return $sent;
	}
	
	public function invoke() {
		return $this->__invoke();
	}
	
	// Replace the default error_handler
	function error_handler($error_message) {
		$this->_debug->title = "ERROR:: {$error_message}";
	}
	
	private function _build_html_message(){
		$this->message_html = <<<HEREDOC
			<html>
			    <body>
			        <table width="100%" cellpadding="0" cellspacing="2">
			        	<thead>
			        		<tr background="#CCC">
			        			<th>{$this->title}</th>
			        		</tr>
			        	</thead>
			        	<tbody>
			        		<tr>
			        			<td>{$this->message_html}</td>
			        		<tr>
			        	</tbody>
			        	<tfoot>
			        		<tr>
			        			<td align="center"><span style="font-size:0.8em"><br /><em>{$this->footer}</em></span></td>
			        		<tr>
			        	</tfoot>
			        </table>
			    </body>
			</html>
HEREDOC;
	}
	
}