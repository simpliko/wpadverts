<?php

class Adverts_Types_Admin {
    
    public function render() {
        
        if( adverts_request( "edit-post" ) ) {
            $this->render_edit_post();
        } else if( adverts_request( "edit-taxonomy" ) ) {
            $this->render_edit_taxonomy();
        } else if( adverts_request( "restore-post-type" ) ) {
            $this->restore_post_type();
        } else if( adverts_request( "restore-taxonomy" ) ) {
            $this->restore_taxonomy();
        } else if( adverts_request( "enable-auto-comments" ) ) {
            $this->enable_auto_comments();
        } else {
            $this->render_list();
        }
    }
    
    public function render_list() {
        
        $post_types = wpadverts_get_post_types();
        $taxonomies = array();
        
        foreach( $post_types as $post_type ) {
            $taxonomies = array_merge( $taxonomies, get_object_taxonomies( $post_type ) );
        }
        
        $taxonomies = array_unique( $taxonomies );
        sort( $taxonomies );
        
        
        
        include ADVERTS_PATH . 'addons/core/admin/types-list.php';
    }
    
    public function render_edit_post() {
        
        wp_enqueue_script( "adverts-types-post" );
        
        $post_type = get_post_type_object( adverts_request( "edit-post" ) );
        
        $dashicons = $this->_scan_dashicons();
        
        if( ! isset( $_POST ) || empty( $_POST ) ) {
            list( $form_simple, $form_labels, $form_renderers ) = $this->_form_defaults( $post_type );
        } else {
            list( $form_simple, $form_labels, $form_renderers ) = $this->_form_update( $post_type );
        }
        
        $h2_title = sprintf( __("Edit '%s'", "wpadverts"), $post_type->label );
        $restore_url = $this->_get_post_type_restore_url( $post_type->name );
        $button_text = __( "Update", "wpadverts" );
        
        $supports = $form_simple->get_value( "supports" );
        $supports_comments = false;
        if( is_array( $supports ) && in_array( "comments", $supports ) ) {
            $supports_comments = true;
            $without_comments = $this->_get_comment_closed_ads( $post_type->name );
            $enable_url = $this->_get_enable_comments_url( $post_type->name );
        }
        //echo "<pre>";print_r($form_renderers);echo "</pre>";

        include ADVERTS_PATH . 'addons/core/admin/types-edit-post.php';
    }
    
    public function render_edit_taxonomy() {
        
        wp_enqueue_script( "adverts-types-post" );
        
        $taxonomy = get_taxonomy( adverts_request( "edit-taxonomy" ) );
        
        if( ! isset( $_POST ) || empty( $_POST ) ) {
            list( $form_simple, $form_labels, $form_renderers ) = $this->_taxonomy_defaults( $taxonomy );
        } else {
            list( $form_simple, $form_labels, $form_renderers ) = $this->_taxonomy_update( $taxonomy );
        }
        
        $h2_title = sprintf( __("Edit '%s' Taxonomy", "wpadverts"), $taxonomy->label );
        $restore_url = $this->_get_taxonomy_restore_url( $taxonomy->name );
        $button_text = __( "Update", "wpadverts" );
        
        include ADVERTS_PATH . 'addons/core/admin/types-edit-post.php';
    }
    
    public function get_data_types() {
        return apply_filters( "wpadverts_classifieds_types", array( 
            "post_types" => array(
                "title" => __("Classified Types", "wpadverts"),
                "button_text" => __("+ New User Type", "wpadverts"),
                "names" => wpadverts_get_post_types()
            ),
        ));     
    }
    
    public function reset_permalinks() {
        global $wp_rewrite;
        
        $wp_rewrite->flush_rules();
    }


    protected function _get_taxonomy_restore_url( $taxonomy ) {
        return add_query_arg( array( 
            "edit-taxonomy" => false, 
            "noheader" => 1, 
            "restore-taxonomy" => $taxonomy, 
            "_nonce" => wp_create_nonce( "wpadverts-data-type-restore" ) 
        ) );
    }
    
    protected function _get_post_type_restore_url( $post_type ) {
        return add_query_arg( array( 
            "edit-post" => false, 
            "noheader" => 1, 
            "restore-post-type" => $post_type, 
            "_nonce" => wp_create_nonce( "wpadverts-data-type-restore" ) 
        ) );
    }

