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
    
    $field_name = "gallery";
    
    $conf = shortcode_atts( array(
        "button_class" => "button-secondary",
        "post_id_input" => "#post_ID"
    ), $conf);
    
    $init = array(
        'runtimes'            => 'html5,silverlight,flash,html4',
        'browse_button'       => 'adverts-plupload-browse-button-'.$field_name,
        'container'           => 'adverts-plupload-upload-ui-'.$field_name,
        'drop_element'        => 'adverts-drag-drop-area-'.$field_name,
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
            'form'        => 'adverts_add',
            'form_scheme' => '',
            'field_name'       => $field_name
          
        ),
    );
    wp_enqueue_script( 'image-edit' );
    wp_enqueue_style( 'adverts-upload' );
    
    ?>



    <div id="<?php echo esc_html("adverts-plupload-upload-ui-".$field_name) ?>" class="adverts-plupload-upload-ui">
        <div id="<?php echo esc_html("adverts-drag-drop-area-".$field_name) ?>"class="adverts-drag-drop-area">
        
        </div>
        <div class="adverts-gallery">
            <p><?php _e( "Drop <strong>images</strong> here to add them.", "adverts" ) ?></p>
            <p><a href="#" id="<?php echo esc_html("adverts-plupload-browse-button-".$field_name) ?>" class="adverts-plupload-browse-button adverts-button"><?php _e( "browse files ...", "adverts" ) ?></a></p>
        </div>
        <div class="adverts-gallery-uploads">

        </div>

    </div>
    
    <?php add_action("wp_footer", "adverts_gallery_modal") ?>
    <?php add_action("admin_footer", "adverts_gallery_modal") ?>
    
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

        $upload_conf = array(
            "init" => $init,
            "data" => $data,
            "conf" => $conf
        );
        
    ?>
    
    
    <script type="text/javascript">
    if(typeof ADVERTS_PLUPLOAD_DATA === "undefined") {
        var ADVERTS_PLUPLOAD_DATA = [];
    }
    ADVERTS_PLUPLOAD_DATA.push(<?php echo json_encode($upload_conf) ?>);
    
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
    

    <script type="text/html" id="tmpl-wpadverts-browser">
    <div  class="wpadverts-overlay wpadverts-overlay-dark" style="display: block;">

        <div class="wpadverts-overlay-body">
             
            <div class="wpadverts-overlay-header">
                <div class="wpadverts-overlay-title">
                    <?php _e( "Attachment Details", "adverts" ) ?>
                </div>
                <div class="wpadverts-overlay-buttons"><!-- no line break 
                    --><span class="wpadverts-overlay-button wpadverts-file-pagi-prev adverts-icon-left-open wpadverts-navi-disabled"></span><!-- no line break
                    --><span class="wpadverts-overlay-button wpadverts-file-pagi-next adverts-icon-right-open wpadverts-navi-disabled"></span><!-- no line break
                    --><a href="#" class="wpadverts-overlay-button wpadverts-overlay-close adverts-icon-cancel" title="Close"></a>
                </div>
            </div>
            
            <div class="wpadverts-attachment-details">
                
            </div>
        </div>
    </div>
    </script>
    
    <script type="text/html" id="tmpl-wpadverts-uploaded-file">
        <# if(data.result === null) { #>
        <div class="adverts-gallery-upload-update adverts-icon-spinner animate-spin" style="position: absolute;"></div>
        <# } else if(typeof data.result.error != "undefined") { #>
        <div class="adverts-gallery-upload-update adverts-icon-attention">
            <span class="adverts-gallery-upload-failed">{{ data.result.error }}</span>
        </div>
        <# } else { #>
        <div class="adverts-loader adverts-gallery-upload-update adverts-icon-spinner animate-spin" style="position: absolute; display: none"></div>
        <# if( data.result.mime_type == "video/mp4" ) { #>
        <span class="adverts-icon adverts-icon-videocam" style="font-size: 80px;line-height: 105px;vertical-align: middle;display: block;width: 150px;height: 150px;text-align: center;opacity: 0.75;"></span>
        <# } else { #>
        <img src="{{ data.result.sizes.adverts_upload_thumbnail.url }}" alt="" class="adverts-gallery-upload-item-img" />
        <# } #>
        
        <# if(data.result.featured) { #>
        <span class="adverts-gallery-item-featured" style="display: block"><?php _e("Main", "adverts") ?></span>
        <# } #>

        <p class="adverts-gallery-upload-actions">
            <a href="#" class="adverts-button-edit adverts-button adverts-button-icon adverts-icon-pencil" title="<?php _e("Edit File", "adverts") ?>"></a>
            <a href="#" class="adverts-button-remove adverts-button adverts-button-icon adverts-icon-trash-1" title="<?php _e("Delete File", "adverts") ?>"></a>
        </p>
        <# } #>
    </script>
    
    <script type="text/html" id="tmpl-wpadverts-browser-attachment-view">
        <div class="wpadverts-attachment-media-view wpadverts-overlay-content">
            <# if( data.file.mime_type == "video/mp4" ) { #>
            <div class="wpadverts-attachment-image">
                <video src="{{ data.file.sizes.normal.url }}" controls="controls"></video>
                <div style="margin-top: 12px"><a href="#" class="adverts-button">Select Thumbnail</a></div>
            </div>
            <# } else { #>
            <div class="wpadverts-attachment-image">
                <# for(var size in data.file.sizes) { #>
                <img src="{{ data.file.sizes[size].url }}" alt="" style="max-width: 100%; max-height: 100%;">
                <# } #>
                <div style="margin-top: 12px; margin-bottom: 12px">
                    <span>Image Size</span>
                    <select class="wpadverts-image-sizes" style="background: white;border: 1px solid silver;height: 37px;line-height: 37px;width: 150px;">
                        <option value="normal">Normal</option>
                        <option value="adverts-upload-thumbnail" data-width="150" data-height="105" data-ratio="<?php echo round(150/105,2) ?>">Thumbnail</option>
                        <option value="adverts-list" data-width="310" data-height="190" data-ratio="<?php echo round(310/190,2) ?>">List</option>
                        <option selected="selected" value="adverts-single" data-width="650" data-height="350" data-ratio="<?php echo round(650/350,2) ?>">Single</option>
                    </select>
                    <a href="#" class="adverts-button wpadverts-attachment-edit-image">Edit Image</a>
                </div>
            </div>
            <# } #>
        </div>

        <div class="wpadverts-attachment-info">
            <form action="" method="post" class="adverts-form adverts-form-aligned">
                <fieldset>
                    <div class="adverts-control-group">
                        <label for="adverts_featured" style="float:none"><?php _e("Featured", "adverts") ?></label>
                        <input type="checkbox" id="adverts_featured" name="adverts_featured" value="1" <# if(data.file.featured) { #>checked="checked"<# } #> />
                        <?php esc_html_e( "Use this image as main image", "adverts") ?>
                    </div>

                    <div class="adverts-control-group">
                        <label for="adverts_caption" style="float:none"><?php _e("Title", "adverts") ?></label>
                        <input type="text" id="adverts_caption" name="adverts_caption" value="{{ data.file.caption }}" />
                    </div>

                    <div class="adverts-control-group">
                        <label for="adverts_content" style="float:none"><?php _e("Description", "adverts") ?></label>
                        <textarea id="adverts_content" name="adverts_content">{{ data.file.content }}</textarea>
                    </div>

                </fieldset>
            </form>

            <div>
                <a href="#" class="adverts-button adverts-upload-modal-update"><?php _e( "Update Description", "adverts" ) ?></a>
                <span class="adverts-loader adverts-icon-spinner animate-spin"></span>
            </div>

            <div class="details">
                <div class="filename"><strong>File name:</strong> {{ data.file.readable.name }}</div>
                <div class="filename"><strong>File type:</strong> {{ data.file.readable.type }}</div>
                <div class="uploaded"><strong>Uploaded on:</strong> {{ data.file.readable.uploaded }}</div>
                <div class="file-size"><strong>File size:</strong> {{ data.file.readable.size }}</div>
                <div class="dimensions"><strong>Dimensions:</strong> {{ data.file.readable.dimensions }}</div>
                <div class="formatted-length"><strong>Length:</strong> {{ data.file.readable.length }}</div>
                <div class="compat-meta"></div>
            </div>
        </div>
    </script>
    
    <script type="text/html" id="tmpl-wpadverts-browser-attachment-image">
        <div class="wpadverts-attachment-media-view wpadverts-overlay-content">
            
            <div class="wpadverts-attachment-image-toolbar" style="margin:12px 12px 12px 12px">
                
                <a href="#" class="adverts-image-action-crop adverts-button adverts-button-small"><span class="adverts-icon-crop"></span></a>
                <a href="#" class="adverts-image-action-rotate-cw adverts-button adverts-button-small"><span class="adverts-icon-cw"></span></a>
                <a href="#" class="adverts-image-action-rotate-ccw adverts-button adverts-button-small"><span class="adverts-icon-ccw"></span></a>
                <a href="#" class="adverts-image-action-flip-h adverts-button adverts-button-small"><span class="adverts-icon-resize-vertical"></span></a>
                <a href="#" class="adverts-image-action-flip-v adverts-button adverts-button-small"><span class="adverts-icon-resize-horizontal"></span></a>
                <a href="#" class="adverts-image-action-undo adverts-button adverts-button-small"><span class="adverts-icon-history"></span></a>
                
                <span style="margin: 0 1em 0 1em">
                    Dimensions 
                    <input type="number" class="adverts-image-scale-width" name="" value="{{ data.file.dimensions.width }}" max="{{ data.file.dimensions.width }}" step="1" style="width: 70px;height: 30px;box-sizing: border-box;border-radius: 1px;" />
                    x
                    <input type="number" class="adverts-image-scale-height" name=""  value="{{ data.file.dimensions.height }}" max="{{ data.file.dimensions.height }}" step="1" style="width: 70px;height: 30px;box-sizing: border-box;border-radius: 1px;" />
                    <a href="#" class="adverts-image-action-scale adverts-button adverts-button-small">Scale</a>
                </span>
                
                
                <span style="margin: 0 1em 0 1em">
                    <a href="#" class="adverts-button adverts-button-small">Save</a>
                </span>
            </div>

            <div class="wpadverts-attachment-image">
                <img src="<?php echo admin_url('admin-ajax.php') ?>?action=adverts_gallery_image_stream&attach_id={{ data.file.attach_id }}&history={{ data.history }}" id="wpadverts-image-crop" alt="" style="max-width: 100%; max-height: 100%;">
            </div>
            
        </div>

        <div class="wpadverts-attachment-info">

        </div>
    </script>
    <link rel="stylesheet" href="http://localhost/wpadverts/wp-includes/js/jcrop/jquery.Jcrop.min.css" type="text/css">
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
    $thumb_list = wp_get_attachment_image_src( $attach_id, "adverts-list");
    
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
        "mime_type" => $post->post_mime_type,
        "featured" => $featured,
        "caption" => $caption,
        "content" => $content,
        "sizes" => array(
            "normal" => array(
                "url" => wp_get_attachment_url( $attach_id )
            ),
            "adverts_upload_thumbnail" => array(
                "url" => $thumb[0]
            ),
            "adverts_list" => array(
                "url" => $thumb_list[0]
            )
        ),
        "readable" => array(
            "name" => basename( $post->guid ),
            "type" => $post->post_mime_type,
            "uploaded" => date_i18n( get_option( "date_format"), strtotime( $post->post_date_gmt ) ),
            "size" => size_format( filesize( get_attached_file( $attach_id ) ) ),
            "length" => null,
            "dimensions" => null
        )
    );
    
    $meta = wp_get_attachment_metadata( $attach_id );
    
    if( isset( $meta["width"] ) && isset( $meta["height"] ) ) {
        $data["readable"]["dimensions"] = sprintf( "%d x %d", $meta["width"], $meta["height"] );
        $data["dimensions"] = $meta;
    }
    if( isset( $meta["length_formatted"] ) ) {
        $data["readable"]["length"] = $meta["length_formatted"];
    }
    
    return $data;
}
