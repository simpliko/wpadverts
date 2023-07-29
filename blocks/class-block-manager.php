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

    /**
     * True if custom styles were already printed in the footer
     * 
     * @var boolean
     */
    protected static $_custom_styles_printed = false;
    
    public function __construct( $path = null ) {
        $this->_path = $path;
        
        add_filter( 'block_categories_all', array( $this, "register_block_category" ) );
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
            $cname = str_replace( " ", "_", ucwords( str_replace( "-", " ", $name ) ) );

            $class = sprintf( "Adverts_Block_%s", $cname );
            
            if( ! class_exists( $class ) ) {
                continue;
            }
            
            $this->_block[$name] = new $class;
        }

    }
 
    public static function print_custom_styles() {
        if( self::$_custom_styles_printed ) {
            return;
        }

        $atts = [];

        echo '<style type="text/css">' . PHP_EOL;
        echo '.wpadverts-cpt form.wpadverts-form {' . PHP_EOL;
        wpadverts_print_grays_variables( isset( $atts["form"] ) ? $atts["form"] : "" );
        echo '}' . PHP_EOL;
        wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array() );
        wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array() );
        echo '</style>' . PHP_EOL;

        self::$_custom_styles_printed = true;
    }
}
