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
                'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                'script' => null
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
        }
        
        extract(shortcode_atts(array(
            'name' => 'default',
            'show' => 'top',
            'item_display' => "wpa-item-stacked",
            'columns' => 4,
            "columns_mobile" => 2,
            'default_icon' => 'fas fa-folder',
            'icon_size' => 9,
            'show_count' => true,
            'sub_count' => 5,
            'color_icon' => '',
            'color_text' => '',
            'color_bg' => '',
            'color_border' => '',
            'margin' => 2,
            'show_icons' => true,
            'item_padding' => 3
        ), $atts));
        
        $columns = (int)$columns;
        
        if($show != 'top') {
            $show = 'all';
        }
        
        $class_cols = sprintf( "%s %s", $this->get_cols_desktop( $columns ), $this->get_cols_mobile( $columns_mobile ) );
        $class_icon_size = $this->get_icon_size( $icon_size );

        $class_margin = $this->get_margin( $margin );
        $class_margin_neg = $this->get_margin_neg( $margin );

        $class_item_padding = $this->get_item_padding( $item_padding );

        if( $item_display == "wpa-item-stacked" ) {
            $class_icon_padding = $this->get_icon_padding( $item_padding );
        } else {
            $class_icon_padding = "";
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

    public function get_cols_desktop( $cols ) {
        $cols_array = array(
            1 => "md:atw-grid-cols-1",
            2 => "md:atw-grid-cols-2",
            3 => "md:atw-grid-cols-3",
            4 => "md:atw-grid-cols-4",
            5 => "md:atw-grid-cols-5",
            6 => "md:atw-grid-cols-6",
            7 => "md:atw-grid-cols-7",
            8 => "md:atw-grid-cols-8",
            9 => "md:atw-grid-cols-9",
            10=> "md:atw-grid-cols-10",
        );
        if( isset( $cols_array[ $cols ] ) ) {
            return $cols_array[$cols];
        } else {
            return $cols_array[4];
        }
    }

    public function get_cols_mobile( $cols ) {
        $cols_array = array(
            1 => "atw-grid-cols-1",
            2 => "atw-grid-cols-2",
            3 => "atw-grid-cols-3",
            4 => "atw-grid-cols-4",
        );
        if( isset( $cols_array[ $cols ] ) ) {
            return $cols_array[$cols];
        } else {
            return $cols_array[4];
        }
    }

    public function get_icon_size( $icon_size ) {
        $icon_sizes = array(
            0 => "atw-text-xs",
            1 => "atw-text-sm",
            2 => "atw-text-base",
            3 => "atw-text-lg",
            4 => "atw-text-xl",
            5 => "atw-text-2xl",
            6 => "atw-text-3xl",
            7 => "atw-text-4xl",
            8 => "atw-text-5xl",
            9 => "atw-text-6xl",
            10 => "atw-text-7xl",
            11 => "atw-text-8xl",
            12 => "atw-text-9xl",
        );
        if( isset( $icon_sizes[$icon_size] ) ) {
            return $icon_sizes[$icon_size];
        } else {
            return $icon_sizes[6];
        }
    }

    public function get_margin( $margin ) {
        $margins = array(
            0 => "atw-m-0",
            1 => "atw-m-1",
            2 => "atw-m-2",
            3 => "atw-m-3",
            4 => "atw-m-4",
            5 => "atw-m-5",
            6 => "atw-m-6",
        );
        if( isset( $margins[ $margin ] ) ) {
            return $margins[$margin];
        } else {
            return $margins[2];
        }
    }

    public function get_margin_neg( $margin ) {
        $margins = array(
            0 => "atw-m-0",
            1 => "atw--m-1",
            2 => "atw--m-2",
            3 => "atw--m-3",
            4 => "atw--m-4",
            5 => "atw--m-5",
            6 => "atw--m-6",
        );
        if( isset( $margins[ $margin ] ) ) {
            return $margins[$margin];
        } else {
            return $margins[2];
        }
    }

    public function get_item_padding( $padding ) {
        $paddings = array(
            0 => "atw-py-0",
            1 => "atw-py-1",
            2 => "atw-py-2",
            3 => "atw-py-3",
            4 => "atw-py-4",
            5 => "atw-py-5",
            6 => "atw-py-6",
        );
        if( isset( $paddings[ $padding ] ) ) {
            return $paddings[$padding];
        } else {
            return $paddings[3];
        }
    }

    public function get_icon_padding( $padding ) {
        $paddings = array(
            0 => "atw-pt-0",
            1 => "atw-pt-1",
            2 => "atw-pt-2",
            3 => "atw-pt-3",
            4 => "atw-pt-4",
            5 => "atw-pt-5",
            6 => "atw-pt-6",
        );
        if( isset( $paddings[ $padding ] ) ) {
            return $paddings[$padding];
        } else {
            return $paddings[3];
        }
    }

    public function get_block_icon_size( $icon_size ) {
        $icon_sizes = array(
            0 => "atw-h-3",
            1 => "atw-h-3.5",
            2 => "atw-h-4",
            3 => "atw-h-5",
            4 => "atw-h-5",
            5 => "atw-h-6",
            6 => "atw-h-7",
            7 => "atw-h-9",
            8 => "atw-h-12",
            9 => "atw-h-14",
            10 => "atw-h-16",
            11 => "atw-h-24",
            12 => "atw-h-32",
        );
        if( isset( $icon_sizes[$icon_size] ) ) {
            return $icon_sizes[$icon_size];
        } else {
            return $icon_sizes[6];
        }
    }
}