    protected function _get_enable_comments_url( $post_type ) {
        return add_query_arg( array( 
            "edit-post" => false, 
            "noheader" => 1, 
            "enable-auto-comments" => $post_type, 
            "_nonce" => wp_create_nonce( "wpadverts-auto-enable-comments" ) 
        ) );
    }

    protected function _get_comment_closed_ads( $post_type ) {
        global $wpdb;

        $sql = "SELECT COUNT(*) AS `cnt` FROM {$wpdb->posts} WHERE `post_type`='%s' AND `post_status`='publish' AND `comment_status`='closed'";
        $prep = $wpdb->prepare( $sql, array( $post_type ) );

        return $wpdb->get_var( $prep );
    }
    
    public function restore_post_type() {
        $post_type = adverts_request( "restore-post-type" );
        
        $supported_cpt = wpadverts_get_post_types();
        
        if( ! wp_verify_nonce( adverts_request( "_nonce" ), "wpadverts-data-type-restore" ) ) {
            wp_die( __( "Invalid nonce.", "wpadverts" ) );
        }
        
        if( ! in_array( $post_type, $supported_cpt ) ) {
            wp_die( __( "You are trying to restore unsupported post type.", "wpadverts" ) );
        }
        
        $option = get_option( "wpadverts_post_types" );
        
        if( is_array( $option ) && isset( $option[$post_type] ) ) {
            unset( $option[$post_type] );
        }
        
        update_option( "wpadverts_post_types", $option );
        
        
        $flash = Adverts_Flash::instance();
        $flash->add_info( __( "Classified configuration restored to default.", "wpadverts" ) );
        
        wp_redirect( remove_query_arg( array( "noheader", "restore-post-type", "_nonce" ) ) );
        exit;
    }
    
    public function enable_auto_comments() {
        $post_type = adverts_request( "enable-auto-comments" );
        
        $supported_cpt = wpadverts_get_post_types();
        
        if( ! wp_verify_nonce( adverts_request( "_nonce" ), "wpadverts-auto-enable-comments" ) ) {
            wp_die( __( "Invalid nonce.", "wpadverts" ) );
        }
        
        if( ! in_array( $post_type, $supported_cpt ) ) {
            wp_die( __( "You are trying to restore unsupported post type.", "wpadverts" ) );
        }
        
        global $wpdb;

        $affected = $wpdb->update( 
            $wpdb->posts, 
            array(
                'comment_status' => 'open'
            ),
            array( 
                'post_type' => $post_type,
                'post_status' => 'publish',
                'comment_status' => 'closed'
            ),  
            array(
                '%s'
            ),
            array( 
                '%s',
                '%s',
                '%s'
            )
        );
        
        $flash = Adverts_Flash::instance();

        if( $affected === false ) {
            $flash->add_info( __( "There was an error while executing a MySQL query.", "wpadverts" ) );
        } else {
            $flash->add_info( sprintf( __( "%d comments enabled. If you are using some caching plugin make sure to clear cache.", "wpadverts" ), $affected ) );
        }
        $redirect_url = add_query_arg( array( 'edit-post' => $post_type, "noheader" => false, "enable-auto-comments" => false, "_nonce" => false ) );

        wp_redirect( $redirect_url );
        exit;
    }

    public function restore_taxonomy() {
        $taxonomy = adverts_request( "restore-taxonomy" );
        
        if( ! wp_verify_nonce( adverts_request( "_nonce" ), "wpadverts-data-type-restore" ) ) {
            wp_die( __( "Invalid nonce.", "wpadverts" ) );
        }
        
        $option = get_option( "wpadverts_taxonomies" );
        
        if( is_array( $option ) && isset( $option[$taxonomy] ) ) {
            unset( $option[$taxonomy] );
        }
        
        update_option( "wpadverts_taxonomies", $option );
        
        
        $flash = Adverts_Flash::instance();
        $flash->add_info( __( "Taxonomy configuration restored to default.", "wpadverts" ) );
        
        wp_redirect( remove_query_arg( array( "noheader", "restore-taxonomy", "_nonce" ) ) );
        exit;
    }
    
