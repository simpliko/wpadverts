<?php

/**
 * Gallery Helper Manages Item Attachments
 * 
 * This class is used to manage: navigation and lightbox on Ad details and 
 * [adverts_add] preview pages.
 * 
 * @package Adverts
 * @subpackage Classes
 * @since 1.2.0
 * @access public
 * 
 */

class Adverts_Gallery_Helper {
    
    /**
     * Post ID
     *
     * @var int
     */
    protected $_post_id = null;
    
    /**
     * Advert Attachments
     *
     * @var array
     */
    protected $_attachments = array();
    
    /**
     * Class Constructor
     * 
     * @since 1.2.0
     * @param int $post_id  ID of a post for wchi gallery will be created
     */
    public function __construct( $post_id ) {
        $this->_post_id = $post_id;
    }

    /**
     * Checks if Lightbox is enabled
     * 
     * Returns true if lightbox in wp-admin / Classifieds / Options / Core / Gallery
     * is enabled.
     * 
     * @since 1.2.0
     * @return boolean
     */
    public function has_lightbox() {
        if( adverts_config( 'gallery.lightbox' ) == "1" ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Register Gallery Scripts
     * 
     * @since 1.2.0
     * @return void
     */
    public function register_scripts() {
        wp_enqueue_script( 'adverts-single' );
        wp_enqueue_script( 'adverts-slick' );

        if( $this->has_lightbox() ) {
            add_action( "wp_footer", array( $this, "footer_lightbox" ) ); //"adverts_single_gallery_lightbox" );
            wp_enqueue_script( 'adverts-swipebox' );
            wp_enqueue_style( 'adverts-swipebox' );
        } 
    }
    
    /**
     * Loads and returns a list of attachments for an Advert
     * 
     * @since 1.2.0
     * @return array        List of attachments
     */
    public function load_attachments( ) {
        
        $post_id = $this->_post_id;
        
        $form_name = "advert";
        $field_name = "gallery";
        
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
        
        $args = array(
            'post_parent' => $post_id,
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'meta_query' => $att_search_meta
        );
        
        $children = get_children( $args );
        $thumb_id = get_post_thumbnail_id( $post_id );
        $attach = array();

        if( empty( $children ) ) {
            return array();
        }

        if( isset( $children[$thumb_id] ) ) {
            $attach[$thumb_id] = $children[$thumb_id];
            unset($children[$thumb_id]);
        }

        $attach += $children;
        $attach = adverts_sort_images($attach, $post_id, $field_name);

        return apply_filters( "adverts_get_post_attachments", $attach, $post_id );
    }
    
    /**
     * Returns cached list of attachments
     * 
     * That is the attachments stored in self::$_attachments field.
     * 
     * @since 1.2.0
     * @return array
     */
    public function get_attachments() {
        return $this->_attachments;
    }
    
    /**
     * Echoes gallery content
     * 
     * The gallery content is visible on single Advert pages and in 
     * [adverts_add] Preview.
     * 
     * @return void
     */
    public function render_gallery() {
        
        $this->_attachments = $this->load_attachments();

        if( empty( $this->_attachments ) ) {
            return;
        }
        
        $custom_cotrols = apply_filters( "adverts_gallery_enable_custom_controls", true );
        
        if( adverts_config( "gallery.image_fit" ) != '' ) {
            $image_fit = "wpadverts-slide-img-fit-" . adverts_config( "gallery.image_fit" );
        } else {
            $image_fit = "wpadverts-slide-img-fit-auto";
        }
        
        $this->register_scripts();
        
        ?>

        <div class="wpadverts-slides wpadverts-slides-with-thumbnail">

            <div class="wpadverts-slides-list">
                <?php foreach($this->_attachments as $attach): ?>
                    <?php if( adverts_get_attachment_mime( $attach ) == "image" ): ?>
                        <?php $image = adverts_get_post_img( $attach, array( "adverts_gallery", "full" ) ); ?>
                        <?php $image_classes = "wpadverts-slide-img-" . $image["orient"] . " " . $image_fit; ?>
                        <div class="wpadverts-slide wpadverts-slide-image" id="<?php echo "wpadverts-slide-".$attach->ID ?>">

                            <div class="wpadverts-slide-decoration">
                                <?php if( adverts_config( 'gallery.lightbox' ) == "1"): ?>
                                <a class="wpadverts-swipe" href="<?php echo adverts_get_post_img_url( $attach, array( "full" ) ); ?>" title="<?php echo esc_html(trim($attach->post_excerpt . " - " . $attach->post_content, " -")) ?>">
                                    <img src="<?php echo esc_attr($image["url"]) ?>" class="<?php echo "wpadverts-slide-img " . $image_classes ?>" title="<?php echo esc_html($attach->post_excerpt) ?>" alt="<?php echo esc_html($attach->post_content) ?>" />
                                    <div class="wpadverts-slide-with-shadow"></div>
                                </a>
                                <?php else: ?>
                                    <img src="<?php echo esc_attr($image["url"]) ?>" class="<?php echo "wpadverts-slide-img wpadverts-slide-img-".$image["orient"] ?>" title="<?php echo esc_html($attach->post_excerpt) ?>" alt="<?php echo esc_html($attach->post_content) ?>" />
                                    <div class="wpadverts-slide-with-shadow"></div>
                                <?php endif; ?>
                            </div>

                            <div class="wpadverts-slide-caption">
                                <?php if($attach->post_excerpt): ?>
                                <span><?php echo esc_html($attach->post_excerpt) ?></span>
                                <?php endif; ?>
                                <?php if($attach->post_content): ?>
                                <span class="wpadverts-slide-caption-desc">
                                    <br/>
                                    <?php echo esc_html($attach->post_content) ?>
                                </span>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php elseif( adverts_get_attachment_mime( $attach ) == "video" ): ?>
                        <div class="wpadverts-slide wpadverts-slide-video" id="<?php echo "wpadverts-slide-".$attach->ID ?>">

                            <div class="wpadverts-video-player">
                                <video <?php if(!$custom_cotrols):?>controls="true"<?php endif; ?> src="<?php echo $attach->guid ?>" preload="metadata" poster="<?php echo adverts_get_post_img_url( $attach, array( 'adverts_gallery' ) ) ?>">
                                    Your browser cannot play this video. 
                                    Please use a different browser or download the video and play on your device.
                                    <a href="<?php echo $attach->guid ?>" class="adverts-button"><?php _e("Download", "wpadverts") ?></a>
                                </video>

                                <?php if( $custom_cotrols ): ?>
                                <?php $this->render_custom_video_controls( $attach ) ?>
                                <?php endif; ?>

                                <div class="wpadverts-slide-caption">
                                    <?php if($attach->post_excerpt): ?>
                                    <span><?php echo esc_html($attach->post_excerpt) ?></span>
                                    <?php endif; ?>
                                    <?php if($attach->post_content): ?>
                                    <span class="wpadverts-slide-caption-desc">
                                        <br/>
                                        <?php echo esc_html($attach->post_content) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="wpadverts-slide wpadverts-slide-other" id="<?php echo "wpadverts-slide-".$attach->ID ?>">

                            <div class="wpadverts-slide-with-shadow"></div>
                            <span class="wpadverts-slide-icon <?php echo adverts_get_attachment_icon( $attach ) ?>"></span>

                            <p class="wpadverts-slide-description">
                                <?php if($attach->post_excerpt): ?>
                                    <strong class="wpadverts-slide-title"><?php echo esc_html( $attach->post_excerpt ) ?></strong>
                                <?php else: ?>
                                    <strong class="wpadverts-slide-title"><?php echo esc_html( $attach->post_title ) ?></strong>
                                <?php endif; ?>
                            </p>

                            <a href="<?php echo esc_html( $attach->guid ) ?>" class="adverts-button"><?php _e("Download File", "wpadverts") ?></a>

                            <p class="wpadverts-slide-description wpadverts-slide-desc">
                                <?php if($attach->post_content): ?>
                                    <span class="wpadverts-slide-content"><?php echo esc_html( $attach->post_content ) ?></span>
                                <?php else: ?>
                                    <span class="wpadverts-slide-content">&nbsp;</span>
                                <?php endif; ?>
                            </p>

                            <?php if( adverts_config( 'gallery.lightbox' ) == "1"): ?>
                            <a class="wpadverts-swipe" href="#<?php echo "wpadverts-slide-full-".$attach->ID ?>" style="display: none"></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if( adverts_config( 'gallery.ui') == "pagination" ): ?>
                <?php $this->nav_pagination( ); ?>
                <?php endif; ?>


            </div>

            <?php if( adverts_config( 'gallery.ui') == "thumbnails" ): ?>
            <?php $this->nav_thumbnails( ); ?>
            <?php endif; ?>



        </div>

        <?php    
    }
    
    /**
     * Renders custom controls for the video player
     * 
     * @since   1.3.0
     * @param   WP_Post   $attach     The attachment object
     * @return  void
     */
    public function render_custom_video_controls( $attach ) {
        ?>

        <div class="wpadverts-slide-with-shadow"></div>

        <div class="wpadverts-player">
            <div class="wpadverts-player-item wpadverts-player-item-play-pause">
                <span class="adverts-icon-play wpadverts-player-play wpadverts-slide-nav-pointer" title="<?php _e("Play", "wpadverts") ?>"></span>
                <span class="adverts-icon-pause wpadverts-player-pause wpadverts-slide-nav-pointer" title="<?php _e("Pause", "wpadverts") ?>"></span>
                <span class="adverts-icon-ccw wpadverts-player-replay wpadverts-slide-nav-pointer" title="<?php _e("Replay", "wpadverts") ?>"></span>
            </div>
            <div class="wpadverts-player-item wpadverts-player-item-progress">
                <span class="wpadverts-player-item-progress-bar wpadverts-slide-nav-pointer" style="">
                    <span class="wpadverts-player-progress"></span>
                    <span class="wpadverts-player-item-progress-text"></span>
                </span>
            </div>
            <div class="wpadverts-player-item wpadverts-player-item-volume-down">
                <span class="wpadverts-player-volume-down adverts-icon-volume-down wpadverts-slide-nav-pointer" title="<?php _e("Volume Down", "wpadverts") ?>"></span>
            </div>
            <div class="wpadverts-player-item wpadverts-player-item-volume-up">
                <span class="wpadverts-player-volume-up adverts-icon-volume-up wpadverts-slide-nav-pointer" title="<?php _e("Volume Up", "wpadverts") ?>"></span>
            </div>

            <?php if( adverts_config( 'gallery.lightbox' ) == "1"): ?>
            <div class="wpadverts-player-item wpadverts-player-item-fullscreen">
                <span class="wpadverts-player-fullscreen adverts-icon-resize-full-alt wpadverts-slide-nav-pointer" title="<?php _e("Full Screen", "wpadverts") ?>"></span>
            </div>
            <a class="wpadverts-swipe" href="#<?php echo "wpadverts-slide-full-".$attach->ID ?>" style="display:none"></a>
            <?php endif; ?>
        </div>

        <?php
    }
    
    /**
     * Renders Lightbox content
     * 
     * This function is called in wp_footer action.
     * 
     * Tthe filter is applied in self::register_scripts(), but only if lightbox
     * is enabled in wp-admin / Classifieds / Options / Gallery panel.
     * 
     * @see self::register_scripts()
     * @uses wp_footer action
     * 
     * @since 1.2.0
     * @return void
     */
    public function footer_lightbox() {

        $attachments = $this->get_attachments();

        if( empty( $attachments ) ) {
            return;
        }

        ?>
        <div style="display: none">
            <?php foreach($attachments as $attach): ?>
                <?php if( adverts_get_attachment_mime( $attach ) == "image" ): ?>
                <?php // do nothing, images are loaded in the gallery ?>
                <?php elseif( adverts_get_attachment_mime( $attach ) == "video" ): ?>
                    <div class="wpadverts-slide-video" id="<?php echo "wpadverts-slide-full-".$attach->ID ?>" style="position: relative">

                        <div class="wpadverts-video-player">
                            <video src="<?php echo $attach->guid ?>" preload="metadata" poster="<?php echo adverts_get_post_img_url( $attach, array( 'adverts_gallery' ) ) ?>">
                                <?php _e("Your browser cannot play this video. Please use a different browser or download the video and play on your device.", "wpadverts") ?>
                                <a href="<?php echo $attach->guid ?>" class="adverts-button"><?php _e("Download", "wpadverts") ?></a>
                            </video>
                            <div class="wpadverts-slide-with-shadow"></div>

                            <div class="wpadverts-player">
                                <div class="wpadverts-player-item wpadverts-player-item-play-pause">
                                    <span class="adverts-icon-play wpadverts-player-play wpadverts-slide-nav-pointer"></span>
                                    <span class="adverts-icon-pause wpadverts-player-pause wpadverts-slide-nav-pointer"></span>
                                    <span class="adverts-icon-ccw wpadverts-player-replay wpadverts-slide-nav-pointer"></span>
                                </div>
                                <div class="wpadverts-player-item wpadverts-player-item-progress">
                                    <span class="wpadverts-player-item-progress-bar wpadverts-slide-nav-pointer" style="">
                                        <span class="wpadverts-player-progress"></span>
                                        <span class="wpadverts-player-item-progress-text"></span>
                                    </span>
                                </div>
                                <div class="wpadverts-player-item wpadverts-player-item-volume-down">
                                    <span class="wpadverts-player-volume-down adverts-icon-volume-down wpadverts-slide-nav-pointer"></span>
                                </div>
                                <div class="wpadverts-player-item wpadverts-player-item-volume-up">
                                    <span class="wpadverts-player-volume-up adverts-icon-volume-up wpadverts-slide-nav-pointer"></span>
                                </div>
                            </div>

                            <div class="wpadverts-slide-caption">
                                <?php if($attach->post_excerpt): ?>
                                <span><?php echo esc_html($attach->post_excerpt) ?></span>
                                <?php endif; ?>
                                <?php if($attach->post_content): ?>
                                <span class="wpadverts-slide-caption-desc">
                                    <br/>
                                    <?php echo esc_html($attach->post_content) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="wpadverts-slide-other" id="<?php echo "wpadverts-slide-full-".$attach->ID ?>">

                        <span class="wpadverts-slide-icon <?php echo adverts_get_attachment_icon( $attach ) ?>"></span>

                        <p class="wpadverts-slide-description">
                            <?php if($attach->post_excerpt): ?>
                                <strong class="wpadverts-slide-title"><?php echo esc_html( $attach->post_excerpt ) ?></strong>
                            <?php else: ?>
                                <strong class="wpadverts-slide-title"><?php echo esc_html( $attach->post_title ) ?></strong>
                            <?php endif; ?>
                        </p>

                        <a href="<?php echo esc_html( $attach->guid ) ?>" class="adverts-button"><?php _e("Download File", "wpadverts") ?></a>

                        <p class="wpadverts-slide-description" style="margin-left: 5em; margin-right: 5em; line-height: 1.3em; color: white; white-space: normal">
                            <?php if($attach->post_content): ?>
                                <span class="wpadverts-slide-content"><?php echo esc_html( $attach->post_content ) ?></span>
                            <?php else: ?>
                                <span class="wpadverts-slide-content">&nbsp;</span>
                            <?php endif; ?>
                        </p>

                    </div>
                <?php endif; ?>

            <?php endforeach; ?>

        </div>
        <?php  
    }
    
    /**
     * Renders Gallery Pagination
     * 
     * This function is called by self::render_gallery() function.
     * 
     * The function is executed if in wp-admin / Classifieds / Options / 
     * Core / Gallery panel "Gallery Pagination" is set to "Next and Previous Buttons".
     * 
     * @see self::render_gallery()
     * 
     * @since 1.2.0
     * @return void
     */
    public function nav_pagination( ) {
        $attachments = $this->get_attachments();
        ?>
        <div class="wpadverts-slide-nav" style="">
            <div class="wpadverts-slide-nav-interface">
                <span class="wpadverts-slide-nav-thumbnails adverts-icon-th wpadverts-slide-nav-action wpadverts-slide-nav-color-hover" title="<?php _e("Show Thumbnails ...", "wpadverts") ?>"></span>
                &nbsp;
                <strong class="wpadverts-slide-nav-counter wpadverts-slide-nav-static">
                    <span class="wpadverts-slide-nav-current">-</span>/<?php echo count( $attachments ) ?>
                </strong>
                <span class="adverts-icon-camera wpadverts-slide-nav-static" title="<?php _e("Total Files", "wpadverts") ?>"></span>
            </div>

            <div class="wpadverts-slide-nav-paginate wpadverts-slide-nav-paginate-left wpadverts-slide-nav-action">
                <span class="adverts-icon-left-open wpadverts-slide-nav-color-hover" title="<?php _e("Previous", "wpadverts") ?>"></span>
            </div>

            <div class="wpadverts-slide-nav-paginate wpadverts-slide-nav-paginate-right wpadverts-slide-nav-action">
                <span class="adverts-icon-right-open wpadverts-slide-nav-color-hover" title="<?php _e("Next", "wpadverts") ?>"></span>
            </div>

            <div class="wpadverts-slide-nav-thumbnails-list" style="display: none">
                <ul id="wpadverts-rsliders-controls" class="wpadverts-als-wrapper als-wrapper" >
                    <?php foreach($attachments as $attach): ?>
                    <?php $media_desc = trim( $attach->post_excerpt . " - " . $attach->post_content, " -") ?>

                    <?php if( adverts_get_post_img_url( $attach, array( "adverts_upload_thumbnail" ) ) ): ?>
                        <?php $thumb = adverts_get_post_img_url( $attach, array( "adverts_upload_thumbnail" ) ) ?>
                        <li class="wpadverts-als-item als-item" data-advert-slide="<?php echo "wpadverts-slide-".$attach->ID ?>">
                            <a href="<?php echo esc_attr($attach->guid) ?>" title="<?php echo esc_attr( $attach->post_excerpt ) ?>" >
                                <img class="wpadverts-als-img" src="<?php echo $thumb ?>" alt="<?php echo esc_attr( $media_desc ) ?>" />
                            </a>
                            <?php if(adverts_get_attachment_mime( $attach ) == "video"): ?>
                            <span class="wpadverts-als-icon-video adverts-icon-play-circled2"></span>
                            <?php endif; ?>
                        </li>
                    <?php else: ?>
                        <li class="wpadverts-als-item als-item wpadverts-als-item-icon" data-advert-slide="<?php echo "wpadverts-slide-".$attach->ID ?>">
                            <a href="<?php echo esc_attr($attach->guid) ?>" title="<?php echo esc_attr( $attach->post_excerpt ) ?>">
                                <span class="<?php echo adverts_get_attachment_icon( $attach ) ?>" title="<?php echo esc_attr( $attach->post_excerpt ) ?>"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                        
                    <?php endforeach; ?>
                </ul>
                
                <div class="wpadverts-slide-nav-thumbnails-close"><span class="adverts-icon-cancel"></span></div>
            </div>

        </div>
        <?php 
    }
    
    /**
     * Renders Gallery Thumbnails
     * 
     * This function is called by self::render_gallery() function.
     * 
     * The function is executed if in wp-admin / Classifieds / Options / 
     * Core / Gallery panel "Thumbnails Slider" is set to "Next and Previous Buttons".
     * 
     * @see self::render_gallery()
     * 
     * @since 1.2.0
     * @return void
     */
    public function nav_thumbnails( ) {
        $attachments = $this->get_attachments();
        ?>
        <div class="wpadverts-als-container als-container">

            <div class="als-nav-wrap als-nav-wrap-left">
                <!--div class="als-nav-fake"><span class="adverts-icon-left-open"></span></div-->
                <div href="#" class="als-prev" title="<?php _e( "Previous", "wpadverts" ) ?>"><span class="adverts-icon-left-open"></span></div>

            </div>

            <div class="wpadverts-als-viewport als-viewport" style="">
                <ul id="wpadverts-rsliders-controls" class="wpadverts-als-wrapper als-wrapper" >
                    <?php foreach($attachments as $attach): ?>
                    <?php $media_desc = trim( $attach->post_excerpt . " - " . $attach->post_content, " -") ?>

                    <?php if( adverts_get_post_img_url( $attach, array( "adverts_upload_thumbnail" ) ) ): ?>
                        <?php $thumb = adverts_get_post_img_url( $attach, array( "adverts_upload_thumbnail" ) ) ?>
                        <li class="wpadverts-als-item als-item">
                            <a href="<?php echo esc_attr($attach->guid) ?>" data-advert-slide="<?php echo "wpadverts-slide-".$attach->ID ?>" title="<?php echo esc_attr( $attach->post_excerpt ) ?>" >
                                <img class="wpadverts-als-img" src="<?php echo $thumb ?>" alt="<?php echo esc_attr( $media_desc ) ?>" />
                            </a>
                            <?php if(adverts_get_attachment_mime( $attach ) == "video"): ?>
                            <span class="wpadverts-als-icon-video adverts-icon-play-circled2"></span>
                            <?php endif; ?>
                        </li>
                    <?php else: ?>
                        <li class="wpadverts-als-item als-item wpadverts-als-item-icon">
                            <a href="<?php echo esc_attr($attach->guid) ?>" data-advert-slide="<?php echo "wpadverts-slide-".$attach->ID ?>" title="<?php echo esc_attr( $attach->post_excerpt ) ?>">
                                <span class="<?php echo adverts_get_attachment_icon( $attach ) ?>" title="<?php echo esc_attr( $attach->post_excerpt ) ?>"></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="als-nav-wrap als-nav-wrap-right">
                <!--div class="als-nav-fake"><span class="adverts-icon-right-open"></span></div-->
                <div href="#" class="als-next" title="<?php _e( "Next", "wpadverts" ) ?>"><span class="adverts-icon-right-open"></span></div>
            </div>

        </div>
        <?php
    }
    
}
