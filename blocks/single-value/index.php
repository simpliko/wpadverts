<?php

class Adverts_Block_Single_Value {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "single-value";
        
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
            "wpadverts-block-single-value",
            ADVERTS_URL . '/assets/js/block-single-value.js',
            array( 'jquery' ),
            '2.0.4'
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                'editor_style' => 'wpadverts-blocks-editor-single-value',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-single-value' : null
            )
        );

    }
    
    public function render( $atts = array() ) {

        // If user is in Publish -> Preview use the Adverts_Block_Templates::get_id() instead of current page ID.
        
        include_once ADVERTS_PATH . '/includes/class-block-templates.php';
        
        if( Adverts_Block_Templates::get_id() !== null ) {
            $post_id = Adverts_Block_Templates::get_id();
        } else {
            $post_id = get_the_ID();
        }
        
        if( ! isset( $atts["data"][0] ) ) {
            return __("no data selected!", "wpadverts");
        }

        $value = wpadverts_get_object_value( $post_id, $atts["data"][0] );
        $value = apply_filters( "wpadverts/block/single-value/value", $value, $atts, $post_id );

        if( empty( $value ) && empty( $data["empty_value"] ) ) {
            return "";
        }

        if( $atts["render_as"] == "text" ) {
            return $value;
        }

        if( ! wpadverts_load_assets_globally() ) {
            wp_enqueue_style( 'wpadverts-blocks' );
            wp_enqueue_script( 'wpadverts-block-details');
        }

        $classes = apply_filters( "wpadverts/block/single-value/classes", [
            $atts["className"] ?? "",
            $atts["type"],
            $atts["text_size"],
            $atts["font_weight"],
            $atts["margin_x"],
            $atts["margin_y"],
            $atts["padding_x"],
            $atts["padding_y"],
            $atts["border_radius"]
        ], $atts, $post_id );
        $params = "";
        $style = "";

        if($atts["color"]) {
            $style .= sprintf("color:%s;", $atts["color"]);
        }
        if($atts["color_bg"]) {
            $style .= sprintf("background-color:%s;", $atts["color_bg"]);
        }

        if(!empty($style)) {
            $params .= sprintf(' style="%s"', $style );
        }

        $html = apply_filters( "wpadverts/block/single-value/html", '<span class="%s"%s>%s</span>', $atts, $post_id );

        return sprintf( $html, join( " ", $classes ), $params, $value );


    }

}