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
     * Returns all saved data
     * 
     * @since   1.5.0
     * @return  array
     */
    public function get_all() {
        return $this->_data;
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
}
