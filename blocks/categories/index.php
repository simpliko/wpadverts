<?php

class Adverts_Block_Categories {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "categories";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        wp_register_style(
            'wpadverts-blocks-editor-categories',
            ADVERTS_URL . '/assets/css/blocks-editor-categoirs.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-categories.css' )
        );

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-categories",
            ADVERTS_URL . '/assets/js/block-categories.js',
            array( 'jquery' ),
            '2.0.0'
        );

        wp_localize_script(
            "wpadverts-block-categories",
            "wpadverts_block_categories",
            array(
                "ajaxurl" => admin_url( "admin-ajax.php" ),
            )
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                'editor_style' => 'wpadverts-blocks-categories',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                'style' => 'wpadverts-blocks-common',
                'script' => 'wpadverts-block-categories'
            )
        );
    }

    public function handlePayload( $atts ) {
        if( adverts_request( "payload" ) == "1" ) {
            $request_body = file_get_contents('php://input');
            return json_decode( $request_body, true );
        } else {
            return $atts;
        }
    }

    public function render( $atts = array() ) {

        $atts = $this->handlePayload( $atts );

        extract(shortcode_atts(array(
            'name' => 'default',
            'show' => 'top',
            'columns' => 4,
            'default_icon' => 'adverts-icon-folder',
            'show_count' => true,
            'sub_count' => 5
        ), $atts));
        
        $columns = (int)$columns;
        
        if($show != 'top') {
            $show = 'all';
        }
        
        $terms = get_terms( apply_filters( 'adverts_categories_query', array( 
            'taxonomy' => 'advert_category',
            'hide_empty' => 0, 
            'parent' => null, 
        ), $atts ) );
        
        ob_start();
        include_once dirname( __FILE__ ) . '/templates/categories-all.php';

        return ob_get_clean();
    }
}