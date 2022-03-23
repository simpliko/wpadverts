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
        
        add_filter( 'block_categories', array( $this, "register_block_category" ) );
    }
    
    public function register_common_scripts() {

    }

    public function register_common_styles() {
        
    }

    public function register_block_category( $categories ) {

        $block_category = array( 
            'title' => esc_html__( 'Classifieds', 'wpadverts' ), 
            'slug' => 'wpadverts' 
        );
        $category_slugs = wp_list_pluck( $categories, 'slug' );
     
        if ( ! in_array( $block_category['slug'], $category_slugs, true ) ) {
            $categories = array_merge(
                $categories,
                array(
                    array(
                        'title' => $block_category['title'], // Required
                        'slug'  => $block_category['slug'], // Required
                        'icon'  => 'wordpress', // Slug of a WordPress Dashicon or custom SVG
                    ),
                )
            );
        }
     
        return $categories;
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
