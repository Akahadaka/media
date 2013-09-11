<?php
/**
 * Designed to better facilitate access from form logins,
 * with user stored credentials validation
 * and added page access level security 
 *
 * @package Ashtree_Common_Secure
 * @author andrew.nash
 * @version 2.0
 * 
 * @change 
 */
 
define('SECURE_NONE',       0, TRUE); 	//0 - none
define('SECURE_REGISTERED', 1, TRUE);	//1 - registered user
define('SECURE_SPECIAL',    2, TRUE);	//2 - special registered user
define('SECURE_ADMIN',      3, TRUE); 	//3 - admin user (moderator)
define('SECURE_FULLADMIN',  4, TRUE); 	//4 - super admin user (owner)
define('SECURE_SUPERADMIN', 5, TRUE); 	//5 - full administrator (developer)

class Ashtree_Common_Secure
{ 
	/**
	 * @access privte
	 * @param Object $_instance
	 */
    private static $_instance;
    
    /*
	 * Session group for the user information
	 * @access public
	 * @param session string
	 */
	 private static $_session = 'Ashtree_Secure';
	
	/**
	 * @access private
	 * @param Array $_params
	 */
	private $_params = array(); 

	/**
	 * @access private
	 * @param Object $_debug
	 */
	private $_debug;
	 
	 /**
	 * The level of access a user has. See constants
	 * @access public
	 * @param Mixed $_security
	 */
	 public $security;
	 
	 /**
	 * An array of userinfo
	 * @access public
	 * @param Array $userinfo
	 */
	 public $userinfo = array();
	 
	 public $connection;
	 
	 public $multiple = TRUE;
	
	/**
	 */
	public function __construct()
	{
		$this->_debug = Ashtree_Common_Debug::instance();
		
		//Make the userinfo accessible
		$this->userinfo = self::userinfo();
	}
	
	/** 
	 *
	 */
	public function __get($key)    
	{
		return array_key_exists($key, $this->_params) ? $this->_params[$key] : 0;
	}
	
	/**
	 *
	 */
	public function __set($key, $value)
	{
	    switch($key) {
	        case 'success':
	        case 'gotopath':
	            $_SESSION[self::$_session]['gotopath'] = $value;
	        default :
	            $this->_params[$key] = $value;
	    }
	    
	}
	
	/**
	 *
	 */
	public function __isset($key)      
	{
		return isset($this->_params[$key]);
	}
	
	/**
	 *
	 */	
	public function __unset($key)
	{
		unset($this->_params[$key]);
	}
	
	/** 
	 *
	 */
	public function __destruct()   
	{
	
	}
	
	/**
     * Singleton pattern
     * @return object
     */
    public static function instance()
    {

        if (!isset(self::$_instance)) {
			$class = __CLASS__;
			self::$_instance = new $class;
		}
		return self::$_instance;    
    }

