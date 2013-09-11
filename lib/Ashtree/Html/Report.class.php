<?php

/**
 * 
 */
class Ashtree_Html_Report 
{
    public $title;
    public $description;
    
    private $_report = "";
    private $_head   = "";
    private $_body   = "";
    private $_foot   = "";
    private $_row    = "";
    
    private $_count  = 0;
    private $_color  = 0;
    
    private $_orientation;
    
    /**
     * 
     */
    public function __construct()
    {
        // Adjust orientation to default
        // with headers at the top
        $this->setHorizontal();    
    }
    
	/**
     * 
     */
    public function __invoke()
    {
        return $this->build();
    }
    
	/**
     * 
     */
    private function _report_horizontal()
    {
       
        if ($this->title != '')       $this->_report  = "<h2>{$this->title}</h2>";
	    if ($this->description != '') $this->_report .= "<p>{$this->description}</p>";
	    
        $this->_report .= "<table width=\"100%\" cellpadding=\"5\" style=\"border:1px solid #CCC\">";
        
        $this->_report .= "	<thead>";
        $this->_report .= $this->head();
        $this->_report .= "	</thead>";
        
        $this->_report .= "	<tbody>";
        $this->_report .= $this->body();
        $this->_report .= "	</tbody>";
        
        $this->_report .= "	<tfoot>";
        $this->_report .= $this->foot();
        $this->_report .= "	</tfoot>";
        
        $this->_report .= "</table>";
        
        return $this->_report; 
    }
    
/**
     * 
     */
    private function _report_vertical()
    {
       
        if ($this->title != '')       $this->_report  = "<h2>{$this->title}</h2>";
	    if ($this->description != '') $this->_report .= "<p>{$this->description}</p>";
	    
        $this->_report .= "<table width=\"100%\" cellpadding=\"5\" style=\"border:1px solid #CCC\">";
        
        $this->_report .= $this->body();
        
        $this->_report .= "</table>";
        
        return $this->_report; 
    }
    
    /**
     * 
     */
    public function build()
    {
        $build_orientation = "_report_{$this->_orientation}";
        return $this->$build_orientation();
    }
    
    /**
     * 
     */
    public static function a($url, $name=NULL, $target=NULL)
    {
        if (!$name) $name = $url;
        $t = (isset($target)) ? " target=\"{$target}\"" : "";
        return "<a href=\"{$url}\"{$t}>{$name}</a>";
    }
    
    /**
     * 
     */
    function setVertical()
    {
        $this->_orientation = 'vertical';
    }
    
	/**
     * 
     */
    function setHorizontal()
    {
        $this->_orientation = 'horizontal';
    }
    
    /**
     * 
     */
    private function _head_horizontal($headers)
    {
        if (isset($headers)) {
            $this->_head = "<tr>";
            foreach($headers as $th)
            {
                $this->_head .= "	<th style=\"background:#CCC\">{$th}</th>";
            }//foreach
            $this->_head .= "</tr>";
        }
        
        return $this->_head;
    }
    
    /**
     * 
     */
    private function _head_vertical($headers)
    {
        $this->_head = $headers;
    }
    
    /**
     * 
     */
    public function _body_horizontal($content)
    {
        if (isset($content)) {
            $this->_count++;
            $class = ($this->_color = !$this->_color) ? "odd" : "even";
            $style = ($class == 'even') ? 'background-color:#EEE;' : '';
            $this->_body .= "<tr class=\"{$class}\">";
            foreach($content as $td)
            {
                $this->_body .= "	<td style=\"{$style}\">{$td}</td>";
            }//foreach
            $this->_body .= "</tr>";
        }
        
        return $this->_body;
    }
    
    /**
     * 
     */
    public function _body_vertical($content)
    {
        if (isset($content)) {
            $class = ($this->_color = !$this->_color) ? "odd" : "even";
            $style = ($class == 'odd') ? 'background-color:#EEE;' : '';
            $this->_row .= "<tr class=\"{$class}\">";
            
            if (isset($this->_head[$this->_count])) $this->_row .= "	<th style=\"background:#CCC\">{$this->_head[$this->_count]}</th>";
            foreach($content as $td)
            {
                $this->_row .= "<td style=\"{$style}\">{$td}</td>";
            }
            if (isset($this->_foot[$this->_count])) $this->_row .= "<th>{$this->_foot[$this->_count]}</th>";
            $this->_row .= "</tr>";
            $this->_count++;
        }
        
        return $this->_row;
    }
    
    
    /**
     * 
     */
    private function _foot_horizontal($footers)
    {
        if (isset($footers)) {
            
            $this->_foot .= "<tr>";
            foreach($footers as $th)
            {
                $this->_foot .= "	<th>{$th}</th>";
            }//foreach
            $this->_foot .= "</tr>";
        }
        
        return $this->_foot;
    }
    
    /**
     * 
     */
    private function _foot_vertical($footers)
    {
        $this->_foot = $footers;
    }
    
    /**
     * 
     */
    public function head($headers=NULL)
    {
        $head_orientation = "_head_{$this->_orientation}";
        return $this->$head_orientation($headers);
    }
    
	/**
     * 
     */
    public function body($content=NULL)
    {
        $body_orientation = "_body_{$this->_orientation}";
        return $this->$body_orientation((array)$content);
    }
    
    /**
     * 
     */
    public function foot($footers=NULL)
    {
        $foot_orientation = "_foot_{$this->_orientation}";
        return $this->$foot_orientation($footers);
    }
    
    /**
     * 
     */
    public function mail($subject, $to)
    {
        $mail = new Ashtree_Common_Sendmail();
        $mail->to = $to;
        $mail->subject = $subject;
        $mail->message = $this->_report;
        $mail->invoke();
    }
}