    protected function _form_defaults( $post_type ) {
        
        $form_simple = new Adverts_Form();
        $form_simple->load( $this->_edit_post_form_simple( $post_type ) );
        
        $option = get_option( "wpadverts_post_types" );
        $comments_auto_enable = 0;
        
        $pt = $post_type->name;

        if( isset( $option[$pt] ) && isset( $option[$pt]["_comments_auto_enable"] ) ) {
            $comments_auto_enable = $option[$pt]["_comments_auto_enable"];
        }

        $bind_default = array(
            "name" => $post_type->name,
            "label" => $post_type->label,
            "exclude_from_search" => $post_type->exclude_from_search,
            "menu_position" => $post_type->menu_position,
            "menu_icon" => $post_type->menu_icon,
            "supports" => array_keys( get_all_post_type_supports( $post_type->name ) ),
            "rewrite_slug" => $post_type->rewrite["slug"],
            "_comments_auto_enable" => $comments_auto_enable
        );
        
        $form_simple->bind( $bind_default );
        
        $form_labels = new Adverts_Form();
        $form_labels->load( $this->_edit_post_form_labels( $post_type ) );

        $form_renderers = new Adverts_Form();
        $form_renderers->load( $this->_edit_form_renderers( "post", $post_type->name ) );

        include_once ADVERTS_PATH . '/includes/class-block-templates.php';
        $block_templates = new Adverts_Block_Templates();

        $engine = $block_templates->get_post_render_method( $post_type->name, false );
        $template = $block_templates->get_post_template_id( $post_type->name );

        $bind_renderers = array(
            "renderers__engine" => $engine === null ? "global" : $engine,
            "renderers__template" => $template,
        );

        $form_renderers->bind( $bind_renderers );

        return array( $form_simple, $form_labels, $form_renderers );
    }
    
    protected function _taxonomy_defaults( $taxonomy ) {
        
        $form_simple = new Adverts_Form();
        $form_simple->load( $this->_edit_taxonomy_form_simple( $taxonomy) );
        
        $bind_default = array(
            "name" => $taxonomy->name,
            "label" => $taxonomy->label,
            "hierarchical" => $taxonomy->hierarchical,
            "rewrite_slug" => $taxonomy->rewrite["slug"],
            "rewrite_hierarchical"=> $taxonomy->rewrite["hierarchical"],
            "__connect_to" => $taxonomy->object_type
        );
        
        $form_simple->bind( $bind_default );
        
        $form_labels = new Adverts_Form();
        $form_labels->load( $this->_edit_taxonomy_form_labels( $taxonomy ) );
        
        $form_renderers = new Adverts_Form();
        $form_renderers->load( $this->_edit_form_renderers( "taxonomy", $taxonomy->name ) );

        include_once ADVERTS_PATH . '/includes/class-block-templates.php';
        $block_templates = new Adverts_Block_Templates();

        $engine = $block_templates->get_taxonomy_render_method( $taxonomy->name, false );
        $template = $block_templates->get_taxonomy_template_id( $taxonomy->name );

        $bind_renderers = array(
            "renderers__engine" => $engine === null ? "global" : $engine,
            "renderers__template" => $template,
        );

        $form_renderers->bind( $bind_renderers );

        return array( $form_simple, $form_labels, $form_renderers );
    }
    
