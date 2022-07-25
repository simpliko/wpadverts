<?php

class Adverts_Block_Manage {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "manage";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        wp_register_style(
            'wpadverts-blocks-editor-manage',
            ADVERTS_URL . '/assets/css/blocks-editor-list.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-list.css' )
        );

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-manage",
            ADVERTS_URL . '/assets/js/block-manage.js',
            array( 'jquery', 'wp-util' ),
            '2.0.0'
        );

        wp_localize_script(
            "wpadverts-block-manage",
            "wpadverts_block_manage",
            array(
                "ajaxurl" => admin_url( "admin-ajax.php" ),
                "delete_q" => __( "Delete this item?", "wpadverts" ),
                "delete_q_text" => __( "Are you sure you want to delete '%s'", "wpadverts" ),
                "delete_q_confirm" => __( "Delete", "wpadverts" ),
                "delete_q_cancel" => __( "Cancel", "wpadverts" ),
                "delete_s" => __( "Item deleted successfully.", "wpadverts" ),
                "delete_s_confirm" => __( "Go back to ads list", "wpadverts" ),
                "delete_e" => __( "There was an error while deleting.", "wpadverts" ),
                "delete_e_confirm" => __( "Close", "wpadverts" ),
            )
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                'editor_style' => 'wpadverts-blocks-manage',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-manage' : null
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

        if( ! wpadverts_load_assets_globally() ) {
            wp_enqueue_style( 'wpadverts-blocks' );
            wp_enqueue_script( 'wpadverts-block-manage' );

            wp_enqueue_script( 'wpadverts-block-list-and-search' );
        }
        
        include_once dirname( __FILE__ ) . '/class-manage-engine.php';

        $engine = new Adverts_Block_Manage_Engine();
        return $engine->main( $atts );
    }
}