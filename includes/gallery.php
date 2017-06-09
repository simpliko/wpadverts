<?php
/**
 * Gallery Functions
 * 
 * @package     Adverts
 * @copyright   Copyright (c) 2015, Grzegorz Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Renders Gallery Input
 * 
 * Renders gallery drop file input and uploaded items.
 * 
 * @param WP_Post $post
 * @param array $conf
 * @since 0.1
 * @return void
 */
function adverts_gallery_content( $post = null, $conf = array() ) {
    
    wp_nonce_field( plugin_basename( __FILE__ ), 'adverts_gallery_content_nonce' ); 
    
    $conf = shortcode_atts( array(
        "button_class" => "button-secondary",
        "post_id_input" => "#post_ID"
    ), $conf);
    
    $init = array(
        'runtimes'            => 'html5,silverlight,flash,html4',
        'browse_button'       => 'adverts-plupload-browse-button',
        'container'           => 'adverts-plupload-upload-ui',
        'drop_element'        => 'adverts-drag-drop-area',
        'file_data_name'      => 'async-upload',            
        'multiple_queues'     => true,
        'max_file_size'       => wp_max_upload_size().'b',
        'url'                 => admin_url('admin-ajax.php'),
        'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
        'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
        'multipart'           => true,
        'urlstream_upload'    => true,

        // additional post data to send to our ajax hook
        'multipart_params'    => array(
          '_ajax_nonce' => wp_create_nonce('adverts-gallery'),
          'action'      => 'adverts_gallery_upload',            // the ajax action name
          
        ),
    );
    
    
    ?>

    <?php if( is_admin() ): ?>
    <div id="adverts-plupload-upload-ui">
        <div id="adverts-drag-drop-area">
        
        </div>
        <div class="adverts-gallery">
            <p class="adverts-gallery-invite"><?php _e( "Drop <strong>images</strong> here to add them.", "adverts" ) ?></p>
            <p class="adverts-gallery-button-wrap"><a href="#" id="adverts-plupload-browse-button" class="button-secondary"><?php _e( "browse files ...", "adverts" ) ?></a></p>
        </div>
        <div class="adverts-gallery-uploads">

        </div>
    </div>
    
    
    <div id="adverts-modal-gallery" class="adverts-modal">
        <div class="media-modal wp-core-ui">
            <a class="media-modal-close adverts-upload-modal-close" href="#" title="<?php _e( "Close", "adverts" ) ?>"><span class="media-modal-icon"></span></a>
            <div class="media-modal-content">
                <div class="media-frame wp-core-ui2 hide-menu">

                    <div class="media-frame-title"><h1><?php _e( "Edit Image", "adverts" ) ?></h1></div>
                    
                    <div class="media-frame-router">
                        <div class="media-router">
                            <a href="#" class="media-menu-item active"><?php _e( "Image Properties", "adverts" ) ?></a>
                        </div>
                    </div>

                    <div class="media-frame-content"> 

                        <div class="wrap" style="padding:0px 20px">

                            <form method="post" action="">

                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><label for="adverts_caption"><?php _e( "Title", "adverts" ) ?></label></th>
                                            <td>
                                                <input name="adverts_caption" type="text" id="adverts_caption" value="" class="regular-text" />
                                            </td>
                                        </tr>               
                                        <tr>
                                            <th scope="row"><label for="adverts_featured"><?php _e( "Featured", "adverts" ) ?></label></th>
                                            <td>
                                                <label for="adverts_featured">
                                                    <input name="adverts_featured" type="checkbox" id="adverts_featured" value="1" />
                                                    <?php esc_attr_e( "Use this image as main image", "adverts") ?>
                                                </label>
                                            </td>
                                        </tr>                
                                        <tr>
                                            <th scope="row"><label for="adverts_content"><?php _e( "Description", "adverts" ) ?></label></th>
                                            <td>
                                                <textarea name="adverts_content" rows="10" cols="50" id="adverts_content" class="large-text code"></textarea>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>

                            </form>

                        </div>
                    
                    </div>
                    
                    <div class="media-frame-toolbar">
                        <div class="media-toolbar">
                            <div class="media-toolbar-secondary"></div>
                            <div class="media-toolbar-primary">
                                <span class="adverts-spinner adverts-loader adverts-icon-spinner animate-spin"></span>
                                <a href="#" class="button media-button button-secondary button-large media-button-select adverts-upload-modal-close"><?php _e( "Cancel", "adverts" ) ?></a>
                                <a href="#" class="button media-button button-primary button-large media-button-select adverts-upload-modal-update"><?php _e( "Update and Close", "adverts" ) ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="media-modal-backdrop"></div>
    </div><!-- .adverts-modal -->
    <?php else: ?>

    <div id="adverts-plupload-upload-ui">
        <div id="adverts-drag-drop-area">
        
        </div>
        <div class="adverts-gallery">
            <p><?php _e( "Drop <strong>images</strong> here to add them.", "adverts" ) ?></p>
            <p><a href="#" id="adverts-plupload-browse-button" class="adverts-button"><?php _e( "browse files ...", "adverts" ) ?></a></p>
        </div>
        <div class="adverts-gallery-uploads">

        </div>

    </div>
    
    <?php add_action("wp_footer", "adverts_gallery_modal") ?>
    <?php endif; ?>
    
    
    
    <?php
        // Get data for uploaded items and format it as JSON.
        $data = array();
        if($post) {
        
            $children = get_children( array(
                'post_parent' => $post->ID,
                'post_type'   => 'attachment', 
                'posts_per_page' => -1,
                'post_status' => 'inherit' 
            ) );

            // adverts_sort_images() is defined in functions.php
            require_once ADVERTS_PATH . "/includes/functions.php"; 
            $children = adverts_sort_images($children, $post->ID);

            foreach($children as $child) {
                $data[] = adverts_upload_item_data( $child->ID );
            }
        }

    ?>
    
    
    <script type="text/javascript">
    var ADVERTS_PLUPLOAD_INIT = <?php echo json_encode($init) ?>;
    var ADVERTS_PLUPLOAD_DATA = <?php echo json_encode($data) ?>;
    var ADVERTS_PLUPLOAD_CONF = <?php echo json_encode($conf) ?>;
    </script>
    <?php
}