    protected function _form_update( $post_type ) {
        
        if( ! isset( $_POST ) || empty( $_POST ) ) {
            return;
        }
        
        $bind = stripslashes_deep( $_POST );
        
        $form_simple = new Adverts_Form();
        $form_simple->load( $this->_edit_post_form_simple( $post_type ) );
        $form_simple->bind( $bind );
        
        if( ! $form_simple->validate() ) {
            $flash = Adverts_Flash::instance();
            $flash->add_error( __( "There are errors in your form", "wpadverts" ) );
            
            return $form_simple;
        }
        
        $option = get_option( "wpadverts_post_types" );
        
        if( ! is_array( $option ) ) {
            $option = array();
        }
        
        $rewrite_slug = trim( $form_simple->get_value( "rewrite_slug", $post_type->name ) );
        
        $values = $form_simple->get_values();
        $values["exclude_from_search"] = isset( $values["exclude_from_search"] ) ? $values["exclude_from_search"] : 0;
        $values["_comments_auto_enable"] = isset( $values["_comments_auto_enable"] ) ? 1 : 0;
        $values["rewrite"] = array(
            "slug" => $rewrite_slug,
            "with_front" => false,
            "feeds" => 1,
            "pages" => 1,
            "ep_mask" => 1
        );
        $values["labels"] = array( "name" => $values["label"] );
        
        $form_labels = new Adverts_Form();
        $form_labels->load( $this->_edit_post_form_labels( $post_type ) );
        
        $form_labels->bind( stripslashes_deep( $_POST ) );
        $labels_all = $form_labels->get_values();
        
        if( is_array( $labels_all ) && isset( $labels_all["labels"] ) && ! empty( $labels_all["labels"] ) ) {
            $values["labels"] = $labels_all["labels"];
        }
        
        unset( $values["rewrite_slug"] );

        $option[ $post_type->name ] = $values;
        
        update_option( "wpadverts_post_types", $option );
        
        $form_renderers = new Adverts_Form();
        $form_renderers->load( $this->_edit_form_renderers( "post", $post_type->name ) );
        $form_renderers->bind( stripslashes_deep( $_POST ) );

        $engine = $form_renderers->get_value( "renderers__engine");
        $template = $form_renderers->get_value( "renderers__template" );

        include_once ADVERTS_PATH . '/includes/class-block-templates.php';
        Adverts_Block_Templates::save( "post", $post_type->name, $engine, $template );

        $info = __( "Classifieds type updated.", "wpadverts" ) . "<br/>";
        $info.= __( 'Remember to reset permalinks by clicking "Save Changes" button in the <a href="%s">Permalinks</a> panel.', "wpadverts" );
        
        $flash = Adverts_Flash::instance();
        $flash->add_info( sprintf( $info, admin_url( 'options-permalink.php') ) );

        return array( $form_simple, $form_labels, $form_renderers );
    }
    
    protected function _taxonomy_update( $taxonomy ) {
        
        if( ! isset( $_POST ) || empty( $_POST ) ) {
            return;
        }
        
        $bind = stripslashes_deep( $_POST );
        
        $form_simple = new Adverts_Form();
        $form_simple->load( $this->_edit_taxonomy_form_simple( $taxonomy ) );
        $form_simple->bind( $bind );
        
        if( ! $form_simple->validate() ) {
            $flash = Adverts_Flash::instance();
            $flash->add_error( __( "There are errors in your form", "wpadverts" ) );
            
            return $form_simple;
        }
        
        $option = get_option( "wpadverts_taxonomies" );
        
        if( ! is_array( $option ) ) {
            $option = array();
        }

        $values = $form_simple->get_values();
        $values["__connect_to"] = $form_simple->get_value( "__connect_to", array() );
        $values["hierarchical"] = isset( $values["hierarchical"] ) ? $values["hierarchical"] : 0;
        $values["menu_position"] = $values["menu_position"];
        
        $rewrite_slug = trim( $form_simple->get_value( "rewrite_slug", $taxonomy->name ) );
        $rewrite_h = absint( $form_simple->get_value( "rewrite_hierarchical", 0 ) );
        $rewrite_hierarchical = ( $values["hierarchical"] && $rewrite_h ) ? 1 : 0;
        
        
        $values["rewrite"] = array(
            "slug" => $rewrite_slug,
            "with_front" => false,
            "hierarchical" => $rewrite_hierarchical,
            "ep_mask" => 1
        );
        $values["labels"] = array( "name" => $values["label"] );
        
        $form_labels = new Adverts_Form();
        $form_labels->load( $this->_edit_taxonomy_form_labels( $taxonomy ) );
        
        $form_labels->bind( stripslashes_deep( $_POST ) );
        $labels_all = $form_labels->get_values();
        
        if( is_array( $labels_all ) && isset( $labels_all["labels"] ) && ! empty( $labels_all["labels"] ) ) {
            $values["labels"] = $labels_all["labels"];
        }
        
        unset( $values["rewrite_slug"] );
        unset( $values["rewrite_hierarchical"] );

        $option[ $taxonomy->name ] = $values;
        
        update_option( "wpadverts_taxonomies", $option );
        
        $form_renderers = new Adverts_Form();
        $form_renderers->load( $this->_edit_form_renderers( "taxonomy", $taxonomy->name ) );
        $form_renderers->bind( stripslashes_deep( $_POST ) );

        $engine = $form_renderers->get_value( "renderers__engine");
        $template = $form_renderers->get_value( "renderers__template" );

        include_once ADVERTS_PATH . '/includes/class-block-templates.php';
        Adverts_Block_Templates::save( "taxonomy", $taxonomy->name, $engine, $template );

        $info = __( "Taxonomy updated.", "wpadverts" ) . "<br/>";
        $info.= __( 'Remember to reset permalinks by clicking "Save Changes" button in the <a href="%s">Permalinks</a> panel.', "wpadverts" );
        
        $flash = Adverts_Flash::instance();
        $flash->add_info( sprintf( $info, admin_url( 'options-permalink.php') ) );
        
        return array( $form_simple, $form_labels, $form_renderers );
    }
    
