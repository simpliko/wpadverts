<?php
/**
 * AJAX Actions
 * 
 * This functions are executed when user is doing an AJAX request.
 *
 * @package     Adverts
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Displays Adverts author suggest box (when editing advert in wp-admin)
 * 
 * Action: adverts_author_suggest
 * 
 * @since 0.1
 * @return void
 */
function adverts_author_suggest() {
    
    // Find users matching user query
    $users = new WP_User_Query( array(
        'search'         => '*'.esc_attr( $_GET["q"] ).'*',
        'search_columns' => array(
            'user_login',
            'display_name',
            'user_email',
        ),
    ) );
    
    $users_found = $users->get_results();
    
    foreach($users_found as $user) {
        echo '<span data-id="'.$user->ID.'">'.esc_html($user->display_name).'</span>';
        echo '<!-- suggest delimeter -->';
    }
    
    echo '<span data-id="0+"><em>Anonymous</em></span>';
    echo '<!-- suggest delimeter -->';
    
    exit;
}

/**
 * Uploads Gallery Image
 * 
 * Action: adverts_gallery_upload
 * 
 * @since 0.1
 * @return void
 */
function adverts_gallery_upload() {

    if( ! check_ajax_referer( 'adverts-gallery', '_ajax_nonce', false ) ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Invalid Session. Please refresh the page and try again.", "adverts" ) 
        ) );
        
        exit;
    }

    include_once ADVERTS_PATH . '/includes/class-upload-helper.php';
    $v = new Adverts_Upload_Helper;
    $field_name = adverts_request( "field_name" );
    $form_params = array(
        "form_scheme" => adverts_request( "_form_scheme" ),
        "form_scheme_id" => adverts_request( "_form_scheme_id" )
    );
    $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form"), $form_params );
    $form_scheme = apply_filters( "adverts_form_load", $form_scheme );

    foreach($form_scheme["field"] as $key => $field) {
        if($field["name"] == $field_name ) {
            if(isset($field["validator"]) && is_array($field["validator"])) {
                foreach($field["validator"] as $vcallback) {
                    $v->add_validator($vcallback);
                }
            }
            
        }
    }
    
    add_filter( "adverts_gallery_upload_prefilter", array( $v, "check" ) );

    // you can use WP's wp_handle_upload() function:
    $status = wp_handle_upload($_FILES['async-upload'], array('test_form'=>true, 'action' => 'adverts_gallery_upload'));

    if(isset($status['error'])) {
        echo json_encode($status);
        exit;
    }

    // $filename should be the path to a file in the upload directory.
    $filename = $status['file'];

    // The ID of the post this attachment is for.
    $parent_post_id = intval($_POST["post_id"]);

    // Check the type of tile. We'll use this as the 'post_mime_type'.
    $filetype = wp_check_filetype( basename( $filename ), null );

    // Get the path to the upload directory.
    $wp_upload_dir = wp_upload_dir();

    // Prepare an array of post data for the attachment.
    $attachment = array(
        'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
        'post_mime_type' => $filetype['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    // Create post if does not exist
    if( $parent_post_id < 1 ) {
        
        add_filter("post_type_link", "__return_empty_string");
        
        $parent_post_id = wp_insert_post( apply_filters( "adverts_insert_post", array( 
            'post_title'        => 'Adverts Auto Draft',
            'post_content'      => '',
            'post_status'       => adverts_tmp_post_status(),
            'post_author'       => wp_get_current_user()->ID,
            'post_type'         => 'advert',
            'comments_status'   => 'closed'
        ) ) );
        
        remove_filter("post_type_link", "__return_empty_string");
    }
    
    // Insert the attachment.
    $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
    if ( !is_wp_error( $attach_id ) ) {
        wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $filename ) );
    }
        
    include_once ADVERTS_PATH . 'includes/gallery.php';
    
    echo json_encode( adverts_upload_item_data( $attach_id ) );
    exit;
};

/**
 * Updates attachment meta data
 * 
 * This function is executed when user updates uploaded media (image) title,
 * caption, description or is_featured flag.
 * 
 * Action: adverts_gallery_update
 * 
 * @since 0.1
 * @return void
 */