	/** 
	 *
	 */
	public function __invoke()   
	{

		$msg = new Ashtree_Common_Message();
		$found      = FALSE;
		$login      = FALSE;
		$remembered = FALSE;
		
		if (!isset($this->success)) $this->success = $_SERVER['REQUEST_URI'];
		if (!isset($this->failure)) $this->failure = $_SERVER['HTTP_REFERER'];
		if (!isset($this->denied))  $this->denied  = ASH_ROOTNAME . '403/';
		
		$_SESSION[self::$_session]['gotopath'] = $this->success;
		
		if ($this->security > 0)
		{
		    //IF a login form post has been initiated
			if (!empty($_POST['login']))
			{
			    
				//THEN log out any existing users
				unset($_SESSION[self::$_session]['userinfo']);

				//IF post userinfo same as in the database
				if ($userinfo = $this->getDatabaseInfo($this->getFormInfo($_POST['login'])))
				{
				   if (!$this->multiple)
				   { 
    				    //THEN This block makes sure only one login is allowed at a time
    					if ($_POST['login']['multiple'])
    					{
    					    $msg->message = 'WARNING:: You have been logged out of another computer. Remember to change your password if you suspect someone has unauthorised access.';
    					}
    				    //ELSE IF accessed less than 20 minutes ago, check session
    					else if (((time() - strtotime($userinfo['accessed']))/60 < 20) && ($userinfo['session']) && ($userinfo['session'] != session_id()))
    					{
    					    $msg->message = 'FAILURE:: You are currently logged in on another computer. Login again and change your password if you suspect someone has unauthorised access.';
    					    $_SESSION[self::$_session]['userinfo'] = $userinfo;
    					    $_SESSION[self::$_session]['gotopath'] = $_SERVER['REQUEST_URI'];
    					    redirect("{$this->failure}?access=MULTIPLE");
    					}
    					//END multiple login check
				   }
					
				    //THEN save the userinfo to the session
				    $userinfo['session'] = session_id();
					$this->setUserinfo($userinfo);
					$found = TRUE;
					$login = TRUE;
				}
				else
				{
				    $msg->message = 'FAILURE:: Login failed. Please check your username and/or password and try again.';
					redirect("{$this->failure}?access=FAILURE");
				}
			}
			//ELSE IF no postdata, check to see if any session userinfo exists
			else if (isset($_SESSION[self::$_session]['userinfo']))
			{
				$found = TRUE;
			}
			//ELSE IF no sessiondata, check to see if cookie userinfo exists AND has not expired
			else if (isset($_COOKIE[self::$_session]['userinfo']))
			{
			    $userinfo = json_decode($_COOKIE[self::$_session]['userinfo'], TRUE);
			    $userinfo['session'] = session_id();
			    $this->setUserinfo($userinfo);
			    
			    $found      = TRUE;
				$login      = TRUE;
				$remembered = TRUE;
			}
			//ELSE redirect to $this->failure || back()?access=FAILURE
			else 
			{
				$msg->message = 'WARNING:: You need to log in to access this page.';
			    echo $_SESSION[self::$_session]['gotopath'] = $_SERVER['REQUEST_URI'];
			    redirect("{$this->failure}?access=DENIED");
			}

			//IF user security is smaller than the security level of the page
			if ($this->userinfo['security'] < $this->security)
			{
				$msg->message = 'FAILURE:: Access denied! You do not have permission to view that page.';
				$this->_debug->log("DEBUG", "{$this->userinfo['security']} < {$this->security}");
				redirect("{$this->denied}?access=DENIED");
			}
			
				
			//redirect to $this->success || self
			$qs = new Ashtree_Common_Querystring($this->success);
			if ($login || isset($qs->access))
			{
				//$msg->clear_messages();
				$modify = ($remembered) ? ' remembered and' : '';
				$msg->message = "SUCCESS:: You have been{$modify} signed in successfully";
				$this->_setUserAccess();
				unset($qs->access);
				redirect($qs);
			}
			
			if (!$this->multiple)
			{
    			//Check that that the session is still the same
    			if (isset($this->userinfo['session']) && ($this->userinfo['session'] != $this->_getUserAccess()))
    			{
    			    $msg->message = 'FAILURE:: You are currently logged in on another computer. Login again and change your password if you suspect someone has unauthorised access.';
    			    $_SESSION[self::$_session]['userinfo'] = $userinfo;
    			    $_SESSION[self::$_session]['gotopath'] = $_SERVER['REQUEST_URI'];
    			    redirect("{$this->failure}?access=MULTIPLE");
    			}
			}
		}
	}
	
	/**
	 *
	 */
	public function invoke()   
	{	
		return $this->__invoke();
	}
	