    protected function _get_labels() {
        $labels = array();
        
        return $labels;
    }
    
    protected function _edit_post_form_simple( $post_type ) {
        
        $taxonomies = array();
        foreach( get_taxonomies() as $tkey => $tval ) {
            $taxonomies[] = array( "value" => $tkey, "text" => ucfirst( str_replace( "_", " ", $tval ) ) );
        }
        
        $url_pattern = '<code>https://example.com/<strong class="wpadverts-admin-type-slug-preview">-</strong>/test-ad/</code><br/>';
        $url_taxo = remove_query_arg( array( 'edit-post' ));
        
        $form_scheme = array(
            "name" => "types-post-main",
            "field" => array(
                array(
                    "name" => "name",
                    "type" => "adverts_field_text",
                    "label" => __( "Name", "wpadverts" ),
                    "order" => 10,
                    "attr" => array( "readonly" => "readonly" )
                ),
                array(
                    "name" => "label",
                    "type" => "adverts_field_text",
                    "label" => __( "Label", "wpadverts" ),
                    "order" => 10,
                ),
                array(
                    "name" => "exclude_from_search",
                    "type" => "adverts_field_checkbox",
                    "label" => __( "Exclude From Search", "wpadverts" ),
                    "order" => 10,
                    "options" => array(
                        array( "value" => "1", "text" => __( "Exclude posts with this post type from front end search results.", "wpadverts" ) )
                    ),
                ),
                array(
                    "name" => "menu_position",
                    "type" => "adverts_field_text",
                    "label" => __( "Menu Position", "wpadverts" ),
                    "order" => 10,
                    "attr" => array(
                        "type" => "number",
                        "min" => 0,
                        "max" => 10000,
                        "step" => 1
                    )
                ),
                array(
                    "name" => "menu_icon",
                    "type" => "adverts_field_text",
                    "label" => __( "Menu Icon", "wpadverts" ),
                    "order" => 10,
                ),
                array(
                    "name" => "supports",
                    "type" => "adverts_field_checkbox",
                    "label" => __( "Supports", "wpadverts" ),
                    "max_choices" => 100,
                    "order" => 10,
                    "options" => array(
                        array( "value" => "title", "text" => __( "Title", "wpadverts" ), "disabled" => "disabled" ),
                        array( "value" => "editor", "text" => __( "Editor", "wpadverts" ) ),
                        array( "value" => "author", "text" => __( "Author", "wpadverts" ) ),
                        array( "value" => "thumbnail", "text" => __( "Thumbnail (Featured Image)", "wpadverts" ) ),
                        array( "value" => "excerpt", "text" => __( "Excerpt", "wpadverts" ) ),
                        array( "value" => "trackbacks", "text" => __( "Trackbacks", "wpadverts" ) ),
                        array( "value" => "comments", "text" => __( "Comments", "wpadverts" ) ),
                    )
                ),
                array(
                    "name" => "_comments_auto_enable",
                    "type" => "adverts_field_checkbox", 
                    "label" => __( "Comments", "wpadverts" ),
                    "max_choices" => 1,
                    "order" => 10,
                    "options" => array(
                        array( "value" => "1", "text" => __( "Automatically enable comments when Ad is saved in the database.", "wpadverts" ) )
                    )
                ),
                array(
                    "name" => "taxonomies",
                    "type" => "adverts_field_label",
                    "label" => __( "Taxonomies", "wpadverts" ),
                    "order" => 10,
                    "content" => sprintf( __( 'You can assign taxonomies to this post type while <a href="%s">creating or editing</a> them.', "wpadverts"), $url_taxo )
                ),
                array(
                    "name" => "rewrite_slug",
                    "type" => "adverts_field_text",
                    "label" => __( "Permalink Prefix", "wpadverts" ),
                    "order" => 10,
                    "attr" => array(
                        "placeholder" => $post_type->name
                    )
                    
                ),
                array(
                    "name" => "_rewrite_slug",
                    "type" => "adverts_field_label",
                    "label" => "",
                    "order" => 10,
                    "content" => sprintf( __( 'Preview: %s To create more advanced permalink schemes consider using a plugin like <a href="https://wordpress.org/plugins/custom-post-type-permalinks/">Custom Post Types Permalinks</a>.', 'wpadverts' ), $url_pattern ),
                    
                ),
            )
        );
        
        return $form_scheme;
    }
    