/**
 * HTML for gallery modal window
 * 
 * This function is executed by wp_footer action, it renders HTML for modal window
 * which allows to customize uploaded image title, description and is featured flag.
 * 
 * @since 0.1
 * @return void
 */
function adverts_gallery_modal() {
    ?>
    
    <div id="adverts-modal-gallery" class="adverts-modal adverts-modal-reposition">
        <div class="adverts-modal-inner">
            <span style="font-size:1.3em; font-weight: bold; display: block; padding: 0 0 15px 0"><?php _e("Image Properties", "adverts") ?></span>
            <br/>
            <form action="" method="post" class="adverts-form adverts-form-aligned">
                <fieldset>
                    
                    <div class="adverts-control-group">
                        <label for="adverts_caption" style="float:none"><?php _e("Title", "adverts") ?></label>
                        <input type="text" id="adverts_caption" name="adverts_caption" value="" />
                    </div>
                    
                    <div class="adverts-control-group">
                        <label for="adverts_featured" style="float:none"><?php _e("Featured", "adverts") ?></label>
                        <input type="checkbox" id="adverts_featured" name="adverts_featured" value="1" />
                        <?php esc_html_e( "Use this image as main image", "adverts") ?>
                    </div>
                    
                    <div class="adverts-control-group">
                        <label for="adverts_content" style="float:none"><?php _e("Description", "adverts") ?></label>
                        <textarea id="adverts_content" name="adverts_content"></textarea>
                    </div>
                    
                </fieldset>
            </form>
            
            <div>
                <a href="#" class="adverts-button adverts-upload-modal-close"><?php _e( "Cancel" ) ?></a>
                <a href="#" class="adverts-button adverts-upload-modal-update"><?php _e( "Update and Close" ) ?></a>
                <span class="adverts-loader adverts-icon-spinner animate-spin"></span>
            </div>

        </div>
        
    </div>
    <?php
}

/**
 * Formats information about specific attachment
 * 
 * @param int $attach_id WP_Post ID
 * @param boolean $is_new
 * @return array
 */
function adverts_upload_item_data( $attach_id, $is_new = false ) {

    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    // Generate the metadata for the attachment, and update the database record.

    $thumb = wp_get_attachment_image_src( $attach_id, "adverts-upload-thumbnail");
    
    $featured = 0;
    $caption = "";
    $content = "";
    
    if( !$is_new ) {
        $post = get_post( $attach_id );
        $parent_id = wp_get_post_parent_id( $post->ID );
        $caption = $post->post_excerpt;
        $content = $post->post_content;
        
        $featured = intval(get_post_meta( $parent_id, '_thumbnail_id', true ) );
        if($featured == $post->ID) {
            $featured = 1;
        } else {
            $featured = 0;
        }
    }
    
    $data = array(
        "post_id" => $post->post_parent,
        "attach_id" => $attach_id,
        "featured" => $featured,
        "caption" => $caption,
        "content" => $content,
        "sizes" => array(
            "normal" => array(
                "url" => wp_get_attachment_url( $attach_id )
            ),
            "adverts_upload_thumbnail" => array(
                "url" => $thumb[0]
            )
        )
    );
    
    return $data;
}