	/**
	 * Remove all security access  that user has
	 * essentially requiring that they log in again
	 * @access public static
	 * @param [String $connection]
	 */
	public static function deauth($connection)
	{
		//remove the database session
	    $sql = Ashtree_Database_Connection::instance($connection);
		$user = json_decode($_SESSION[self::$_session]['userinfo'], TRUE);
		$sql->query = "
			UPDATE snow_userinfo
			SET session = :null
			WHERE identity = :identity;
		";
		$sql->bind(':null', null);
		$sql->bind(':identity', $user['identity']);
		$sql->invoke();
		
		//remove the session data
		unset($_SESSION[self::$_session]['userinfo']);
		
		//remove the cookie data
		setcookie(self::$_session . '[userinfo]', '', time() - 3600, '/', $_SERVER['SERVER_NAME']);
		setcookie('PHPSESSID', '', time()-3600, '/');
	}
		
	/**
	 * Save the userinfo
	 * for access by another instance
	 * @access public
	 * @param Array $userdata
	 */
	public function setUserinfo($userdata)
	{
		$this->userinfo = $userdata;
		$_SESSION[self::$_session]['userinfo'] = json_encode($userdata);
		
	    //IF userinfo remember is TRUE,
		if (isset($userdata['remember']) && is($userdata['remember']))
		{
			//THEN save userinfo to COOKIE (1 month expiry)
			$month = time()+60*60*24*30;
			$test_day = time()+60*60*24;
			$test_min = time()+60;
			setcookie(self::$_session . '[userinfo]', $_SESSION[self::$_session]['userinfo'], $test_day, '/', $_SERVER['SERVER_NAME']);
		}
	}
		
	/**
	 * Retrieve an array of user information
	 * @access public
	 * @return Array
	 */
	public function getUserinfo()
	{
	    
		return $this->userinfo;
	}
	
	/**
	 * Retrieves stored userinfo in the session
	 * @return Array
	 */
	public static function userinfo()
	{
	    return (isset($_SESSION[self::$_session]['userinfo'])) ? json_decode($_SESSION[self::$_session]['userinfo'], TRUE) : FALSE;
	}
	
	/**
	 * Set or retrieve stored environment 
	 * for extended accessibility options
	 * @return String
	 */
	public static function environment($environment=NULL)
	{	    
	    if (isset($environment)) { 
	        if (($environment == '') && (isset($_SESSION[self::$_session]['environment']))) {
	            $tmp_environ = $_SESSION[self::$_session]['environment'];
	            unset($_SESSION[self::$_session]['environment']);
	            return $tmp_environ;
	        } else if ($environment != '') {
	            return $_SESSION[self::$_session]['environment'] = $environment;
	        }
	    } else {
	        return (isset($_SESSION[self::$_session]['environment'])) ? $_SESSION[self::$_session]['environment'] : FALSE;   
	    }
	}
	
	/**
	 * Retrieve and set a new stored token
	 * used as an added layer to prevent remote form posts
	 * @return String
	 */
	public static function token($token=NULL)
	{
	    if (isset($token)) {
	        if (($token == '') && (isset($_SESSION[self::$_session]['token']))) {
	            $tmp_token = $_SESSION[self::$_session]['token'];
	            unset($_SESSION[self::$_session]['token']);
	            return $tmp_token;
	        } else if ($token != '') {
	            return $_SESSION[self::$_session]['token'] = $token;  
	        }
	    } else {
	        return (isset($_SESSION[self::$_session]['token'])) ? $_SESSION[self::$_session]['token'] : $_SESSION[self::$_session]['token'] = md5(time());
	    }
	}
	
	/**
	 * Set or retrieve stored path 
	 * where user was trying to access
	 * @return String
	 */
	public static function gotopath($gotopath=NULL)
	{
	    if (isset($gotopath)) {
	        if (($gotopath == '') && (isset($_SESSION[self::$_session]['gotopath']))) {
	            $tmp_gotopath = $_SESSION[self::$_session]['gotopath'];
	            unset($_SESSION[self::$_session]['gotopath']);
	            return $tmp_gotopath;
	        } else if ($gotopath != '') {
	            return $_SESSION[self::$_session]['gotopath'] = $gotopath;  
	        }
	    } else {
	        return (isset($_SESSION[self::$_session]['gotopath'])) ? $_SESSION[self::$_session]['gotopath'] : FALSE;
	    }
	}
	