function adverts_gallery_update() {
    
    if( ! check_ajax_referer( 'adverts-gallery', '_ajax_nonce', false ) ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Invalid Session. Please refresh the page and try again.", "adverts" ) 
        ) );
        exit;
    }
       
    $post_id = intval($_POST["post_id"]);
    $attach_id = intval($_POST["attach_id"]);
    $caption = trim( adverts_request("caption", "" ) );
    $content = trim( adverts_request("content", "" ) );
    $featured = intval($_POST["featured"]);
    
    $attach = get_post( $attach_id );
    
    if( $attach->post_parent != $post_id ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Incorrect Post or Attachment ID.", "adverts" ) 
        ) );
        exit;
    }
    
    $result = wp_update_post(array(
        "ID" => $attach_id,
        "post_content" => $content,
        "post_excerpt" => $caption
    ));
    
    if($result instanceof WP_Error) {
        echo json_encode( array( "result" => 0, "error" => $result->get_error_message() ) );
        exit;
    }
    
    $featured_id = get_post_meta( $post_id, '_thumbnail_id', true );

    if( $featured == "1" ) {
        update_post_meta( $post_id, '_thumbnail_id', $attach_id );
    } elseif( $featured_id == $attach_id ) {
        delete_post_meta( $post_id, '_thumbnail_id' );
    }
    
    echo json_encode( array( "result" => 1, "file" => adverts_upload_item_data( $attach_id ) ) );
    exit;
}


/**
 * Updates order of attachments (as JSON) in wp_postmeta table
 * under the key '_adverts_attachments_order'.
 *
 * This function is executed when user changes the order of
 * images in the gallery using drag and drop, or an image is
 * added/deleted.
 *
 * Action: adverts_gallery_update_order
 *
 * @see assets/js/adverts-gallery.js
 * @since 1.0.13
 */
function adverts_gallery_update_order() {
    
    if (!check_ajax_referer('adverts-gallery', '_ajax_nonce', false)) {
        echo json_encode(array(
            "result" => 0,
            "error" => __("Invalid Session. Please refresh the page and try again.", "adverts")
        ));

        exit;
    }

    $post_id = intval( adverts_request( "post_id" ) );

    $dirty_ordered_keys = json_decode( stripslashes( adverts_request( "ordered_keys" ) ) );
    $length = sizeof( $dirty_ordered_keys );
    $clean_ordered_keys = array();

    for ( $i = 0; $i < $length; $i++ ) {
        $clean_ordered_keys[$i] = intval( $dirty_ordered_keys[$i] );
    }

    $clean_ordered_keys_json = json_encode( $clean_ordered_keys );

    update_post_meta( $post_id, '_adverts_attachments_order', $clean_ordered_keys_json );

    echo json_encode( array( "result" => 1 ) );
    exit;
}

/**
 * Deletes one gallery attachmenent (image)
 * 
 * Action: adverts_gallery_delete
 * 
 * @since 0.1
 * @return void
 */
function adverts_gallery_delete() {
    
    if( ! check_ajax_referer( 'adverts-gallery', '_ajax_nonce', false ) ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Invalid Session. Please refresh the page and try again.", "adverts" ) 
        ) );
        
        exit;
    }
    
    $attach_id = intval($_POST["attach_id"]);
    $attach = get_post( $attach_id );

    if ( $attach === null ) {
        echo json_encode( array( "result" => 0, "error" => __( "Attachment does not exist.", "adverts" ) ) );
    } elseif ( $attach->post_parent != absint(adverts_request( "post_id" ) ) ) {
        echo json_encode( array( "result" => 0, "error" => __( "Incorrect attachment ID.", "adverts" ) ) );
    } elseif ( wp_delete_attachment( $attach_id ) ) {
        echo json_encode( array( "result" => 1 ) );
    } else {
        echo json_encode( array( "result" => 0, "error" => __( "File could not be deleted.", "adverts" ) ) );
    }
    
    exit;
}

