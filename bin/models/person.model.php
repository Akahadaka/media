<?php 

final class Person_Model extends Ashtree_Model {
	private $identity;
	private $username;
	private $security = 1;
	
	private $firstname;
	private $lastname;
	private $email;
	private $phone;
	private $invited;
	private $special = 0;
	
	private $created;
	private $updated;
	
	private $tablename = 'dea_people';
	
	public function __construct() {
	
	}
	
	public function getUsername() {
		return $this->email;
	}
		
	public function getVars() {
		return get_object_vars($this);
	}
	
	public function setVars($input) {
		// Fix for checkbox not posting any value
		$this->special = 0;
		foreach($_POST as $key=>$val) {
			$this->$key = $val;
		}
	}
	
	public function sendmail_forgot($username) {
		
		$this->username = $this->email = $username;
		$this->password = $password = Ashtree_Common::random_alphanumeric(6);
		
		if ($this->password != '') {
			$sql = Ashtree_Database_Connection::instance(ASH_SITE_NAME);
			$sql->query = "UPDATE ash_userinfo SET password = MD5(:password) WHERE username = :username";
	
			$sql->bind(':password', $this->password);
			$sql->bind(':username', $this->email);
			$sql->invoke();
			if (!$sql->affected) {
				return false;
			}
			
			$sql->query = "
				SELECT * FROM {$this->tablename} data
				LEFT JOIN ash_userinfo info ON info.identity =  data.identity
				WHERE info.username = :username
			";
			$sql->bind(':username', $username);
			$sql->invoke();

			foreach ($sql->getFirstRow() as $key=>$value) {
				$this->$key = $value;
			}
			
			$this->password = $password;
		}
		
		$mail = new Ashtree_Common_Sendmail();
		#$mail->to           = "{$this->firstname} {$this->lastname} <{$this->email}>";
		$mail->to           = $this->email;
		$mail->from         = "Ashtree Media <beta+media@ashtree.co.za>";
		$mail->subject      = $mailer->title = "Media Password Reset";
		$mail->isHTML();
		$site_url = ASH_ROOTHTTP;
		$mail->message      = <<<MAIL
<p>Dear {$this->firstname}</p>
<p>
You have requested a password reset. Your login credential are as follows
</p>
<table border="0">
	<tr>
		<th>Username:</th>
		<td>{$this->username}</td>
	</tr>
	<tr>
		<th>Password:</th>
		<td>{$this->password}</td>
	</tr>
</table>
<p>
You may log in at <a href="{$site_url}login">{$site_url}login</a>.
</p>
<p>
Kind Regards<br />
--<br />
The Department of Environmental Affairs
</p>
MAIL;
		return $mail->invoke();
	}
	
	public function sendmail_welcome() {
		$mail = new Ashtree_Common_Sendmail();
		$mail->to           = $this->email;
		$mail->from         = "Ashtree Media <beta+media@ashtree.co.za>";
		$mail->subject      = $mailer->title = "Media Online Registration";
		$mail->isHTML();
		
		$site_url = ASH_ROOTHTTP;
		
		$mail->message      = <<<MAIL
<p>Dear {$this->firstname}</p>
<p>
Welcome to the Department of Environmental Affairs Website. Your login credential are as follows
</p>
<table border="0">
	<tr>
		<th>Username:</th>
		<td>{$this->email}</td>
	</tr>
	<tr>
		<th>Password:</th>
		<td>{$this->password}</td>
	</tr>
</table>
<p>
You may log in at <a href="{$site_url}login">{$site_url}login</a>.
</p>
<p>
Kind Regards<br />
--<br />
The Department of Environmental Affairs
</p>
MAIL;
		$mail->invoke();
	}
	
