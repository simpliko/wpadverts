<?php
/**
 * Simple way to generate HTML tags using OOP syntax
 *
 * @author Grzegorz Winiarski
 * @package Adverts
 * @subpackage Classes
 * @since 0.1
 */
class Adverts_Html {

    /**
     * Tag name
     *
     * @var string
     */
    protected $_tag = "";
    
    /**
     * List of tag attributes (key => value)
     *
     * @var array
     */
    protected $_attr = array();
    
    /**
     * HTML to insert inside tag
     *
     * @var string
     */
    protected $_html = null;
    
    /**
     * Force tag long closing 
     * 
     * For example force <tag></tag> instead of <tag />
     *
     * @var boolean
     */
    protected $_force = false;
    
    /**
     * Constructor
     * 
     * @param string $tag Tag name
     * @param array $attr List of attributes (key => value)
     * @param string $html HTML to insert inside the tag
     * @since 0.1
     * @return void 
     */
    public function __construct($tag, $attr = array(), $html = null)
    {
        $this->_tag = $tag;
        $this->_attr = $attr;
        $this->_html = $html;
    }
    
    /**
     * Force tag long closing
     * 
     * For example force <tag></tag> instead of <tag />
     * 
     * @param boolean $force
     * @since 0.1
     * @return void
     */
    public function forceLongClosing($force = true)
    {
        $this->_force = $force;
    }
    
    /**
     * Return HTML code based on __construct() params
     * 
     * @since 0.1
     * @return string
     */
    public function render()
    {
        $output = "<".$this->_tag." ";
        
        foreach($this->_attr as $k => $v) {
            if(!empty($v) || is_string($v) && strlen($v)>0) {
                $output .= esc_attr($k).'="'.esc_attr($v).'" ';
            }
        }
        
        if(empty($this->_html) && !$this->_force) {
            $output .= " />";
        } else {
            $output .= ">";
            $output .= $this->_html;
            $output .= "</".$this->_tag.">";
        }
        
        return $output;
    }
    
    /**
     * Magic to string, converts object to string
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
    
    /**
     * Static function allows to quickly generarte tag
     * 
     * @param type $tag
     * @param type $attr
     * @param type $html
     * @return type
     */
    public static function build($tag, $attr = array(), $html = null) 
    {
        $helper = new self($tag, $attr, $html);
        return $helper->render();
    }
}

?>
