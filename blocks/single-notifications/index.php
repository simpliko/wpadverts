<?php

class Adverts_Block_Single_Notifications {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "single-notifications";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        /*
        wp_register_style(
            'wpadverts-blocks-editor-single-gallery',
            ADVERTS_URL . '/assets/css/blocks-editor-single-gallery.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-single-gallery.css' )
        );
*/

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-single-notifications",
            ADVERTS_URL . '/assets/js/block-single-notifications.js',
            array( 'jquery' ),
            '2.0.4'
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                'editor_style' => 'wpadverts-blocks-editor-single-notifications',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-single-notifications' : null
            )
        );

    }
    
    public function render( $atts = array() ) {
        ob_start();
        do_action( "wpadverts/block/single-notifications", get_the_ID(), $atts );
        return ob_get_clean();
    }

}