	public function sendmail_notify() {
		$mail = new Ashtree_Common_Sendmail();
		$mail->to           = 'beta+media@ashtree.co.za';
		$mail->from         = "Ashtree Media <beta+media@ashtree.co.za>";
		$mail->isHTML();
		
		$site_url       = ASH_ROOTHTTP;
		$special_access = ($this->special) ? 'Yes' : 'No';
		$special_url    = ($this->special) ? <<<MAIL
 and has requested special access. <a href="{$site_url}user/account/update/{$this->identity}">Grant Special Access to {$this->firstname}</a>
MAIL
		: "";
		$special_subject = ($this->special) ? ' with Special Access Request' : '';
		
		$mail->subject      = $mailer->title = "DEA Online Registration{$special_subject}";
		$mail->message  = <<<MAIL
<p>Dear DEA Admin</p>
<p>
A new user has been created on the system{$special_url}.
</p>
<table border="0">
	<tr>
		<th>Name:</th>
		<td>{$this->firstname} {$this->lastname}</td>
	</tr>
	<tr>
		<th>Email:</th>
		<td>{$this->email}</td>
	</tr>
	<tr>
		<th>Contact:</th>
		<td>{$this->phone}</td>
	</tr>
	<tr>
		<th>Special Access Requested:</th>
		<td>{$special_access}</td>
	</tr>
</table>
<p>
--<br />
The Department of Environmental Affairs
</p>
MAIL;
		$mail->invoke();
	}
	
	public function select($conn, $id) {
		$this->id = $id;
		
		// Userdata
		$conn->query = "
			SELECT * FROM {$this->tablename} data 
			LEFT JOIN ash_userinfo info ON info.identity =  data.identity
			WHERE data.identity = :identity
		";
		$conn->bind(':identity', $id);
		$conn->invoke();

		$result = $conn->getFirstRow();
		foreach ((array)$result as $key=>$value) {
			$this->$key = $value;
		}

		return $conn->affected;
	}
	
	public function insert($conn) {
		
		// Upload files
		if (isset($_FILES['PROC_DOC'])) {
			$this->proc_doc = $_FILES['PROC_DOC']['name'];
		}
		
		// Insert into database
		$conn->query = "
			INSERT INTO ash_userinfo (
				username, 
				password, 
				security, 
				verified
			) VALUES (
				:email, 
				MD5(:password), 
				:security, 
				'1'
			)	
		";
		$conn->bind(':email', $this->email);
		$conn->bind(':password', $this->password);
		$conn->bind(':security', $this->security);
		$conn->invoke();
		$this->identity = $conn->getInsertId();
		
		$conn->query = "
			INSERT INTO {$this->tablename} (
				identity, 
				firstname, 
				lastname, 
				email, 
				phone, 
				special
			) VALUES (
				:identity, 
				:firstname, 
				:lastname, 
				:email, 
				:phone, 
				:special
			)	
		";
		$conn->bind(':identity', $this->identity);
		$conn->bind(':firstname', $this->firstname);
		$conn->bind(':lastname', $this->lastname);
		$conn->bind(':email', $this->email);
		$conn->bind(':phone', $this->phone);
		$conn->bind(':special', $this->special);
		$conn->invoke();
		
		$affected = $conn->affected;
		
		return $affected;
	}
	
	public function delete($conn) {
		$conn->query = "
			DELETE FROM {$this->tablename} WHERE id = :id
		";
		$conn->bind($this->getVars());
		$conn->invoke();
		
		return $conn->affected;
	}
	
	public function update($conn) {
		$usr = Ashtree_Common_Secure::userinfo();
		
		$conn->query = "
			UPDATE {$this->tablename} SET 
				firstname = :firstname,
				lastname = :lastname,	
				email = :email,
				phone = :phone,
				special = :special				
				
			WHERE identity = :id
		";

		$conn->bind($this->getVars());
		$conn->invoke();
		
		$conn->query = "
			UPDATE ash_userinfo SET
				username = :username,
				security = :security
			WHERE identity = :id
		";
		
		$conn->bind(':username', $this->email);
		$conn->bind(':security', $this->security);
		$conn->bind(':id', $this->id);
		$conn->invoke();
		
		if ($this->password != '') {
			$conn->query = "UPDATE ash_userinfo SET password = MD5(:password) WHERE identity = :id";
			
			$conn->bind(':password', $this->password);
			$conn->bind(':id', $this->id);
			$conn->invoke();
		}
		
		return true;
	}
}