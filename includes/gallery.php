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
            
            echo "<pre>";
            //print_r(adverts_upload_item_data(349));
            //print_r(adverts_upload_item_data(351));
            //wp_delete_attachment($post_id);
            //print_r(get_attached_file( $child->ID )).PHP_EOL."\r\n";
            //print_r(get_attached_file($child->ID)).PHP_EOL;
;            //print_r($data);
            echo "</pre>";
        }

        $sizes = array();
        foreach( adverts_config( "gallery.image_sizes" ) as $size_key => $size ) {
            $sizes[ str_replace( "-", "_", $size_key ) ] = $size;
        }
        
        $upload_conf = array(
            "init" => $init,
            "data" => $data,
            "conf" => $conf,
            "sizes" => $sizes
        );
        
        
        
        
        
    ?>
    
    
    <script type="text/javascript">
    if(typeof ADVERTS_PLUPLOAD_DATA === "undefined") {
        var ADVERTS_PLUPLOAD_DATA = [];
    }
    ADVERTS_PLUPLOAD_DATA.push(<?php echo json_encode($upload_conf) ?>);
    
    if(typeof ADVERTS_IMAGE_SIZES === "undefined") {
        var ADVERTS_IMAGE_SIZES = <?php echo json_encode($sizes) ?>;
    }
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
            
            <# if( data.result.sizes.adverts_upload_thumbnail.url ) { #>
            <img src="{{ data.result.sizes.adverts_upload_thumbnail.url }}" alt="" class="adverts-gallery-upload-item-img" />
            <# } else if( data.mime == "video" ) { #>
            <span class="">
                <span class="{{ data.icon }}"></span>
                <span class="">{{ data.result.filename }}</span>
            </span>
            <# } else { #>
            <span class="adverts-gallery-upload-item-file">
                <span class="adverts-gallery-upload-item-file-icon {{ data.icon }}"></span>
                <span class="adverts-gallery-upload-item-file-name">{{ data.result.readable.name }}</span>
            </span>
            <# } #>

            <div class="adverts-gallery-item-features">
                <# if(data.result.featured) { #>
                <span class="adverts-gallery-item-feature adverts-icon-flag" title="<?php _e( "Featured", "adverts" ) ?>"></span>
                <# } #>
                
                <# if(data.mime == "video") { #>
                <span class="adverts-gallery-item-feature adverts-icon-videocam" title="<?php _e("Video", "adverts") ?>"></span>
                <# } #>
            </div>


            <div class="adverts-gallery-upload-actions">
                <a href="#" class="adverts-button-edit adverts-button adverts-button-icon adverts-icon-pencil" title="<?php _e("Edit File", "adverts") ?>"></a>
                <a href="#" class="adverts-button-remove adverts-button adverts-button-icon adverts-icon-trash-1" title="<?php _e("Delete File", "adverts") ?>"></a>
            </div>
        <# } #>
    </script>
    
    <script type="text/html" id="tmpl-wpadverts-browser-attachment-view">
        <div class="wpadverts-attachment-media-view wpadverts-overlay-content">
            <# if( data.mime == "video" ) { #>
            <div class="wpadverts-attachment-video">
                <div class="wpadverts-file-browser-video-player">
                    <video class="wpadverts-file-browser-video" src="{{ data.file.guid }}" controls style="object-fit: meet">
                        <source src="{{ data.file.guid }}">
                    </video>
                </div>
                <div class="wpadverts-file-browser-video-select-thumbnail">
                    <div class="wpadverts-file-browser-video-preview"></div>
                </div>
            </div>
            <# } #>

            <# if( data.mime == "video" || data.mime == "image" ) { #>
            <div class="wpadverts-attachment-image">
                <# for(var size in data.file.sizes) { #>
                <img src="{{ data.file.sizes[size].url }}" class="adverts-image-preview adverts-image-preview-{{ size }}" alt="" style="max-width: 100%; max-height: 100%;">
                <# } #>

            </div>
            <# } #>
            
            <# if(data.mime == "other") { #>
            <div class="wpadverts-attachment-other">
                <div class="" style="margin: 0 0 2em 0">
                    <span class="{{ data.icon }}" style="font-size: 128px; opacity: 0.35;"></span>
                </div>
                <div class="" style="margin: 0 0 2em 0">
                    <span >{{ data.file.readable.name }} </span>
                </div>
                <a href="{{ data.file.guid }}" class="adverts-button"><?php _e("Download File", "adverts") ?></a>
            </div>
            <# } #>
        </div>

        <div class="wpadverts-attachment-info">
            <form action="" method="post" class="adverts-form adverts-form-aligned">
                <fieldset>
                    <# if( data.mime == "video" || data.mime == "image" ) { #>
                    <div class="adverts-control-group">
                        <label for="adverts_featured" style="float:none"><?php _e("Featured", "adverts") ?></label>
                        <input type="checkbox" id="adverts_featured" name="adverts_featured" value="1" <# if(data.file.featured) { #>checked="checked"<# } #> />
                        <?php esc_html_e( "Use this image as main image", "adverts") ?>
                    </div>
                    <# } #>

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

            <# if( data.mime == "image" || data.mime == "video" ) { #>
            <div style="margin-top: 15px;padding-top: 15px;border-top: 1px solid #ddd;clear:both;overflow:hidden">
                <form action="" method="post" class="adverts-form adverts-form-aligned">
                    <fieldset>
                        <div class="adverts-control-group">
                            <label><?php _e("Preview", "adverts") ?></label>
                            <select class="wpadverts-image-sizes" style="background: white;border: 1px solid silver;width: 100%;">
                                <# if(data.mime == "video") { #>
                                <option value="video" data-explain="<?php _e("Scroll the video to a selected place and click 'Capture' button to create video cover.", "adverts") ?>"><?php echo __("Video", "adverts") ?></option>
                                <# } #>
                                <?php foreach( adverts_gallery_explain_size() as $key => $size): ?>
                                <option value="<?php echo esc_html(str_replace("-", "_", $key)) ?>" data-explain="<?php echo esc_attr($size["desc"]) ?>"><?php echo esc_html($size["title"]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="adverts-control-group" style="font-size: 12px; max-width: 100%; line-height: 18px; color: #666;">
                            <span class="adverts-icon-info-circled"></span>
                            <span class="adverts-icon-size-explain-desc">-</span>
                        </div>
                        <# if(data.mime == "image") { #>
                        <div class="adverts-control-group">
                            <a href="#" class="adverts-button wpadverts-attachment-edit-image"><?php _e("Edit Image", "adverts") ?></a>
                            <a href="#" class="adverts-button wpadverts-attachment-create-image"><?php _e("Create Image", "adverts") ?></a>
                        </div>
                        <# } else if(data.mime == "video") { #>
                        <div class="adverts-control-group wpadverts-file-browser-video-actions">
                            <div class="wpadverts-file-browser-video-player" style="margin-top: 12px">
                                <a href="#" class="wpadverts-file-browser-video-thumbnail adverts-button"><?php _e("Select Thumbnail", "adverts") ?></a>
                            </div>

                            <div class="wpadverts-file-browser-video-select-thumbnail">
                                <a href="#" class="wpadverts-file-browser-video-thumbnail-save adverts-button"><?php _e("Save Thumbnail", "adverts") ?></a>
                                <a href="#" class="wpadverts-file-browser-video-thumbnail-cancel adverts-button"><?php _e("Cancel", "adverts") ?></a>
                            </div>
                        </div>
                        <# } #>
                        
                    </fieldset>
                </form>
            </div>
            <# } #>
            
            <div class="details">
                <# if( data.file.readable.name ) { #>
                <div class="filename"><strong><?php _e("File name:") ?></strong> {{ data.file.readable.name }}</div>
                <# } #>
                
                <# if( data.file.readable.type ) { #>
                <div class="filename"><strong><?php _e("File type:") ?></strong> {{ data.file.readable.type }}</div>
                <# } #>
                
                <# if( data.file.readable.uploaded ) { #>
                <div class="uploaded"><strong><?php _e("Uploaded on:") ?></strong> {{ data.file.readable.uploaded }}</div>
                <# } #>
                
                <# if( data.file.readable.size ) { #>
                <div class="file-size"><strong><?php _e("File size:") ?></strong> {{ data.file.readable.size }}</div>
                <# } #>
                
                <# if( data.file.readable.dimensions ) { #>
                <div class="dimensions"><strong><?php _e("Dimensions:") ?></strong> {{ data.file.readable.dimensions }}</div>
                <# } #>
                
                <# if( data.file.readable.length ) { #>
                <div class="formatted-length"><strong><?php _e("Length:") ?></strong> {{ data.file.readable.length }}</div>
                <# } #>
                
                <div class="compat-meta"></div>
            </div>
        </div>
    </script>
    
    <script type="text/html" id="tmpl-wpadverts-browser-attachment-image">
        <div class="wpadverts-attachment-media-view wpadverts-overlay-content">
            <div class="wpadverts-attachment-image">
                <img src="#" data-src="<?php echo admin_url('admin-ajax.php') ?>?action=adverts_gallery_image_stream&attach_id={{ data.file.attach_id }}&size={{ data.size }}&history={{ data.history }}&rand={{ data.rand }}" id="wpadverts-image-crop" alt="" style="max-width: 100%; max-height: 100%;">
            </div>
        </div>

        <div class="wpadverts-attachment-info">
            
            <form action="" method="post" class="adverts-form adverts-form-aligned">
                <fieldset>
                    <div class="adverts-control-group">
                        <label for="adverts_featured" style="float:none"><?php _e("Image Manipulation", "adverts") ?></label>
                        <a href="#" class="adverts-image-action-crop adverts-button adverts-button-small"><span class="adverts-icon-crop"></span></a>
                        <a href="#" class="adverts-image-action-rotate-cw adverts-button adverts-button-small"><span class="adverts-icon-cw"></span></a>
                        <a href="#" class="adverts-image-action-rotate-ccw adverts-button adverts-button-small"><span class="adverts-icon-ccw"></span></a>
                        <a href="#" class="adverts-image-action-flip-h adverts-button adverts-button-small"><span class="adverts-icon-resize-vertical"></span></a>
                        <a href="#" class="adverts-image-action-flip-v adverts-button adverts-button-small"><span class="adverts-icon-resize-horizontal"></span></a>
                        <a href="#" class="adverts-image-action-undo adverts-button adverts-button-small"><span class="adverts-icon-history"></span></a>

                    </div>

                    <div class="adverts-control-group">
                        <label for="adverts_caption" style="float:none"><?php _e("Image Size", "adverts") ?></label>
                        <input type="number" class="adverts-image-scale-width" name="" value="{{ data.dim[0] }}" max="{{ data.dim[0] }}" step="1" style="width: 70px;height: 30px;box-sizing: border-box;border-radius: 1px;" />
                        x
                        <input type="number" class="adverts-image-scale-height" name=""  value="{{ data.dim[1] }}" max="{{ data.dim[1] }}" step="1" style="width: 70px;height: 30px;box-sizing: border-box;border-radius: 1px;" />
                        <a href="#" class="adverts-image-action-scale adverts-button adverts-button-small"><?php _e("Scale", "adverts") ?></a>
                    </div>

                    <div class="adverts-control-group">
                        <label for="adverts_content" style="float:none"><?php _e("Save", "adverts") ?></label>
                    
                        <a href="#" class="adverts-image-action-save adverts-button adverts-button-small"><?php _e("Save", "adverts") ?></a>
                        <a href="#" class="adverts-image-action-cancel adverts-button adverts-button-small"><?php _e("Cancel", "adverts") ?></a>
                        |
                        <a href="#" class="adverts-image-action-restore adverts-button adverts-button-small"><?php _e("Restore", "adverts") ?></a>

                        <div>
                            <input type="checkbox" />
                            <?php _e( "Apply changes to all image sizes", "adverts") ?>

                        </div>
                    </div>
                    

                </fieldset>
            </form>
            
            <div class="details">
    
                <div class="filename"><strong><?php _e("Original size:") ?></strong> <span class="adverts-image-prop-original-size">-</span></div>
                <div class="filename"><strong><?php _e("Current size:") ?></strong> <span class="adverts-image-prop-current-size">-</span></div>
                <# if(data.recommended !== null ) { #>
                <div class="filename"><strong><?php _e("Recommended size:") ?></strong> <span class="adverts-image-prop-recommended-size"> {{ data.recommended.width }} x {{ data.recommended.height }}</span></div>
                <# } #>
                <div class="filename"><strong><?php _e("Zoom:") ?></strong> <span class="adverts-image-prop-zoom">100%</span></div>
                <div class="wpadverts-image-selection"><strong><?php _e("Selection:") ?></strong> <span class="adverts-image-prop-selection">-</span></div>
                
            </div>
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
    $sizes = array();
    $image_keys = array( "url", "width", "height", "is_intermidiate" );

    
    $image_defaults = array( 
        "full" => array(
            "enabled" => 1,
            "width" => null,
            "height" => null,
            "crop" => false
        )
    );

    $image_sizes = array_merge( $image_defaults, adverts_config( "gallery.image_sizes" ) );

    foreach( $image_sizes as $image_key => $image_size ) {
        if( $image_key !== "full" &&  ! has_image_size( $image_key ) ) {
            continue;
        }
        
        $src = wp_get_attachment_image_src( $attach_id, $image_key );
        
        if( $src === false ) {
            $src = array( 
                "url" => null, 
                "width" => $image_size["width"],
                "height" => $image_size["height"],
                "crop" => $image_size["crop"]
            );
        } else {
            $src = array_combine( $image_keys, $src );
        }
        
        $sizes[ str_replace( "-", "_", $image_key ) ] = $src;
    }
    
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
        "guid" => $post->guid,
        "mime_type" => $post->post_mime_type,
        "featured" => $featured,
        "caption" => $caption,
        "content" => $content,
        "sizes" => $sizes,
        "readable" => array(
            "name" => basename( $post->guid ),
            "type" => $post->post_mime_type,
            "uploaded" => date_i18n( get_option( "date_format"), strtotime( $post->post_date_gmt ) ),
            "size" => size_format( filesize( get_attached_file( $attach_id ) ) ),
            "length" => null
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

function adverts_gallery_explain_size( $size = null ) {
    
    $e = apply_filters( "adverts_gallery_explain", array( 
        "full" => array(
            "title" => __( "Gallery - Full Size", "adverts" ),
            "desc" => __( "Image in original size - used on classified details page in the gallery.", "adverts" )
        ),
        "adverts-gallery" => array(
            "title" => __( "Gallery - Slider", "adverts" ),
            "desc" => __( "Image resized to %d x %d - used in the images slider on classified details page.", "adverts" )
        ),
        "adverts-list" => array(
            "title" => __( "Classifieds List", "adverts" ),
            "desc" => __( "Image resized to %d x %d - used on the classifieds list.", "adverts" )
        ),
        "adverts-upload-thumbnail" => array(
            "title" => __( "Thumbnail", "adverts" ),
            "desc" => __( "Image resized to %d x %d - the image visible in upload preview.", "adverts" )
        ),
    ), $size );
    
    if( $size === null ) {
        return $e;
    }

    if( isset( $e["size"] ) ) {
        return $e["size"];
    }
}