	/**
	 * Saves the current session information
	 * to the database for later comparisons
	 * @access private
	 * @param [String $connection]
	 */
	private function _setUserAccess($connection=NULL)
	{
		$access_date = date('Y-m-d H:i:s');
		$access_agent = $_SERVER['HTTP_USER_AGENT'];
		$access_ip = "{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']}";
		$access_session = session_id();
		$connection = ($connection) ? $connection : $this->connection;
		
	    $sql = Ashtree_Database_Connection::instance($connection);
		$sql->query = "
			UPDATE snow_userinfo
			SET 
				accessed = '{$access_date}',
				browser = '{$access_agent}',
				address = '{$access_ip}',
				session = '{$access_session}'
			WHERE identity = '{$this->userinfo['identity']}';
		";
		$sql->invoke();
	}
		
	/**
	 * Returns the last accessed session
	 * logged by that user
	 * @access private
	 * @param [$string $connection]
	 * @return String
	 */
	private function _getUserAccess($connection=NULL)
	{
	    $connection = ($connection) ? $connection : $this->connection;
		
	    $sql = Ashtree_Database_Connection::instance($connection);
		$sql->query = "
			SELECT session 
			FROM snow_userinfo
			WHERE identity = '{$this->userinfo['identity']}';
		";
		$sql->invoke();
		
		return ($sql->affected) ? $sql->recordset[0]['session'] : FALSE;
	}
	
	/**
	 * Retrieves userdata 
	 * from a submitted form
	 * @access public
	 * @param [Array $formdata]
	 * @param [Array $struct]
	 * @return Array
	 */
	public function getFormInfo($formdata=NULL, $struct=NULL)
	{
		//$formdata = (isset($formdata)) ? $formdata : $_POST['login'];
		$username = (isset($struct['username'])) ? $struct['username'] : 'username';
		$password = (isset($struct['password'])) ? $struct['password'] : 'password';
		$remember = (isset($struct['remember'])) ? $struct['remember'] : 'remember';
		
		$userinfo = array(
			$username=>$formdata[$username],
			$password=>md5($formdata[$password]),
			$remember=>@is($formdata[$remember])
		);
		
		return $userinfo;
	}
	
	/**
	 * Retrieves the userinfo
	 * from the stored database
	 * @access public
	 * @param Array $formdata
	 * @param [String $connection]
	 * @param [Array $struct]
	 * @return Array
	 */
	public function getDatabaseInfo($formdata, $connection=NULL, $struct=NULL)
	{
		$connection = ($connection) ? $connection : $this->connection;
		$username = (isset($struct['username'])) ? $struct['username'] : 'username';
		$password = (isset($struct['password'])) ? $struct['password'] : 'password';
		$remember = (isset($struct['remember'])) ? $struct['remember'] : 'remember';
		$security = (isset($struct['security'])) ? $struct['security'] : 'security';
			
		$sql = Ashtree_Database_Connection::instance($connection);
		
		//$sql->sanitize($formdata);
		$sql->query = "
				SELECT 
					info.identity,
					info.username,
					info.password,
					info.security,
					info.accessed,
					info.session,
					:remember AS remember,
					data.firstname AS dispname
				FROM snow_userinfo info
					LEFT JOIN {$sql->prefix}people data ON info.identity = data.identity
				WHERE info.verified IS TRUE
				AND info.username = :username
				AND info.password = :password;
		";
		$sql->bind(':remember', $formdata['remember']);
		$sql->bind(':username', $formdata['username']);
		$sql->bind(':password', $formdata['password']);
		$sql->invoke();
			
		return ($sql->affected) ? $sql->getFirstRow() : FALSE;
	} 
	
}