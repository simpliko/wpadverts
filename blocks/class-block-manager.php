<?php

class Adverts_Block_Manager {
    
    
    /**
     * Path to folder with blocks
     *
     * @var string
     */
    protected $_path = null;
    
    /**
     * List of registered blocks
     *
     * @var object[]
     */
    protected $_block = array();
    
    public function __construct( $path = null ) {
        $this->_path = $path;
        

    }
    
    public function register() {
        $path = $this->_path;
        if( $path === null ) {
            $path = dirname( __FILE__ );
        }
        
        $blocks = glob( rtrim( $path, "/" ) . "/*/" );

        if( ! is_array( $blocks ) ) {
            return;
        }
        
        foreach( $blocks as $block ) {
            
            $loader = $block . "index.php";
            
            if( !file_exists( $loader ) ) {
                continue;
            }
            
            include_once $loader;
            
            $name = basename( $block );
            $class = sprintf( "Adverts_Block_%s", ucfirst( $name ) );
            
            if( ! class_exists( $class ) ) {
                continue;
            }
            
            $this->_block[$name] = new $class;
        }

    }
    
}
