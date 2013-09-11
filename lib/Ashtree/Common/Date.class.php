<?php
define('DATE_SECONDS_IN_MINUTE',    60, TRUE);
define('DATE_SECONDS_IN_HOUR',      3600, TRUE);
define('DATE_SECONDS_IN_DAY',       86400, TRUE);
define('DATE_SECONDS_IN_WEEK',      604800, TRUE);
define('DATE_SECONDS_IN_MONTH',     2618784, TRUE);
define('DATE_HOURS_IN_DAY',         24, TRUE);
define('DATE_HOURS_IN_WEEK',        168, TRUE);
define('DATE_WEEK_IN_DAYS',         7, TRUE);
define('DATE_AVG_MONTH_IN_SECONDS', 2618784, TRUE);
define('DATE_AVG_DAYS_IN_YEAR',     2618784, TRUE);


/**
 * 
 */
class Ashtree_Common_Date
{
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
        
    }
    
	/**
     * 
     */
    public static function getFirstDayOfMonth($date=NULL)
    {   
        $timestamp = (isset($date)) ? ((!is_numeric($date)) ? strtotime($date) : $date) : strtotime('now');
        $year  = ($timestamp != NULL) ? date('Y', $timestamp) : date('Y');
		$month = ($timestamp != NULL) ? date('n', $timestamp) : date('n');
		
		$date_first_this_month = mktime(0, 0, 0, $month, 1, $year);   
		
		return $date_first_this_month;
    }
    
	/**
     * 
     */
    public static function getLastDayOfMonth($date=NULL)
    {
        $timestamp = (isset($date)) ? ((!is_numeric($date)) ? strtotime($date) : $date) : strtotime('now');
        $year  = ($timestamp != NULL) ? date('Y', $timestamp) : date('Y');
		$month = ($timestamp != NULL) ? date('n', $timestamp) : date('n');

		if (($month++) == 13) {
			$month  = 1; 
			$year  += 1;
		} 
		
		$date_first_next_month = mktime(0, 0, 0, $month, 1, $year);  
		$date_last_this_month  = $date_first_next_month - DATE_SECONDS_IN_DAY; 
		
		return $date_last_this_month;
    }
    
/**
     * 
     */
    public static function isLastDayOfMonth($date=NULL)
    {
        $timestamp = (isset($date)) ? ((!is_numeric($date)) ? strtotime($date) : $date) : strtotime('now');
        $tomorrow  = strtotime('+1 day', $timestamp);
        
        return (date('j', $tomorrow) == 1);
    }
}