/**
 * Generates image preview
 * 
 * This function is executed by the Gallery, it displays resized and cropped image.
 * 
 * Action: adverts_gallery_image_stream
 * 
 * @since 1.2
 * @return void
 */
function adverts_gallery_image_stream() {
    
    if( ! check_ajax_referer( 'adverts-gallery', '_ajax_nonce', false ) ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Invalid Session. Please refresh the page and try again.", "adverts" ) 
        ) );
        exit;
    }
    
    if( ! adverts_user_can_edit_image() ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "You cannot edit images.", "adverts" ) 
        ) );
        exit;
    }
    
    $attach_id = adverts_request( "attach_id" );
    $history_encoded = adverts_request( "history" );
    $size = adverts_request( "size" );
    $post_id = intval( adverts_request( "post_id" ) );
    
    $attach = get_post( $attach_id );
    
    if( $attach->post_parent != $post_id ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Incorrect Post or Attachment ID.", "adverts" ) 
        ) );
        exit;
    }
    
    $history = json_decode( $history_encoded );
    
    if( ! is_array( $history ) ) {
        $history = array();
    }
    

    if( wp_attachment_is_image( $attach_id ) ) {
        $attached_file = get_attached_file( $attach_id );
    } else {
        $attached_file = wp_get_attachment_image_src( $attach_id, "full" );
        $attached_file = dirname( get_attached_file( $attach_id ) ) . "/" . basename( $attached_file[0] );
    }
    
    if( $size ) {
        $upload = adverts_upload_item_data( $attach_id );
        $attached_file = dirname( $attached_file ) . "/" . basename( $upload["sizes"][$size]["url"] );
    } else {
        $upload = adverts_upload_item_data( $attach_id );
        if(isset($upload["sizes"]["full"]["is_intermidiate"]) && $upload["sizes"]["full"]["is_intermidiate"]) {
            $attached_file = $upload["sizes"]["full"]["url"];
        }
    }

    $image = wp_get_image_editor( $attached_file );

    foreach( $history as $c ) {
        if( ! isset( $c->a ) ) {
            continue;
        }
        
        if( $c->a == "c" ) {
            $image->crop(intval($c->x), intval($c->y), $c->w, $c->h);
        } else if( $c->a == "ro" ) {
            $image->rotate($c->v);
        } else if( $c->a == "re" ) {
            // resize
            $image->resize($c->w, $c->h);
        } else if( $c->a == "f" ) {
            $image->flip($c->h, $c->v);
        }
    }

    $image->stream();
    
    exit;
}

/**
 * Restores default image(s)
 * 
 * This function is executed by the Gallery, it restores original image sizes.
 * 
 * Action: adverts_gallery_image_stream
 * 
 * @since 1.2
 * @return void
 */
function adverts_gallery_image_restore() {
    
    if( ! check_ajax_referer( 'adverts-gallery', '_ajax_nonce', false ) ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Invalid Session. Please refresh the page and try again.", "adverts" ) 
        ) );
        
        exit;
    }
    
    if( ! adverts_user_can_edit_image() ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "You cannot edit images.", "adverts" ) 
        ) );
        exit;
    }
    
    $size = adverts_request( "size" );
    $attach_id = adverts_request( "attach_id" );
    $post_id = intval( adverts_request( "post_id" ) );
    
    $attach = get_post( $attach_id );
    
    if( $attach->post_parent != $post_id ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Incorrect Post or Attachment ID.", "adverts" ) 
        ) );
        exit;
    }
    
    if( $size === "full" ) {
        // restore all
        $keys = array_keys( adverts_config( "gallery.image_sizes" ) );
        $restore = array_merge( array( "full" ),  $keys );
    } else {
        $restore = array( str_replace( "_", "-", $size ) );
    }
    
    $meta = wp_get_attachment_metadata( $attach_id );
    $attachment_dir = dirname( get_attached_file( $attach_id ) );
    $backup_sizes = get_post_meta( $attach_id, '_wp_attachment_backup_sizes', true );

    foreach( $restore as $r ) {
        if( isset( $backup_sizes[$r . '-orig'] ) ) {
            wp_delete_file( $attachment_dir . "/" . $meta["sizes"][$r]["file"] );
            $meta["sizes"][$r] = $backup_sizes[$r . '-orig'];
            unset( $backup_sizes[$r . '-orig'] );
        }
    }
    
    wp_update_attachment_metadata( $attach_id, $meta );
    update_post_meta( $attach_id, '_wp_attachment_backup_sizes', $backup_sizes );
    
    $result = new stdClass();
    $result->result = 1;
    $result->file = adverts_upload_item_data( $attach_id );
    
    echo json_encode( $result );
    exit;
}

