<?php

class Adverts_Block_Single_Data_Table {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "single-data-table";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        /*
        wp_register_style(
            'wpadverts-blocks-editor-single-data-table',
            ADVERTS_URL . '/assets/css/blocks-editor-single-data-table.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-single-data-table.css' )
        );
        */

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-single-data-table",
            ADVERTS_URL . '/assets/js/block-single-data-table.js',
            array( 'jquery' ),
            '2.0.4'
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                'editor_style' => 'wpadverts-blocks-editor-single-data-table',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-single-data-table' : null
            )
        );

    }
    
    public function render( $atts = array() ) {

        $atts = apply_filters( "wpadverts/block/details/atts", $atts );

        if( ! wpadverts_load_assets_globally() ) {
            wp_enqueue_style( 'wpadverts-blocks' );
            wp_enqueue_script( 'wpadverts-block-details');
        }

        $params = shortcode_atts(array(
            'name' => 'default',
            'post_type' => 'advert'
        ), $atts, 'adverts_details' );

        extract( $params );

        include_once ADVERTS_PATH . '/includes/class-block-templates.php';

        // If user is in Publish -> Preview use the Adverts_Block_Templates::get_id() instead of current page ID.
        if( Adverts_Block_Templates::get_id() !== null ) {
            $post_id = Adverts_Block_Templates::get_id();
        } else {
            $post_id = get_the_ID();
        }
        
        $post_content = get_post( $post_id )->post_content;
        $post_content = wp_kses($post_content, wp_kses_allowed_html( 'post' ) );
        $post_content = apply_filters( "adverts_the_content", $post_content );

        $data_table = apply_filters( "wpadverts/block/single-data-table", $this->get_data( $post_id ), $post_id );
        $data_table = $this->filter_data_table( $data_table, $atts );

        $cols_class = $this->get_cols($atts["columns"]);

        $template = sprintf( "%s/templates/%s.php", dirname( __FILE__ ), $atts["layout"]);
        ob_start();
        include $template;
        return ob_get_clean();
    }


    protected function _get_taxonomy_parsed( $post_id, $taxonomy ) {
        $advert_category = get_the_terms( $post_id, $taxonomy );

        if( empty( $advert_category ) ) {
            return false;
        }

        ob_start();
        foreach($advert_category as $c) {
            ?>
                <div><?php echo adverts_get_taxonomy_path( $c, " / " ) ?></div>
            <?php
        }

        return ob_get_clean();
    }

    protected function _get_location_parsed( $post_id ) {
        $location = get_post_meta( $post_id, "adverts_location", true );

        if( empty( $location ) ) {
            return false;
        }

        return apply_filters( "adverts_tpl_single_location", esc_html( $location ), $post_id, "block/single-data-table" );
    }

    public function get_data( $post_id ) {
        $post = get_post( $post_id );

        $post_content = $post->post_content;
        $post_content = wp_kses($post_content, wp_kses_allowed_html( 'post' ) );
        $post_content = apply_filters( "adverts_the_content", $post_content );

        $data_table = [
            [
                "name" => "default__ID",
                "type" => "__builtin",
                "icon" => "fas fa-hashtag",
                "label" => __("ID", "wpadverts"),
                "value" => $post_id
            ],            
            [
                "name" => "default__post_title",
                "type" => "__builtin",
                "icon" => "fas fa-heading",
                "label" => __("Title", "wpadverts"),
                "value" => $post->post_title
            ],            
            [
                "name" => "default__post_excerpt",
                "type" => "__builtin",
                "icon" => "fas fa-file-lines",
                "label" => __("Excerpt", "wpadverts"),
                "value" => $post->post_excerpt
            ],
            [
                "name" => "default__post_content",
                "type" => "adverts_field_textarea",
                "icon" => "fas fa-file-lines",
                "label" => __("Description", "wpadverts"),
                "value" => $post_content
            ], 
            [
                "name" => "date__post_date",
                "type" => "__builtin",
                "icon" => "fas fa-calendar",
                "label" => __("Published", "wpadverts"),
                "value" => $post->post_date
            ], 
            [
                "name" => "date__post_date_gmt",
                "type" => "__builtin",
                "icon" => "fas fa-calendar",
                "label" => __("Published", "wpadverts"),
                "value" => $post->post_date_gmt
            ],             
            [
                "name" => "date__post_modified",
                "type" => "__builtin",
                "icon" => "fas fa-clock",
                "label" => __("Modified", "wpadverts"),
                "value" => $post->post_modified
            ], 
            [
                "name" => "date__post_modified_gmt",
                "type" => "__builtin",
                "icon" => "fas fa-clock",
                "label" => __("Modified", "wpadverts"),
                "value" => $post->post_modified_gmt
            ],             
            [
                "name" => "meta__adverts_person",
                "type" => "__builtin",
                "icon" => "fas fa-user",
                "label" => __("Contact Person", "wpadverts"),
                "value" => get_post_meta( $post_id, "adverts_person", true )
            ],             
            [
                "name" => "meta__adverts_email",
                "type" => "__builtin",
                "icon" => "fas fa-envelope",
                "label" => __("Contact Email", "wpadverts"),
                "value" => get_post_meta( $post_id, "adverts_email", true )
            ],             
            [
                "name" => "meta__adverts_phone",
                "type" => "__builtin",
                "icon" => "fas fa-phone",
                "label" => __("Contact Phone", "wpadverts"),
                "value" => get_post_meta( $post_id, "adverts_phone", true )
            ],             
            [
                "name" => "meta__adverts_price",
                "type" => "__builtin",
                "icon" => "fas fa-dollar",
                "label" => __("Price", "wpadverts"),
                "value" => get_post_meta( $post_id, "adverts_price", true )
            ],              
            [
                "name" => "meta__adverts_location",
                "type" => "__builtin",
                "icon" => "fas fa-location-dot",
                "label" => __("Location", "wpadverts"),
                "value" => get_post_meta( $post_id, "adverts_location", true )
            ],             
            [
                "name" => "pattern__location",
                "type" => "adverts_field_text",
                "icon" => "fas fa-location-dot",
                "label" => __("Location", "wpadverts"),
                "value" => $this->_get_location_parsed( $post_id )
            ],             
            [
                "name" => "pattern__price",
                "type" => "__builtin",
                "icon" => "fas fa-dollar",
                "label" => __("Price", "wpadverts"),
                "value" => adverts_price( get_post_meta( $post_id, "adverts_price", true ) )
            ], 
            [
                "name" => "pattern__post_date",
                "type" => "__builtin",
                "icon" => "fas fa-calendar",
                "label" => __("Published", "wpadverts"),
                "value" => date_i18n( get_option( "date_format" ), $post->post_date )
            ], 
        ];
     
        $taxes = get_object_taxonomies( $post->post_type );
        foreach($taxes as $tx) {
            $txo = get_taxonomy($tx);
            if($tx == "advert_location") {
                continue;
            } elseif($tx == "advert_category") {
                $txo->label = __("Category", "wpadverts");
            }
            
            $data_table[] = apply_filters( "wpadverts/block/single-data-table/taxonomy", [
                "name" => sprintf( "taxonomy__%s", $tx),
                "type" => "adverts_field_select",
                "icon" => "fas fa-folder",
                "label" => $txo->label,
                "value" => $this->_get_taxonomy_parsed( $post_id, $txo->name )
            ], $txo, $post_id );
        }

        $data_table = apply_filters( "wpadverts/block/single-data-table", $data_table, $post_id );

        if( function_exists( "wpadverts_custom_fields_get_tpl_single" ) ) {
            $tr = wpadverts_custom_fields_get_tpl_single( $post_id, "table-row", true);
            $fw = wpadverts_custom_fields_get_tpl_single( $post_id, "full-width", true);
            $c_data = [];

            if(is_array($tr)) {
                $c_data = array_merge( $c_data, $tr );
            }

            if(is_array($fw)) {
                $c_data = array_merge( $c_data, $fw );
            }
            //echo "<pre>";print_r($c_data);echo "</pre>";

            foreach( $c_data as $data ) {
                $data_table[] = [
                    "name" => sprintf( "meta__%s", $data["field"]["name"] ),
                    "type" => $data["field"]["type"],
                    "icon" => $data["icon"],
                    "label" => $data["label"],
                    "value" => $data["value"]
                ];
            }

        }

        return $data_table;
    }

    public function filter_data_table( $data_table, $atts ) {
        $filtered = [];
        
        if( ! empty( $atts["exclude_types"] ) ) {
            $et = [];
            foreach( $atts["exclude_types"] as $exc ) {
                $et[] = $exc["name"];
            }
            
            foreach( $data_table as $dt ) {
                if( ! in_array( $dt["type"], $et ) ) {
                    $filtered[] = $dt;
                }
            }
        }

        if( ! empty( $atts["include_types"] ) ) {
            $it = [];
            foreach( $atts["include_types"] as $itc ) {
                $it[] = $itc["name"];
            }

            foreach( $data_table as $dt ) {
                if( in_array( $dt["type"], $it ) ) {
                    $filtered[] = $dt;
                }
            }
        }

        if( ! empty( $atts["include_fields"] ) ) {
            foreach( $atts["include_fields"] as $ifc ) {
                $f = $this->get_field_by_name( $ifc["name"], $data_table );
                if( is_array( $f ) ) {
                    $filtered[] = $f;
                }
            }
        }

        if( ! empty( $atts["exclude_fields"] ) ) {
            $ef = [];
            foreach( $atts["exclude_fields"] as $efc ) {
                $ef[] = $efc["name"];
            }

            foreach( $data_table as $dt ) {
                if( ! in_array( $dt["name"], $ef ) ) {
                    $filtered[] = $dt;
                }
            }
        }

        return $filtered;
    }

    public function get_field_by_name( $name, $data_table ) {
        foreach( $data_table as $dt ) {
            if( $dt["name"] == $name ) {
                return $dt;
            }
        }

        return null;
    }

    public function get_cols( $cols ) {
        $arr = [
            1 => "md:atw-grid-cols-1",
            2 => "md:atw-grid-cols-2",
            3 => "md:atw-grid-cols-3",
            4 => "md:atw-grid-cols-4",
            5 => "md:atw-grid-cols-5",
            6 => "md:atw-grid-cols-6",
        ];

        return $arr[$cols];
    }
}