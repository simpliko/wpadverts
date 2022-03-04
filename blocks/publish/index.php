<?php

class Adverts_Block_Publish {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "publish";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        wp_register_style(
            'wpadverts-blocks-editor-publish',
            ADVERTS_URL . '/assets/css/blocks-editor-publish.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-publish.css' )
        );

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-publish",
            ADVERTS_URL . '/assets/js/block-publish.js',
            array( 'jquery' ),
            '2.0.0'
        );
        
        register_block_type( sprintf( "%s/%s", $package, $module ), array(
            'apiVersion' => 2,
            'editor_style' => 'wpadverts-blocks-editor-publish',
            'editor_script' => $js_handler,
            'render_callback' => array( $this, "render" ),
            'style' => 'wpadverts-blocks-common',
            'script' => 'wpadverts-block-publish',
            'attributes' => array(
                'post_type' => array(
                    'type' => 'string'
                ),
                'form_scheme' => array(
                    'type' => 'string',
                    'default' => ''
                ),
            )
        ) );

    }
    
    public function render( $atts = array() ) {

        $atts = $this->handlePayload( $atts );

        include_once dirname( __FILE__ ) . '/class-publish-engine.php';

        $engine = new Adverts_Block_Publish_Engine();
        return $engine->main( $atts );
    }

    public function handlePayload( $atts ) {
        if( adverts_request( "payload" ) == "1" ) {
            $request_body = file_get_contents('php://input');
            return json_decode( $request_body, true );
        } else {
            return $atts;
        }
    }
}