/**
 * Saves Image
 * 
 * This function is executed by the Gallery, after clicking "Save" image in 
 * the image edition panel.
 * 
 * When saving an image the default images are saved as backup and the resized 
 * images are used as current images.
 * 
 * @since 1.2
 * @return void
 */
function adverts_gallery_image_save() {
    
    if( ! check_ajax_referer( 'adverts-gallery', '_ajax_nonce', false ) ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Invalid Session. Please refresh the page and try again.", "adverts" ) 
        ) );
        
        exit;
    }
    
    if( ! adverts_user_can_edit_image() ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "You cannot edit images.", "adverts" ) 
        ) );
        exit;
    }
    
    $attach_id = adverts_request( "attach_id" );
    $action_type = adverts_request( "action_type" );
    $history_encoded = adverts_request( "history" );
    $post_id = adverts_request( "post_id" );
    
    $size_dash = adverts_request( "size" );
    $size = str_replace("_", "-", adverts_request( "size" ));
    
    $attach = get_post( $attach_id );
    $history = json_decode( $history_encoded );
    

    if( $attach->post_parent != $post_id ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Incorrect Post or Attachment ID.", "adverts" ) 
        ) );
        exit;
    }
    
    if( ! is_array( $history ) ) {
        $history = array();
    }
    
    $attached_file = get_attached_file( $attach_id );
    
    $file_name = pathinfo( $attached_file, PATHINFO_FILENAME );
    
    if( $size && $action_type == "edit" ) {
        $upload = adverts_upload_item_data( $attach_id );
        $attached_file = dirname( $attached_file ) . "/" . basename( $upload["sizes"][$size_dash]["url"] );
    } else if($action_type == "create" ) {
        $upload = adverts_upload_item_data( $attach_id );
        $attached_file = dirname( $attached_file ) . "/" . basename( $upload["sizes"]["full"]["url"] );
    }

    $image = wp_get_image_editor( $attached_file );

    if( is_wp_error($image) ) {
        echo json_encode( array(
            "result" => 0,
            "error" => $image->get_error_message()
        ) );
        exit;
    }
    
    foreach( $history as $c ) {
        if( ! isset( $c->a ) ) {
            continue;
        }
        
        if( $c->a == "c" ) {
            $image->crop($c->x, $c->y, $c->w, $c->h);
        } else if( $c->a == "ro" ) {
            $image->rotate($c->v);
        } else if( $c->a == "re" ) {
            // resize
            $image->resize($c->w, $c->h);
        } else if( $c->a == "f" ) {
            $image->flip($c->h, $c->v);
        }
    }
    
    $return = new stdClass();
    
    $backup_sizes = get_post_meta( $attach_id, '_wp_attachment_backup_sizes', true );
    $meta = wp_get_attachment_metadata( $attach_id );
    
    $basename = pathinfo( $attached_file, PATHINFO_BASENAME );
    $dirname = pathinfo( $attached_file, PATHINFO_DIRNAME );
    $ext = pathinfo( $attached_file, PATHINFO_EXTENSION );
    $filename = pathinfo( $attached_file, PATHINFO_FILENAME );
    $suffix = time() . rand(100, 999);
    
    $is_resized = preg_match( '/-e([0-9]+)$/', $filename );
    
    if( $action_type == "create" && $size != "full" ) {
        $sizes = adverts_config( "gallery.image_sizes" );
        $filename = sprintf("%s-%dx%d", $filename, $sizes[$size]["width"], $sizes[$size]["height"] );
    }
    
    while ( true ) {
        $filename = preg_replace( '/-e([0-9]+)$/', '', $filename );
        $filename .= "-e{$suffix}";
        $new_filename = "{$filename}.{$ext}";
        $new_path = "{$dirname}/$new_filename";
        if ( file_exists($new_path) ) {
            $suffix++;
        } else {
            break;
        }
    }

    $saved = $image->save( $new_path );
    
    if(!$saved) {
        echo json_encode( array(
            "result" => 0,
            "error" => $image->get_error_message()
        ) );
        exit;
    }
    
    if( $is_resized ) {
        // working on already resized file, just delete the old file and set
        // new file name in meta $size
        $s = $meta["sizes"][$size];
        
        if ( ! empty( $s['file'] ) ) {
            
            // delete old resized file
            $delete_file = path_join( $dirname, $s['file'] );
            wp_delete_file( $delete_file );

        }
        
    } else {
        // working on new image, save the new file name in meta and set backup size
        $tag = "$size-orig";
        
        if( ! isset( $meta['sizes'][$size] ) ) {
            $backup_sizes[$tag] = array(
                "file" => basename( $meta["file"] ),
                "width" => $meta["width"],
                "height" => $meta["height"]
            );
        } else {
            $backup_sizes[$tag] = $meta['sizes'][$size];
        }
    }
    
    $meta["sizes"][$size] = array(
        "file" => $new_filename,
        "width" => $saved["width"],
        "height" => $saved["height"]
    );
    
    
    if( $size == "full" && adverts_request( "apply_to_all" ) == "1" ) {
        $save_path = $dirname;
        $new_file = $new_path;
        
        $sizes = adverts_config( "gallery.image_sizes" );
        $size_keys = array_keys( $sizes );

        foreach( $size_keys as $size_key ) {
            
            // 1. IF exists delete backup file
            // 2. MOVE size to backup_size
            // 3. generate new size
            // 4. save new size
            
            if( ! isset( $backup_sizes[$size_key . '-orig'] ) ) {
                $backup_sizes[$size_key . '-orig'] = $meta["sizes"][$size_key] ;
            }
            
            if( isset( $meta["sizes"][$size_key] ) ) {
                wp_delete_file( $meta["sizes"][$size_key]["file"]);
            }
            
            $cs = $sizes[$size_key];
            $interm_file_name = sprintf( "%s-%dx%d-e%s.png", $file_name, $cs["width"], $cs["height"], $suffix );

            $image = wp_get_image_editor( $new_file );
            $image->resize($cs["width"], $cs["height"], $cs["crop"]);
            
            $file = $image->save( dirname( $new_file ) . "/" . $interm_file_name );
            
            $meta["sizes"][$size_key] = array(
                "file" => $file["file"],
                "width" => $file["width"],
                "height" => $file["height"],
                "mime-type" => $file["mime-type"]
            );
        }

    }

    wp_update_attachment_metadata( $attach_id, $meta );
    update_post_meta( $attach_id, '_wp_attachment_backup_sizes', $backup_sizes);
    
    $return->result = 1;
    $return->file = adverts_upload_item_data( $attach_id );
    echo json_encode( $return );
            
    exit;
}

