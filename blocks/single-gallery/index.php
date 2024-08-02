<?php

class Adverts_Block_Single_Gallery {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "single-gallery";
        
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
            "wpadverts-block-single-gallery",
            ADVERTS_URL . '/assets/js/block-single-gallery.js',
            array( 'jquery' ),
            '2.1.5'
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                'editor_style' => 'wpadverts-blocks-editor-single-gallery',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-single-gallery' : null
            )
        );

    }
    
    protected function _get_object_fit($text) {
        $options = array(
            "contain" => "atw-object-contain",
            "cover" => "atw-object-cover",
            "fill" => "atw-object-fill",
            "none" => "atw-object-none",
            "scale-down" => "atw-object-scale-down"
        );

        return $options[$text];
    }

    protected function _get_object_position($text) {
        $options = array(
            "bottom-right" => "atw-bottom-0 atw-right-0",
            "top-right" => "atw-top-0 atw-right-0",
            "top-left" => "atw-top-0 atw-left-0",
            "bottom-left" => "atw-bottom-0 atw-left-0"
        );

        return $options[$text];
    }

    public function render( $atts = array() ) {

        include_once ADVERTS_PATH . "/includes/class-gallery-helper.php";
        include_once ADVERTS_PATH . '/includes/class-block-templates.php';

        // If user is in Publish -> Preview use the Adverts_Block_Templates::get_id() instead of current page ID.
        if( Adverts_Block_Templates::get_id() !== null ) {
            $post_id = Adverts_Block_Templates::get_id();
        } else {
            $post_id = get_the_ID();
        }

        if( defined( "WPADVERTS_BLOCK_GALLERY_USE_OLD" ) ) {
            ob_start();
            $gallery_helper = new Adverts_Gallery_Helper( $post_id );
            $gallery_helper->render_gallery();
            return ob_get_clean();
        }

        $atts = apply_filters( "wpadverts/block/gallery/atts", $atts );

        $gallery_img_height = $atts["gallery_img_height"];
        $gallery_img_size = $atts["gallery_img_size"];
        $gallery_fit = $this->_get_object_fit($atts["gallery_fit"]);
        $gallery_bg = $atts["gallery_bg"];
        
        $slider_is_lazy = $atts["slider_is_lazy"];

        $thumb_show = $atts["thumb_show"];
        $thumb_width = $atts["thumb_width"];
        $thumb_height = $atts["thumb_height"];
        $thumb_img_size = $atts["thumb_img_size"];
        $thumb_fit = $this->_get_object_fit($atts["thumb_fit"]);
        $thumb_bg = $atts["thumb_bg"];

        $nav_show = $atts["nav_show"];
        $nav_position = $this->_get_object_position($atts["nav_position"]);

        $custom_controls = false;

        if(!$nav_show) {
            $nav_position = "atw-hidden";
        }


        $gallery_helper = new Adverts_Gallery_Helper( $post_id );
        $images = $gallery_helper->load_attachments();
        $template = dirname( __FILE__ ) . "/templates/gallery.php";

        wp_enqueue_script("wpadverts-block-single-gallery");

        wp_enqueue_style( 'wpadverts-tiny-slider');
        wp_enqueue_script( 'wpadverts-tiny-slider' );

        wp_enqueue_script( 'wpadverts-glightbox' );
        wp_enqueue_style( 'wpadverts-glightbox' );

        ob_start();
        include $template;
        $content = ob_get_clean();

        return $content;

        // If user is in Publish -> Preview use the Adverts_Block_Templates::get_id() instead of current page ID.
        if( Adverts_Block_Templates::get_id() !== null ) {
            $post_id = Adverts_Block_Templates::get_id();
        } else {
            $post_id = get_the_ID();
        }

        ob_start();
        $gallery_helper = new Adverts_Gallery_Helper( $post_id );
        $gallery_helper->render_gallery();

        return ob_get_clean();
    }

}