    protected function _edit_taxonomy_form_simple( $post_type ) {
        
        $taxonomy = get_taxonomy( adverts_request( "edit-taxonomy" ) );
        
        $url_pattern = '<code>https://example.com/<strong class="wpadverts-admin-type-slug-preview">-</strong>/category-name/<span class="wpadverts-admin-type-slug-preview-sub">sub-category/</code><br/>';
        
        $connect_to = array();
        $connect = array();
        foreach( $this->get_data_types() as $key => $data_type ) {
            $connect = array_merge( $connect, $data_type["names"] );
        }
        sort( $connect );
        foreach( $connect as $post_type ) {
            $pt = get_post_type_object( $post_type );
            $connect_to[] = array(
                "value" => $post_type,
                "text" => sprintf( '%s <code>%s</code>', $pt->label, $post_type )
            );
        }

        $form_scheme = array(
            "name" => "types-taxonomy-main",
            "field" => array(
                array(
                    "name" => "name",
                    "type" => "adverts_field_text",
                    "label" => __( "Name", "wpadverts" ),
                    "order" => 10,
                    "attr" => array( "readonly" => "readonly" )
                ),
                array(
                    "name" => "label",
                    "type" => "adverts_field_text",
                    "label" => __( "Label", "wpadverts" ),
                    "order" => 10,
                ),
                array(
                    "name" => "__connect_to",
                    "type" => "adverts_field_checkbox",
                    "label" => __( "Connect To", "wpadverts" ),
                    "order" => 10,
                    "max_choices" => 100,
                    "options" => $connect_to
                ),
                array(
                    "name" => "hierarchical",
                    "type" => "adverts_field_checkbox",
                    "label" => __( "Hierarchical", "wpadverts" ),
                    "order" => 10,
                    "options" => array(
                        array( "value" => "1", "text" => __( "This taxonomy is hierarchical.", "wpadverts" ) )
                    ),
                ),
                array(
                    "name" => "rewrite_slug",
                    "type" => "adverts_field_text",
                    "label" => __( "Permalink Prefix", "wpadverts" ),
                    "order" => 10,
                    "attr" => array(
                        "placeholder" => $taxonomy->name
                    )
                    
                ),
                array(
                    "name" => "rewrite_hierarchical",
                    "type" => "adverts_field_checkbox",
                    "label" => "",
                    "order" => 10,
                    "options" => array(
                        array( "value" => "1", "text" => __( "Use hierarchical URLs.", "wpadverts" ) )
                    ),
                    
                ),
                array(
                    "name" => "_rewrite_slug",
                    "type" => "adverts_field_label",
                    "label" => "",
                    "order" => 10,
                    "content" => sprintf( __( 'Preview: %s To create more advanced permalink schemes consider using a plugin like <a href="https://wordpress.org/plugins/custom-post-type-permalinks/">Custom Post Types Permalinks</a>.', 'wpadverts' ), $url_pattern )
                    
                ),
            )
        );
        
        return $form_scheme;
    }
    