/**
 * Saves video cover
 * 
 * This function is executed by the Gallery, after clicking "Capture ..." and
 * then "Save Thumbnail" button.
 * 
 * The function expects following params
 * - attach_id - ID of the (video) attachment for which the cover will be generated
 * - image - base64 encoded image data
 * 
 * @since 1.2
 * @return void
 */
function adverts_gallery_video_cover() {
    
    if( ! check_ajax_referer( 'adverts-gallery', '_ajax_nonce', false ) ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Invalid Session. Please refresh the page and try again.", "adverts" ) 
        ) );
        
        exit;
    }
    
    if( ! adverts_user_can_edit_image() ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "You cannot edit images.", "adverts" ) 
        ) );
        exit;
    }
    
    $attach_id = adverts_request("attach_id");
    $post_id = intval( adverts_request( "post_id" ) );
    $attach = get_post( $attach_id );
    
    if( $attach->post_parent != $post_id ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => __( "Incorrect Post or Attachment ID.", "adverts" ) 
        ) );
        exit;
    }
    
    $meta = wp_get_attachment_metadata( $attach_id );
    
    if(!is_array($meta)) {
        $meta = array();
    }
    if(!isset($meta["sizes"])) {
        $meta["sizes"] = array();
    }
    
    $attached_file = get_attached_file( $attach_id );
    $save_path = pathinfo( $attached_file, PATHINFO_DIRNAME );
    $file_name = pathinfo( $attached_file, PATHINFO_FILENAME );
    
    $img = adverts_request( "image" );
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $bits = base64_decode($img);
    
    $new_file_name = $file_name . "-full.png";
    $new_file = $save_path . "/" . $new_file_name;
    
    $ifp = @ fopen( $new_file, 'wb' );
    if ( ! $ifp ) {
        echo json_encode( array( 
            "result" => 0, 
            "error" => sprintf( __( 'Could not write file %s' ), $new_file_name )
        ) );
        
        exit;
    }

    @fwrite( $ifp, $bits );
    fclose( $ifp );
    clearstatcache();

    // Set correct file permissions
    $stat = @ stat( dirname( $new_file ) );
    $perms = $stat['mode'] & 0007777;
    $perms = $perms & 0000666;
    @ chmod( $new_file, $perms );
    clearstatcache();
    
    $image = wp_get_image_editor( $new_file );
    
    if( is_wp_error( $image ) ) {
        // file not readable, delete it
        wp_delete_file( $new_file );
        
        echo json_encode( array( 
            "result" => 0, 
            "error" => $image->get_error_message()
        ) );
        exit;
    }
    
    // Ok, file saved and readable by image editor
    // now delete current sizes and backups
    $sizes = adverts_config( "gallery.image_sizes" );
    $size_keys = array_keys( $sizes );
    $backup_sizes = get_post_meta( $attach_id, '_wp_attachment_backup_sizes', true );
    

    foreach( $size_keys as $size_key ) {
        if( isset( $meta["sizes"][$size_key] ) ) {
            //echo "[".$save_path . "/" . $meta["sizes"][$size_key]["file"]."]";
            wp_delete_file( $save_path . "/" . $meta["sizes"][$size_key]["file"] );
            unset( $meta["sizes"][$size_key] );
        }
        
        if( isset( $backup_sizes[$size_key] ) ) {
            wp_delete_file( $save_path . "/" . $backup_sizes[$size_key]["file"] );
            unset( $backup_sizes[$size_key] );
        }
    }

    if( is_array( $backup_sizes ) ) {
        update_post_meta( $attach_id, '_wp_attachment_backup_sizes', $backup_sizes);
    }
    
    // Register new full size image
    $meta["sizes"]["full"] = array(
        "file" => $new_file_name,
        "width" => intval( adverts_request( "width" ) ),
        "height" => intval( adverts_request( "height" ) ),
        "mime-type" => "image/png"
    );

    foreach( $sizes as $size_key => $size ) {
        $interm_file_name = sprintf( "%s-%dx%d.png", $file_name, $size["width"], $size["height"] );

        $image = wp_get_image_editor( $new_file );
        $image->resize($size["width"], $size["height"], $size["crop"]);
        $file = $image->save( dirname( $new_file ) . "/" . $interm_file_name );

        if( is_wp_error( $file ) ) {
            echo json_encode( array( 
                "result" => 0, 
                "error" => $file->get_error_message()
            ) );
            exit;
        }
        
        $meta["sizes"][$size_key] = array(
            "file" => $file["file"],
            "width" => $file["width"],
            "height" => $file["height"],
            "mime-type" => $file["mime-type"]
        );
    }
    
    wp_update_attachment_metadata( $attach_id, $meta );
    
    echo json_encode( array( 
        "result" => 1, 
        "file" => adverts_upload_item_data( $attach_id )
    ) );
    exit;
}

