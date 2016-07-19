<?php
/**
 * Class handles flash messages for loggedin users.
 * 
 * Flash messages are notices, warnings and errors that will be displayed once
 * and then disposed.
 * 
 * The messages are stored as user meta data.
 *
 * @author Grzegorz Winiarski
 * @package Adverts
 * @subpackage Classes
 * @since 0.1
 */

class Adverts_Flash
{
    /**
     * Adverts_Flash Instance
     *
     * @var Adverts_Flash 
     */
    protected static $_instance = null;
    
    /**
     * Flash namespace
     * 
     * This is user meta option name that will store flash messages
     *
     * @var string 
     */
    protected $_ns = null;

    /**
     * Array of Info messages
     *
     * @var array
     */
    protected $_info = array();

    /**
     * Array of Error messages
     *
     * @var array
     */
    protected $_error = array();
    
    /**
     * This variable checks if flash messages were already loaded form meta table
     *
     * @var boolean
     */
    protected $_loaded = false;

    /**
     * Singleton Returns Adverts Flash Instance
     * 
     * Flash object is used to store messages (info and warnings) which will
     * be displayed to user once.
     * 
     * @since 1.0
     * @return Adverts_Flash
     */
    public static function instance()
    {
        if(self::$_instance === null) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Constructor
     * 
     * Protected constructor because Adverts_Flash can have only one instance
     * @since 1.0
     * @return null
     */
    protected function __construct()
    {
        $this->_ns = "adverts_flash";
    }

    /**
     * Loads messages from user meta
     * 
     * @since 1.0
     * @return null
     */
    public function load() 
    {
        if($this->_loaded) {
            return;
        }
        
        $id = get_current_user_id();
        $flash = get_user_meta($id, $this->_ns, true);
        $this->_id = $id;
        
        if($flash === "") {
            $this->_new = true;
        } 
        
        if(empty($flash)) {
            $this->_info = array();
            $this->_error = array();
        } else {
            $this->_info = $flash["info"];
            $this->_error = $flash["error"];
        }
        
        $this->_loaded = true;
    }
    
    /**
     * Resets messages
     * 
     * This functions should be used once flash messages were displayed to user.
     * 
     * @since 1.0
     * @return null 
     */
    public function dispose() 
    {
        $this->_info = array();
        $this->_error = array();
    }
    
    /**
     * Saves messages in user meta
     * 
     * This function will work only if current user is loggedin.
     * 
     * @since 1.0
     * @return null
     */
    public function save() 
    {   
        
        $flash = array(
            "info" => $this->_info,
            "error" => $this->_error
        );

        $id = $this->_id;
        
        if(empty($this->_info) && empty($this->_error)) {
            delete_user_meta($id, $this->_ns);
        } elseif($this->_new) {
            add_user_meta($id, $this->_ns, $flash, true);
        } else {
            update_user_meta($id, $this->_ns, $flash);
        }
        

    }

    /**
     * Add new Info message to queue
     * 
     * @since 1.0
     * @param string $info
     */
    public function add_info($info)
    {
        $this->load();
        $this->_info[] = $info;
        $this->save();
    }

    /**
     * Returns all Info messages
     * 
     * @since 1.0
     * @return array
     */
    public function get_info()
    {
        $this->load();
        return array_unique($this->_info);
    }

    /**
     * Add new Error message to queue
     * 
     * @since 1.0
     * @param string $error
     */
    public function add_error($error)
    {
        $this->load();
        $this->_error[] = $error;
        $this->save();
    }

    /**
     * Returns all Error messages
     * 
     * @return array
     */
    public function get_error()
    {
        $this->load();
        return array_unique($this->_error);
    }

}