    protected function _edit_post_form_labels( $post_type ) {
        
        $form_scheme = array(
            "name" => "types-post-labels",
            "field" => array( )
        );
        
        $post_type_object = new WP_Post_Type( $post_type->name, Adverts_Types::get_cpt_defaults( $post_type->name ) );
        $labels_default = get_post_type_labels( $post_type_object );

        $labels = array();
        $option = get_option( "wpadverts_post_types" );
        
        if( isset( $option[ $post_type->name ] ) && isset( $option[ $post_type->name ]["labels"] ) ) {
            $labels = $option[ $post_type->name ]["labels"];
        }
        
        foreach( $post_type->labels as $k => $label ) {
            $form_scheme["field"][] = array(
                "name" => sprintf( "labels[%s]", $k ),
                "type" => "adverts_field_text",
                "label" => ucwords( str_replace( "_", " ", $k ) ),
                "order" => 10,
                "attr" => array( "placeholder" => isset( $labels_default->$k ) ? $labels_default->$k : "" ),
                "value" => isset( $labels[$k] ) ? $labels[$k] : ""
            );
        }
        
        
        return $form_scheme;
    }

    protected function _edit_form_renderers( $object, $type ) {

        $form_scheme = array(
            "name" => sprintf( "%s_%s", $object, $type ),
            "field" => array(
                array(
                    "name" => sprintf( "renderers__%s", "engine" ),
                    "type" => "adverts_field_radio",
                    "label" => __( "Renderer Type", "wpadverts" ),
                    "order" => 10,
                    "value" => "",
                    "options" => array(
                        array( "value" => "global", "text" => __( "Use default method.", "wpadverts" ) ),
                        array( "value" => "shortcode", "text" => __( "Shortcode - use the old version 1.5 templates.", "wpadverts" ) ),
                        array( "value" => "block", "text" => __( "Block (recommended) - use block templates.", "wpadverts" ) ),
                        array( "value" => "none", "text" => __( "None - you will need to create your own templates.", "wpadverts" ) ),
                    )
                ),
                array(
                    "name" => sprintf( "renderers__%s", "template" ),
                    "type" => "adverts_field_select",
                    "label" => __( "Renderer Template", "wpadverts" ),
                    "order" => 10,
                    "value" => "",
                    "options" => wpadverts_get_block_templates_options()
                )
            )
        );

        return $form_scheme;
    }
    
    protected function _edit_taxonomy_form_labels( $taxonomy ) {
        
        $form_scheme = array(
            "name" => "types-taxonomy-labels",
            "field" => array( )
        );

        $tax_defaults = Adverts_Types::get_taxonomy_defaults( $taxonomy->name ) ;
        $taxonomy_object = new WP_Taxonomy( $taxonomy->name, "advert", $tax_defaults );
        $labels_default = get_taxonomy_labels( $taxonomy_object );

        $labels = array();
        $option = get_option( "wpadverts_taxonomies" );
        if( isset( $option[ $taxonomy->name ] ) && isset( $option[ $taxonomy->name ]["labels"] ) ) {
            $labels = $option[ $taxonomy->name ]["labels"];
        }

        foreach( $taxonomy->labels as $k => $label ) {
            $form_scheme["field"][] = array(
                "name" => sprintf( "labels[%s]", $k ),
                "type" => "adverts_field_text",
                "label" => ucwords( str_replace( "_", " ", $k ) ),
                "order" => 10,
                "attr" => array( "placeholder" => isset( $labels_default->$k ) ? $labels_default->$k : "" ),
                "value" => isset( $labels[$k] ) ? $labels[$k] : ""
            );
        }
        
        
        return $form_scheme;
    }
    
    protected function _edit_post_form_taxonomies( $post_type ) {
        
        $form_scheme = array(
            "name" => "types-post-taxonomies",
            "field" => array( )
        );
        
        foreach( $post_type->labels as $k => $label ) {
            $form_scheme["field"][] = array(
                "name" => $k,
                "type" => "adverts_field_text",
                "label" => ucfirst( str_replace( "_", " ", $k ) ),
                "order" => 10,
                "attr" => array( "placeholder" => $label )
            );
        }
        
        
        return $form_scheme;
    }
    
    protected function _scan_dashicons() {
        
        $dashicons = array();
        $file_path = get_home_path() . 'wp-includes/css/dashicons.css';
        $lines = file( $file_path );
        $scan = false;
        
        foreach( $lines as $line ) {
            $line = trim( $line );
            

            if( $line === "/* Icons */" ) {
                $scan = true;
            }
            
            if( ! $scan ) {
                continue;
            }
            
            preg_match( "/(dashicons-[a-z0-9\-]+):before/", $line, $match );

            if( is_array( $match ) && isset( $match[1] ) ) {
                $dashicons[] = $match[1];
            }
            
        }
        
        return $dashicons;
    }
}