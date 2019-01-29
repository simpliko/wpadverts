<?php
/**
 * Main Adverts Class (singleton)
 *
 * @package Adverts
 * @since 0.1
 * @access public
 */

class Adverts {

    /**
     * Adverts singleton
     *
     * @var Adverts
     */
    private static $_instance = NULL;
    
    /**
     * Adverts data container
     *
     * @var array
     */
    private $_data = array();
    
    /**
     * List of objects registering WPAdverts emails
     *
     * @var array
     */
    private $_messages = array();
    
    /**
     * Singleton, returns Adverts instance
     * 
     * @return Adverts
     */
    public static function instance() {
        if( self::$_instance === NULL ) {
            self::$_instance = new self;
        }
        
        return self::$_instance;
    }
    
    /**
     * Returns adverts saved data
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get( $key, $default = NULL ) {
        if(isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return $default;
        }
    }
    
    /**
     * Sets adverts option
     * 
     * @param string $key
     * @param mixed $data
     */
    public function set( $key, $data ) {
        $this->_data[$key] = $data;
    }
    
    public function add_messages( $key, $messages ) {
        $this->_messages[$key] = $messages;
    }
    
    public function get_messages( $key = null ) {
        if( $key === null ) {
            return $this->_messages;
        } else {
            return $this->_messages[$key];
        }
    }
}