/**
 * Returns ad contact information (email and phone)
 * 
 * This action is executed onclick in the frontend on advert details page.
 * 
 * No Priv
 * Action: adverts_show_contact
 * 
 * @since 0.1
 * @return void
 */
function adverts_show_contact() {
    
    // @todo check_ajax_referer( 'my-special-string', 'security' );
    
    $id = adverts_request("id");
    $post = get_post($id);
    
    if( $post === null || $post->post_type != 'advert') {
        echo json_encode( array( 
            'result' => 0,
            'error' => __("Post with given ID does not exist.", "adverts")
        ));
        exit;
    } else {
        echo json_encode( array(
            'result' => 1,
            'email' => get_post_meta( $id, 'adverts_email', true ),
            'phone' => get_post_meta( $id, 'adverts_phone', true )
        ));
    }
    
    exit;
}

/**
 * Deletes temporary post
 * 
 * This action is executed when user leaves post an ad form in the frontend
 * 
 * No Priv
 * Action: adverts_delete_tmp
 * 
 * @since 0.1
 * @return void
 */
function adverts_delete_tmp() {
    
    // @todo check_ajax_referer( 'my-special-string', 'security' );
    
    $id = adverts_request("id");
    $post = get_post($id);
    
    if( $post === null || $post->post_status != adverts_tmp_post_status()) {
        echo json_encode( array( 
            'result' => 0,
            'error' => __("Post with given ID does not exist.", "adverts")
        ));
        exit;
    }
    
    $param = array( 'post_parent' => $id, 'post_type' => 'attachment' );
    $children = get_posts( $param );
    
    if( is_array( $children ) ) {
        foreach( $children as $attch) {
            adverts_delete_post( $attch->ID );
        }
    } 
    
    adverts_delete_post( $id );
    
    echo json_encode( array(
        'result' => 1
    ));
    
    exit;
}

