<?php
/**
 * 
 */
class Ashtree_Common_Debug
{
    /**
	 * @access private
	 * @var Object $_instance
	 */
	private static $_instance;
	
	/**
	 * @access private
	 * @var String $_log
	 */
	private static $_log = array();
	
	/**
	 * @access private
	 * @var String $_seq
	 */
	private static $_seq = 0;
	
	/**
	 * @access private
	 * @var String $_log
	 */
	private static $_syslog = array();
	
	
	/**
	 * @access public
	 * @var String $server
	 */
	private static $_server;
	public $server;
	
	/**
	 * @access public
	 * @var String $server
	 */
	private static $_page;
	public $page;
	
	/**
	 * @acccess private
	 * @var $_debugbar
	 */
	private static $_debugbar;
	
	
	/**
	 * @param
	 */
    public function __construct()
    {
        // Initialise some of the variables
        self::$_server   = $this->server   = $_SERVER['HTTP_HOST'];
        self::$_page     = $this->page     = BASENAME($_SERVER['PHP_SELF']);
  
        self::$_debugbar = (defined('ASH_DEBUG_BAR')) ? ((defined('ASH_DEBUG') && is(ASH_DEBUG)) ? is(ASH_DEBUG_BAR) : FALSE) : FALSE;
		
		// A useful tool for quickly destroying a session
		if (isset($_GET['$session']) && ($_GET['$session'] == 'destroy')) 
		{
			$qs = new Ashtree_Common_Querystring($_SERVER['REQUEST_URI']);
			$qs->param('$session', '');
			session_destroy();
			redirect($qs);
		}
    }
    
    /**
	 * @param
	 */
    public function __destruct()
    {
        // Check if user has altered the status of the debugbar
        if (isset($_GET['$debugbar']))
        {
            self::$_debugbar = ($_GET['$debugbar'] != '') ? is($_GET['$debugbar']) : TRUE;
        } 
       
        // Print the debugbar
        
        if (self::$_debugbar) $this->_print_debugbar();
    }
    
    
    /**
     * Singleton pattern
     * @return object
     */
    public static function instance()
    {

        if (!isset(self::$_instance)) 
		{
		    
			$class = __CLASS__;
			self::$_instance = new $class;
		}
		return self::$_instance;    
    }
    
    /**
     * Outputs a whole lot of debug information
     */
    private function _print_debugbar()
    {
        echo '<div id="debugbar" style="clear:both">';
        echo '<hr />';
        
        foreach(self::$_log['html'] as $row)
        {
            echo $row;   
        }

        if (!empty($_GET))
        {
            echo dump('<hr />$_GET');
            echo dump($_GET, 1);
        }
        
        if (!empty($_POST))
        {
            echo dump('<hr />$_POST');
            echo dump($_POST, 1);
        }
        
        if (!empty($_FILES))
        {
        	echo dump('<hr />$_FILES');
        	echo dump($_FILES, 1);
        }
        
        if (!empty($_SESSION))
        {
            echo dump('<hr />$_SESSION');
            echo dump($_SESSION, 1);
        }
        
        if (!empty($_COOKIE))
        {
            echo dump('<hr />$_COOKIE');
            echo dump($_COOKIE, 1);
        }
        
        echo '</div>';
    }
    
    /**
     * Programatically set the debugbar on and off
     * @param Boolean $status
     */
    public static function debugbar($status=NULL)
    {
        if (isset($status)) self::$_debugbar = is($status);
        else return self::$_debugbar;
    }
    
    /**
     * Outputs array and object data to the screen
     */
    public static function dump($data, $pre=FALSE)
    {
        if (!defined('ASH_DEBUG') || !is(ASH_DEBUG) || (ASH_SITE_MODE != 'development')) return FALSE;
        
        $output = print_r($data, TRUE);
        if ($pre) {
            return Ashtree_Html_Tag::wrap($output, 'pre');
        } else {
            return $output .= '<br />';
        }
        
    }
    
     /**
     * Logs a a message
     * stored to memory
     * where it can be
     * @param String $message
     * @param [String $function]
     * @param [String $page]
     * @param [String $server]
     */
    public static function log($severity, $message, $function=NULL, $page=NULL, $server=NULL)
    {
        $history = debug_backtrace();
        
        $cl = (isset($history[1]['class'])) ? "{$history[1]['class']}::" : FALSE;
        $fn = (isset($function)) ? $function : $cl . $history[1]['function'];
        $sv = ((isset($server)) ? $server : self::$_server) . '::';
        $pg = $sv . ((isset($page)) ? $page : self::$_page);
        $tm = date('Y-m-d H:i:s');
        
        self::$_seq++;
        
        self::$_log['plain'][self::$_seq] = "log {$tm}> [{$severity}] ({$pg}) ({$fn}) {$message}";
        self::$_log['html'][self::$_seq]  = Ashtree_Html_Tag::wrap(self::$_log['plain'][self::$_seq], '<div class="' . strtolower($severity) . '" />');  
        #echo htmlentities(self::$_log['html'][self::$_seq]) . '<br />';
    }
    
    
    /**
     * Appends a status
     * to a message
     * such as ...[OK], ...[Failed], ...[Aborted]
     * @param String $update
     */
    public static function status($update)
    {
        $upd = '[' . strtoupper($update) . ']';
        Ashtree_Html_Tag::addClass(self::$_log['html'][self::$_seq], str_replace(array(' ', '/'), '_', strtolower($update)));
        self::$_log['html'][self::$_seq] = Ashtree_Html_Tag::prepend(
            Ashtree_Html_Tag::wrap($upd, '<div style="float:right" />'), 
            self::$_log['html'][self::$_seq]
        );
        self::$_log['plain'][self::$_seq] += $upd;
    }
    
    
	/**
     * Remove previous messages
     * up to the number counted
     * @param [int $count]
     * @param [int $offset]
     */
    public static function clear($count=1, $offset=1)
    {
        for ($i = (self::$_seq - $offset); $i > (self::$_seq - $offset - $count); $i--) {
            unset(self::$_log['html'][$i]);
            unset(self::$_log['plain'][$i]);
        }//for
    }
}