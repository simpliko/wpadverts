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
    
    add_filter( "adverts_gallery_upload_prefilter", "adverts_file_is_image");

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
            wp_delete_attachment( $attch->ID, true );
        }
    } 
    
    wp_delete_post( $id, true );
    
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
 * @return void
 */
function adverts_delete() {
    
    $id = adverts_request("id");
    $post = get_post($id);
    
    // check if post exists
    if( !$post ) {
        echo 1;
        exit;
    }
    
    // check if current user is post author
    if( $post->post_author != get_current_user_id() ) {
        echo 2;
        exit;
    }
    
    // check if post is an advert
    if( $post->post_type != 'advert') {
        echo 3;
        exit;
    }
    
    $param = array( 'post_parent' => $id, 'post_type' => 'attachment' );
    $children = get_posts( $param );
    
    // also delete all uploaded files
    if( is_array( $children ) ) {
        foreach( $children as $attch) {
            wp_delete_attachment( $attch->ID, true );
        }
    } 
    
    wp_delete_post( $id, true );
    
    if(adverts_request("redirect_to") ) {
        wp_redirect( adverts_request( "redirect_to" ) );
    }
    
    exit;
}