/**
 * Delete Advert
 * 
 * This action is executed by ad poster in the frontend (from the [adverts_manage] shortcode).
 * 
 * No Priv
 * Action: adverts_delete
 * 
 * @since 0.1
 * @since 1.1.0     Support dor deleting Ads using JavaScript
 * @return void
 */
function adverts_delete() {
    
    $id = adverts_request("id");
    $is_ajax = adverts_request( "ajax", false );
    $post = get_post($id);
    $result = null;
    
    // Check AJAX referer
    if( ! check_ajax_referer( 'adverts-delete', '_ajax_nonce', false ) ) {
        $result = array( 
            "result" => 0, 
            "error" => __( "Invalid Session. Please refresh the page and try again.", "adverts" ) 
        );
    }
    
    // check if post exists
    if( !$post ) {
        $result = array( 
            "result" => -1, 
            "error" => __( "Post you are trying to delete does not exist.", "adverts" ) 
        );
    }
    
    // check if current user is post author
    if( $post->post_author != get_current_user_id() ) {
        $result = array( 
            "result" => -2, 
            "error" => __( "Post you are trying to delete does not belong to you.", "adverts" ) 
        );
    }
    
    // check if post is an advert
    if( $post->post_type != 'advert') {
        $result = array( 
            "result" => -3, 
            "error" => __( "This post is not an Advert.", "adverts" ) 
        );
    }
    
    if( ! $result === null ) {
        if( $is_ajax ) {
            echo json_encode( $result );
        } else {
            wp_die( $result["error"] );
        }
    }
    
    $post_title = $post->post_title;
    $param = array( 'post_parent' => $id, 'post_type' => 'attachment' );
    $children = get_posts( $param );
    
    // also delete all uploaded files
    if( is_array( $children ) ) {
        foreach( $children as $attch) {
            adverts_delete_post( $attch->ID);
        }
    } 
    
    adverts_delete_post( $id );
    
    if(adverts_request("redirect_to") ) {
        wp_redirect( adverts_request( "redirect_to" ) );
    } else if( $is_ajax ) {
        echo json_encode( array( 
            "result" => 1, 
            "message" => sprintf( __( "Advert <strong>%s</strong> deleted.", "adverts" ), $post_title )
        ) );
    }
    
    exit;
}