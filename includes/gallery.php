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

    $button = "adverts-button";

    if(is_admin()) {
        $button = "button";
    }
    
    if( isset( $_POST["wpadverts-form-upload-uniqid"] ) ) {
        $uniqid = $_POST["wpadverts-form-upload-uniqid"];
    } else {
        $uniqid = null;
    }
    
    $conf = shortcode_atts( array(
        "button_class" => "button-secondary",
        "input_post_id" => "#post_ID",
        "input_post_id_nonce" => "#_post_id_nonce",
        "_wpadverts_checksum_nonce" => "",
        "_wpadverts_checksum" => "",
        "_post_id" => 0,
        "_post_id_nonce" => "",
        "field_name" => "gallery",
        "form_name" => "advert",
        "save" => array( "method" => "media-library", "supports" => array( "featured" ) ),
        "uniqid" => $uniqid
    ), $conf);

    $field_name = $conf["field_name"];
    $form_name = $conf["form_name"];
    
    $init = array(
        'runtimes'            => 'html5,silverlight,flash,html4',
        'browse_button'       => 'adverts-plupload-browse-button-'.$field_name,
        'container'           => 'adverts-plupload-upload-ui-'.$field_name,
        'drop_element'        => 'adverts-drag-drop-area-'.$field_name,
        'file_data_name'      => 'async-upload',            
        'multiple_queues'     => true,
        'max_file_size'       => '0',
        'url'                 => adverts_ajax_url(),
        'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
        'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
        'multipart'           => true,
        'urlstream_upload'    => true,
        
        // additional post data to send to our ajax hook
        'multipart_params'  => array(
            '_wpadverts_checksum_nonce'   => $conf["_wpadverts_checksum_nonce"],
            '_wpadverts_checksum' => $conf["_wpadverts_checksum"],
            'action'        => 'adverts_gallery_upload',
            'form'          => $form_name,
            'field_name'    => $field_name,
        ),
    );
    
    
    
    // Filters the default Plupload settings.
    $init = apply_filters( 'adverts_plupload_default_settings', $init );

    wp_enqueue_script( 'image-edit' );
    wp_enqueue_style( 'jcrop' );
    wp_enqueue_style( 'adverts-upload' );
    
    ?>



    <div id="<?php echo esc_html("adverts-plupload-upload-ui-".$field_name) ?>" class="adverts-plupload-upload-ui <?php echo is_admin() ? "wpadverts-browser-admin" : "adverts-browser-frontend" ?>">
        <div id="<?php echo esc_html("adverts-drag-drop-area-".$field_name) ?>"class="adverts-drag-drop-area">
        
        </div>
        <div class="adverts-gallery">
            <p><?php _e( "Drop files here to add them.", "wpadverts" ) ?></p>
            <p><a href="#" id="<?php echo esc_html("adverts-plupload-browse-button-".$field_name) ?>" class="adverts-plupload-browse-button <?php echo $button ?>"><?php _e( "browse files ...", "wpadverts" ) ?></a></p>
        </div>
        <div class="adverts-gallery-uploads">

        </div>

    </div>
    
    <?php add_action("wp_footer", "adverts_gallery_modal") ?>
    <?php add_action("admin_footer", "adverts_gallery_modal") ?>
    
    <?php
        // Get data for uploaded items and format it as JSON.
        $data = array();
        
        if( $post ) {
            $post_parent = $post->post_parent;
        } else {
            $post_parent = 0;
        }
        
        if( $post && ( ! isset( $conf["save"] ) || $conf["save"]["method"] == "media-library" ) ) {
        
            $att_search_meta = array(
                array( 'key' => 'wpadverts_form', 'value' => $form_name ),
                array( 'key' => 'wpadverts_form_field', 'value' => $field_name )
            );
            
            if( $form_name == "advert" && $field_name == "gallery" ) {
                $att_search_meta = array(
                    "relation" => "OR",
                    $att_search_meta,
                    array(
                        array( 'key' => 'wpadverts_form', 'compare' => 'NOT EXISTS' ),
                        array( 'key' => 'wpadverts_form_field', 'compare' => 'NOT EXISTS' )
                    )
                );
            }
            
            $att_search = array(
                'post_parent' => $post->ID,
                'post_type'   => 'attachment', 
                'posts_per_page' => -1,
                'post_status' => 'inherit',
                'meta_query' => $att_search_meta
            );

            $children = get_children( $att_search );
            // adverts_sort_images() is defined in functions.php
            require_once ADVERTS_PATH . "/includes/functions.php"; 
            $children = adverts_sort_images($children, $post->ID, $field_name);

            foreach($children as $child) {
                $data[] = adverts_upload_item_data( $child->ID );
            }

        }
        
        if( isset( $conf["save"] ) && $conf["save"]["method"] == "file" ) {
            
            $uniqid = adverts_request( "wpadverts-form-upload-uniqid" );
            $files = array();
            
            $field_tmp = array(
                "name" => $field_name,
                "save" => $conf["save"]
            );
            
            include_once ADVERTS_PATH . '/includes/class-upload-helper.php';

            $v = new Adverts_Upload_Helper;
            $v->set_field( $field_tmp );
            $v->set_form_name( $form_name );
            
            if( $uniqid ) {
                $v->set_uniquid( $uniqid );
                $path = $v->get_path();
                $uid = $v->get_uri();
                $files = glob( rtrim( $path, "/" ) . "/*" );
            } 
            
            if( $post && empty( $files ) ) {
                $v->set_post_id( $post->ID );
                $path = $v->get_path_dest();
                $uid = $v->get_uri_dest();
                $files = glob( rtrim( $path, "/" ) . "/*" );
            }
            
            if( ! is_array( $files ) ) {
                $files = array();
            }
            


            foreach( $files as $file ) {
                
                $type = wp_check_filetype( $file );
                
                $f = array(
                    "file" => $file,
                    "url" => rtrim( $uid, "/" ) . "/" . basename( $file ),
                    "type" => $type["type"]
                );
                
                $data[] = adverts_upload_file_data($f, $post, $uniqid );
            }
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
    
    $button = "adverts-button";
    
    if(is_admin()) {
        $button = "button";
    }
    
    ?>
    
    <script type="text/html" id="tmpl-wpadverts-uploaded-file">
        <# if(data.result === null) { #>
            <div class="adverts-gallery-upload-update adverts-icon-spinner animate-spin"></div>
        <# } else if(typeof data.result.error != "undefined") { #>
            <div class="adverts-gallery-upload-update adverts-icon-attention">
                <span class="adverts-gallery-upload-failed">{{ data.result.error }}</span>
            </div>
        <# } else { #>
            <div class="adverts-loader adverts-gallery-upload-update adverts-icon-spinner animate-spin" style="position: absolute; display: none"></div>
            
            <# if( typeof data.result.sizes.adverts_upload_thumbnail != "undefined" && data.result.sizes.adverts_upload_thumbnail.url !== null ) { #>
            <img src="{{ data.result.sizes.adverts_upload_thumbnail.url }}" alt="" class="adverts-gallery-upload-item-img" />
            <# } else if( data.mime == "video" ) { #>
            <span class="adverts-gallery-upload-item-file">
                <span class="adverts-gallery-upload-item-file-icon {{ data.icon }}"></span>
                <span class="adverts-gallery-upload-item-file-name">{{ data.result.readable.name }}</span>
            </span>
            <# } else { #>
            <span class="adverts-gallery-upload-item-file">
                <span class="adverts-gallery-upload-item-file-icon {{ data.icon }}"></span>
                <span class="adverts-gallery-upload-item-file-name">{{ data.result.readable.name }}</span>
            </span>
            <# } #>

            <div class="adverts-gallery-item-features">
                <# if(data.result.featured) { #>
                <span class="adverts-gallery-item-feature adverts-icon-flag" title="<?php _e( "Featured", "wpadverts" ) ?>"></span>
                <# } #>
                
                <# if(data.mime == "video") { #>
                <span class="adverts-gallery-item-feature adverts-icon-videocam" title="<?php _e("Video", "wpadverts") ?>"></span>
                <# } #>
            </div>


            <div class="adverts-gallery-upload-actions">
                <# if(data.conf.save.method == "file") { #>
                    <# if(data.mime == "image" ) { #>
                    <a href="{{ data.result.guid }}" target="_blank" class="adverts-button-view <?php echo $button ?> adverts-button-icon adverts-icon-eye" title="<?php _e("View", "wpadverts") ?>"></a>
                    <# } else { #>
                    <a href="{{ data.result.guid }}" target="_blank" class="adverts-button-download <?php echo $button ?> adverts-button-icon adverts-icon-download" title="<?php _e("Download", "wpadverts") ?>"></a>
                    <# } #>
                <# } else { #>
                <a href="#" class="adverts-button-edit <?php echo $button ?> adverts-button-icon adverts-icon-pencil" title="<?php _e("Edit File", "wpadverts") ?>"></a>
                <# } #>
                <a href="#" class="adverts-button-remove <?php echo $button ?> adverts-button-icon adverts-icon-trash-1" title="<?php _e("Delete File", "wpadverts") ?>"></a>
            </div>
        <# } #>
    </script>
    
    <script type="text/html" id="tmpl-wpadverts-browser">
    <div class="wpadverts-overlay wpadverts-overlay-dark <?php echo is_admin() ? "wpadverts-browser-admin" : "adverts-browser-frontend" ?>">

        <div class="wpadverts-overlay-body">
             
            <div class="wpadverts-overlay-header">
                <div class="wpadverts-overlay-title">
                    <?php _e( "Attachment Details", "wpadverts" ) ?>
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
    
    <script type="text/html" id="tmpl-wpadverts-browser-attachment-view">
        <div class="wpadverts-attachment-media-view wpadverts-overlay-content">
            <# if( data.mime == "video" ) { #>
                <div class="wpadverts-attachment-video">
                    <div class="wpadverts-file-browser-video-player">
                        <video class="wpadverts-file-browser-video" src="{{ data.file.guid }}" controls>
                            <source src="{{ data.file.guid }}">
                        </video>
                    </div>
                    <div class="wpadverts-file-browser-video-select-thumbnail">
                        <div class="wpadverts-file-browser-video-preview"></div>
                    </div>
                </div>
         
                <# for(var size in data.file.sizes) { #>
                    <div class="adverts-image-preview adverts-image-preview-{{ size }}">
                    <# if(data.file.sizes[size].url === null) { #>
                        <div class="adverts-image-missing">
                            <div class="wpadverts-attachment-icon-big-wrap">
                                <span class="wpadverts-attachment-icon-big {{ data.icon }}"></span>
                            </div>
                            <div class="wpadverts-attachment-icon-big-wrap">
                                <span>
                                    <strong><?php _e("This video does not have thumbnails yet. ") ?></strong>
                                </span>
                            </div>
                            <div class="wpadverts-attachment-icon-big-wrap">
                                <span><?php _e("In 'Preview' select 'Video' and click 'Capture ...' button to generate thumbnails.", "wpadverts") ?></strong>
                            </div>
                        </div>
                    <# } else { #>
                        <img src="{{ data.file.sizes[size].url }}?timestamp={{ data.timestamp }}" class="" alt="" />
                    <# } #>
                    </div>
                <# } #>
            
            
            <# } else if( data.mime == "image" ) { #>
                <# for(var size in data.file.sizes) { #>
                <div class="adverts-image-preview adverts-image-preview-{{ size }}"> 
                    <img src="{{ data.file.sizes[size].url }}?timestamp={{ data.timestamp }}" class="" alt="" />
                </div>
                <# } #>
            <# } else if( data.mime == "audio" ) { #>
            <div class="wpadverts-attachment-audio">
                <div class="wpadverts-attachment-icon-big-wrap">
                    <span class="wpadverts-attachment-icon-big {{ data.icon }}"></span>
                </div>
                <div class="wpadverts-attachment-icon-big-wrap">
                    <span >{{ data.file.readable.name }} </span>
                </div>
                <audio src="{{ data.file.guid }}"></audio>
            </div>
            <# } else if(data.mime == "other") { #>
            <div class="wpadverts-attachment-other">
                <div class="wpadverts-attachment-icon-big-wrap">
                    <span class="wpadverts-attachment-icon-big {{ data.icon }}"></span>
                </div>
                <div class="wpadverts-attachment-icon-big-wrap">
                    <span >{{ data.file.readable.name }} </span>
                </div>
                <a href="{{ data.file.guid }}" class="<?php echo $button ?>"><?php _e("Download File", "wpadverts") ?></a>
            </div>
            <# } #>
        </div>

        <div class="wpadverts-attachment-info">
            <form action="" method="post" class="adverts-form adverts-form-aligned">
                <fieldset>
                    <# if( data.can_feature && ( data.mime == "video" || data.mime == "image" ) ) { #>
                    <div class="adverts-control-group">
                        <label for="adverts_featured"><?php _e("Featured", "wpadverts") ?></label>
                        <input type="checkbox" id="adverts_featured" name="adverts_featured" value="1" <# if(data.file.featured) { #>checked="checked"<# } #> />
                        <?php esc_html_e( "Use this image as main image", "wpadverts") ?>
                    </div>
                    <# } #>

                    <div class="adverts-control-group">
                        <label for="adverts_caption"><?php _e("Title", "wpadverts") ?></label>
                        <input type="text" id="adverts_caption" name="adverts_caption" value="{{ data.file.caption }}" />
                    </div>

                    <div class="adverts-control-group">
                        <label for="adverts_content"><?php _e("Description", "wpadverts") ?></label>
                        <textarea id="adverts_content" name="adverts_content">{{ data.file.content }}</textarea>
                    </div>

                </fieldset>
            </form>

            <div>
                <a href="#" class="<?php echo $button ?> adverts-upload-modal-update"><?php _e( "Update Description", "wpadverts" ) ?></a>
                <span class="adverts-loader adverts-icon-spinner animate-spin"></span>
                <span class="adverts-update-description-success adverts-icon-ok"></span>
            </div>

            
            <# if( data.mime == "image" || data.mime == "video" ) { #>
            <div class="wpadverts-file-preview">
                <form action="" method="post" class="adverts-form adverts-form-aligned">
                    <fieldset>
                        <div class="adverts-control-group">
                            <label><?php _e("Preview", "wpadverts") ?></label>
                            <select class="wpadverts-image-sizes">
                                <# if(data.mime == "video") { #>
                                <option value="video" data-explain="<?php _e("Scroll the video to a selected place and click 'Capture' button to create video cover.", "wpadverts") ?>"><?php echo __("Video", "wpadverts") ?></option>
                                <# } #>
                                <?php foreach( adverts_gallery_explain_size() as $key => $size): ?>
                                <?php if($size["enabled"] == "1" && ( $key == "full" || has_image_size( $key ))): ?>
                                <option value="<?php echo esc_html(str_replace("-", "_", $key)) ?>" data-explain="<?php echo esc_attr( isset($size["desc_parsed"]) ? $size["desc_parsed"] : $size["desc"]) ?>"><?php echo esc_html($size["title"]) ?></option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="wpadverts-file-size-explain adverts-control-group">
                            <span class="adverts-icon-info-circled"></span>
                            <span class="adverts-icon-size-explain-desc">-</span>
                        </div>
                        
                        <?php if( adverts_user_can_edit_image() ): ?>
                        <div class="adverts-control-group wpadverts-file-browser-image-actions">
                            <a href="#" class="<?php echo $button ?> wpadverts-attachment-edit-image"><?php _e("Edit Image", "wpadverts") ?></a>
                            <a href="#" class="<?php echo $button ?> wpadverts-attachment-create-image" title="<?php _e("Create thumbnail from full size image.", "wpadverts") ?>"><?php _e("Create Image", "wpadverts") ?></a>
                        </div>
                        
                        <div class="adverts-control-group wpadverts-file-browser-video-actions">
                            <div class="wpadverts-file-browser-video-player">
                                <a href="#" class="wpadverts-file-browser-video-thumbnail <?php echo $button ?>"><?php _e("Capture ...", "wpadverts") ?></a>
                            </div>

                            <div class="wpadverts-file-browser-video-select-thumbnail">
                                <a href="#" class="wpadverts-file-browser-video-thumbnail-save <?php echo $button ?>"><?php _e("Save Thumbnail", "wpadverts") ?></a>
                                <a href="#" class="wpadverts-file-browser-video-thumbnail-cancel <?php echo $button ?>"><?php _e("Cancel", "wpadverts") ?></a>
                                <span class="adverts-file-video-spinner adverts-loader adverts-icon-spinner animate-spin"></span>
                            </div>
                            
                            
                        </div>
                        <?php endif; ?>
                        
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
    
    <?php if( adverts_user_can_edit_image() ): ?>
    <script type="text/html" id="tmpl-wpadverts-browser-attachment-image">
        <div class="wpadverts-attachment-media-view wpadverts-overlay-content wpadverts-attachment-media-image-editor">
            <div class="wpadverts-attachment-image">
                <img src="#" data-src="<?php echo adverts_ajax_url() ?>?action=adverts_gallery_image_stream&_wpadverts_checksum={{ data._wpadverts_checksum }}&_wpadverts_checksum_nonce={{ data._wpadverts_checksum_nonce }}&_post_id={{ data._post_id }}&_post_id_nonce={{ data._post_id_nonce }}&attach_id={{ data.file.attach_id }}&size={{ data.size }}&history={{ data.history }}&rand={{ data.rand }}&_ajax_nonce={{ data.nonce }}" id="wpadverts-image-crop" alt="" style="max-width: 100%; max-height: 100%;">
            </div>
        </div>

        <div class="wpadverts-attachment-info">
            
            <form action="" method="post" class="adverts-form adverts-form-aligned">
                <fieldset>
                    <div class="adverts-control-group">
                        <label for="adverts_featured"><?php _e("Image Manipulation", "wpadverts") ?></label>
                        <a href="#" class="adverts-image-action-crop <?php echo $button ?> adverts-button-small" title="<?php _e("Crop", "wpadverts") ?>"><span class="adverts-icon-crop"></span></a>
                        <a href="#" class="adverts-image-action-rotate-cw <?php echo $button ?> adverts-button-small" title="<?php _e("Rotate 90 degrees", "wpadverts") ?>"><span class="adverts-icon-cw"></span></a>
                        <a href="#" class="adverts-image-action-rotate-ccw <?php echo $button ?> adverts-button-small" title="<?php _e("Rotate -90 degrees", "wpadverts") ?>"><span class="adverts-icon-ccw"></span></a>
                        <a href="#" class="adverts-image-action-flip-h <?php echo $button ?> adverts-button-small" title="<?php _e("Flip Vertically", "wpadverts") ?>"><span class="adverts-icon-resize-vertical"></span></a>
                        <a href="#" class="adverts-image-action-flip-v <?php echo $button ?> adverts-button-small" title="<?php _e("Flip Horizontally", "wpadverts") ?>"><span class="adverts-icon-resize-horizontal"></span></a>
                        <a href="#" class="adverts-image-action-undo <?php echo $button ?> adverts-button-small" title="<?php _e("Undo", "wpadverts") ?>"><span class="adverts-icon-history"></span></a>

                    </div>

                    <div class="adverts-control-group">
                        <label for="adverts_caption"><?php _e("Image Size", "wpadverts") ?></label>
                        <input type="number" class="adverts-image-scale-width" name="d_width" value="{{ data.dim[0] }}" max="{{ data.dim[0] }}" step="1" />
                        x
                        <input type="number" class="adverts-image-scale-height" name="d_height"  value="{{ data.dim[1] }}" max="{{ data.dim[1] }}" step="1" />
                        <a href="#" class="adverts-image-action-scale <?php echo $button ?> adverts-button-small"><?php _e("Scale", "wpadverts") ?></a>
                    </div>

                    <div class="adverts-control-group">
                        <label for="adverts_content"><?php _e("Actions", "wpadverts") ?></label>
                    
                        <a href="#" class="adverts-image-action-save <?php echo $button ?> adverts-button-small" title="<?php _e("Save Image", "wpadverts") ?>"><?php _e("Save", "wpadverts") ?></a>
                        <a href="#" class="adverts-image-action-cancel <?php echo $button ?> adverts-button-small" title="<?php _e("Cancel") ?>"><?php _e("Cancel", "wpadverts") ?></a>
                        &nbsp;
                        <a href="#" class="adverts-image-action-restore <?php echo $button ?> adverts-button-small" title="<?php _e("Restore original image", "wpadverts") ?>"><?php _e("Restore", "wpadverts") ?></a>

                        &nbsp;
                        
                        <span class="wpadverts-image-edit-spinner adverts-icon-spinner animate-spin"></span>
                        
                        <# if(data.size == "full") { #>
                        <div class="wpadverts-image-apply-to">
                            <input type="checkbox" name="wpadverts-image-action-apply-to" class="wpadverts-image-action-apply-to" value="1" checked="checked" />
                            <label for="wpadverts-image-action-apply-to"><?php _e( "Apply changes to all image sizes", "wpadverts") ?></label>
                        </div>
                        <# } else { #>
                            <input type="hidden" name="wpadverts-image-action-apply-to" class="wpadverts-image-action-apply-to" value="0" />
                        <# } #>
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
    <?php endif; ?>

    <script type="text/html" id="tmpl-wpadverts-browser-error">
    <# if(data.overlay === true) { #>
    <div class="wpadverts-overlay">
    <# } #>
        <div class="wpadverts-file-error">

            <div class="wpadverts-attachment-other">
                <div class="wpadverts-attachment-icon-big-wrap">
                    <span class="wpadverts-attachment-icon-big adverts-icon-attention"></span>
                </div>
                <div class="wpadverts-attachment-icon-big-wrap">
                    <span>
                        <strong>{{ data.error }}</strong>
                    </span>
                </div>
                <a href="#" class="<?php echo $button ?>"><?php _e("Close") ?></a>
            </div>
        </div>
    <# if(data.overlay === true) { #>
    </div>
    <# } #>
    </script>
    
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
        
        if( $image_key !== "full" && isset( $src[3] ) && $src[3] === false ) {
            $src[1] = $sizes["full"]["width"];
            $src[2] = $sizes["full"]["height"];
        }
        
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
        "post_id_nonce" => wp_create_nonce( "wpadverts-publish-" . $post->post_parent ),
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

/**
 * Formats information about specific uploaded file
 * 
 * @since   1.5.0
 * @param   array       $file       File data
 * @param   WP_Post     $post       WP_Post object
 * @param   string      $uniqid     Uniqe ID generated for the upload
 * @return  array
 */
function adverts_upload_file_data( $file, $post = null, $uniqid = null ) {
    
    if( $post ) {
        $post_parent = $post->ID;
        $post_parent_nonce = wp_create_nonce( "wpadverts-publish-" . $post->ID );
    } else {
        $post_parent = null;
        $post_parent_nonce = "";
    }
    
    $data = array(
        "uniqid" => $uniqid,
        "post_id" => $post_parent,
        "post_id_nonce" => $post_parent_nonce,
        "attach_id" => null,
        "guid" => $file["url"],
        "mime_type" => $file["type"],
        "featured" => "",
        "caption" => "",
        "content" => "",
        "sizes" => array(),
        "readable" => array(
            "name" => basename( $file["file"] ),
            "type" => $file["type"],
            "uploaded" => date_i18n( get_option( "date_format"), filemtime( $file["file"] ) ),
            "size" => size_format( filesize( $file["file"] ) ),
            "length" => null
        )
    );
    
    return $data;
}

/**
 * Returns explainer texts for image sizes
 * 
 * This explainers do explain where the images are being used. This function 
 * is being used only in adverts_gallery_modal()
 * 
 * @see adverts_gallery_modal()
 * 
 * @since 1.2
 * @param string    $size           Size for which explainers should be returned
 * @return mixed                    Array if $size is not provided or string
 */
function adverts_gallery_explain_size( $size = null ) {
    
    $e = apply_filters( "adverts_gallery_explain", array( 
        "full" => array(
            "enabled" => 1,
            "title" => __( "Gallery - Full Size", "wpadverts" ),
            "desc" => __( "Image in original size - used on classified details page in the gallery.", "wpadverts" )
        ),
        "adverts-gallery" => array(
            "enabled" => 0,
            "title" => __( "Gallery - Slider", "wpadverts" ),
            "desc" => __( "Image resized to %d x %d - used in the images slider on classified details page.", "wpadverts" )
        ),
        "adverts-list" => array(
            "enabled" => 0,
            "title" => __( "Classifieds List", "wpadverts" ),
            "desc" => __( "Image resized to %d x %d - used on the classifieds list.", "wpadverts" )
        ),
        "adverts-upload-thumbnail" => array(
            "enabled" => 0,
            "title" => __( "Thumbnail", "wpadverts" ),
            "desc" => __( "Image resized to %d x %d - the image visible in upload preview.", "wpadverts" )
        ),
    ), $size );
    
    $sizes = adverts_config( "gallery.image_sizes" );
    
    foreach( $e as $key => $s ) {
        if( isset( $sizes[$key] ) && $sizes[$key]["enabled"] == "1" ) {
            $e[$key]["enabled"] = 1;
            $e[$key]["desc_parsed"] = sprintf( $e[$key]["desc"], $sizes[$key]["width"], $sizes[$key]["height"] );
        }
    }
    
    if( $size === null ) {
        return $e;
    }

    if( isset( $e["size"] ) ) {
        return $e["size"];
    }
}