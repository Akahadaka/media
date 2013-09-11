<?php
/**
 * Detect CMS variables
 * such as @content_block
 * and replace them with matching content
 * from the database
 * 
 * @author andrew.nash
 * @version 2.0
 */

class Ashtree_Common_Cms
{
    /**
     * @access private
     * @var Array $_params
     */
    private $_params = array();
    
    /**
     * @access private
     * @var Object $_debug
     */
    private $_debug = array();
    
    /**
     * @access private
     * @var Array $_variables
     */
    private $_variables = NULL;
    
    /**
     * @access public
     * @var String $connection
     */
    public $connection = NULL;
    
    public function __construct($connection=NULL)
    {
        $this->_debug = Ashtree_Common_Debug::instance();
        
        if ($connection) $this->connection = $connection;
    }
    
    public function get_template_vars()
    {
        $vars = array();
        foreach ((array)Ashtree_Common::readdir('/bin/templates', '.tpl.php') as $filename)
        {
            $content = file_get_contents(Ashtree_Common::get_real_path("/bin/templates/{$filename}", TRUE));
            preg_match_all('/{@(.*?)}/i', $content, $matches);
            //echo dump($matches, 1);
            $vars[str_replace('.tpl.php', '', $filename)] = $matches[1];
        }//foreach
        
        return $vars;
    }
    
    /**
     * Save contents to a database
     * by the template::variable combination
     * @param String $contents
     * @param String $template
     * @param String $variable
     */
    public function set_content($contents, $template, $variable)
    {
        $cpk = crc32("{$template}::{$variable}");
        $upd = date('Y-m-d H:i:s');
        
        $sql = Ashtree_Common_Connection::instance($this->connection);
        $sql->query = "
        	INSERT INTO ash_cmsldata (
        		identity,
        		contents,
        		template,
        		variable
        	) VALUES (
        		'{$cpk}',
        		'{$contents}',
        		'{$template}',
        		'{$variable}'
        	) 
        	ON DUPLICATE KEY UPDATE
        	contents = VALUE(contents),
        	template = VALUE(template),
        	variable = VALUE(variable),
        	updated  = '{$upd}'
        ";
        $sql->invoke();
        return $sql->affected;
    }
    
	/**
     * Fetches the matching value
     * of the template::variable combination
     * @param String $template
     * @param [String $variable]
     * @return Mixed
     */
    public function get_content($template, $variable=NULL)
    {
        $template = str_replace(ASH_ROOTPATH, '', $template);
        
        if (!is_array($this->_variables))
        {
            $this->_variables = $this->_get_db_content($template, $variable);
        }

        if (!empty($this->_variables))
        {
            if (isset($variable)) {
                return $this->_variables[$template][$variable];   
            } else {
                return $this->_variables[$template];   
            }
        }

    }
    
    /**
     * Fetches rows of content from the database
     * based on the template::variable combination
     * @param String $template
     * @param String $variable
     */
    private function _get_db_content($template, $variable)
    {
        if (isset($variable))
        {
            $cpk   = crc32("{$template}::{$variable}");
            $where = "WHERE identity = '{$cpk}'";
        }
        else
        {
            $where = "WHERE template = '{$template}'";
        }
        
        $sql = Ashtree_Common_Connection::instance($this->connection);
        $sql->query = "
        	SELECT 
        		template,
        		variable,
        		contents 
        	FROM ash_cmsldata
        	{$where}
        ";
        
        $sql->invoke();
        
        // Rearrange the array 
        // for easy access to data
        $row = array();
        if ($sql->affected) {
            foreach($sql->recordset as $val) {
                $row[$val['template']][$val['variable']] = $val['contents'];
            }//foreach
        }
        
        return $row;
    }
}