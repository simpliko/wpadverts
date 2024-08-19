<div class="wpa-block-gallery atw-relative  atw-mb-3" >

    <div class="atw-relative">
        <div class="wpa-block-gallery-slider atw-relative <?php if($slider_is_lazy): ?>wpa-slider-is-lazy<?php endif; ?>">
            <?php foreach($images as $attach): ?>
                
                <div class="<?php echo esc_attr($gallery_img_height) ?> atw-w-full atw-flex-none">

                    <?php $mime = adverts_get_attachment_mime( $attach ) ?>

                    <?php if( $mime == "image" ): ?>

                    <a data-title="<?php echo esc_attr($attach->post_excerpt ?? $attach->post_title) ?>" data-description="<?php echo esc_attr($attach->post_content) ?>" data-desc-position="bottom" href="<?php echo esc_attr( $attach->guid ) ?>" class="wpa-glightbox atw-box-border atw-flex atw-justify-center atw-h-full atw-border atw-border-solid atw-border-gray-100 atw-round atw-mx-1">
                    
                        <?php $image = wp_get_attachment_image_src( $attach->ID, $gallery_img_size );  ?>
                        <?php if($slider_is_lazy): ?>
                            <img draggable="false" data-src="<?php echo esc_attr( $image[0] ) ?>" class="wpa-lazy-img atw-h-full atw-w-full atw-max-h-full atw-max-w-full atw-object-center <?php echo esc_attr($gallery_fit) ?>" alt="" />
                        <?php else: ?>
                            <img draggable="false" src="<?php echo esc_attr( $image[0] ) ?>" class="atw-h-full atw-w-full atw-max-h-full atw-max-w-full atw-object-center <?php echo esc_attr($gallery_fit) ?>" alt="" />
                        <?php endif; ?>
                    </a>

                    <?php elseif( $mime == "video" ): ?>

                    <a data-title="<?php echo esc_attr($attach->post_excerpt ?? $attach->post_title) ?>" data-description="<?php echo esc_attr($attach->post_content) ?>" data-desc-position="bottom" href="<?php echo esc_attr( $attach->guid ) ?>" class="wpa-glightbox atw-box-border atw-flex atw-justify-center atw-h-full atw-border atw-border-solid atw-border-gray-100 atw-round atw-mx-1">
                    
                        <video <?php if(!$custom_controls):?>controls="true"<?php endif; ?> src="<?php echo $attach->guid ?>" preload="metadata" poster="<?php echo adverts_get_post_img_url( $attach, array( $gallery_img_size ) ) ?>" class="atw-m-0 atw-p-0">
                            <?php _e("Your browser cannot play this video.", "wpadverts") ?> 
                            <?php _e("Please use a different browser or download the video and play on your device.", "wpadverts" ) ?>
                        </video>

                    </a>

                    <?php else: ?>

                    <a href="<?php echo "#inline-attach--".$attach->ID ?>" data-title="<?php echo esc_attr($attach->post_excerpt ?? $attach->post_title) ?>" data-description="<?php echo esc_attr($attach->post_content) ?>" data-desc-position="bottom" class="wpa-glightbox atw-no-underline atw-box-border atw-flex atw-justify-center atw-h-full atw-border atw-border-solid atw-border-gray-100 atw-round atw-mx-1">
                    
                        <div id="<?php echo "inline-attach--".$attach->ID ?>" class="atw-flex atw-content-center atw-w-full atw-h-full atw-items-center">
                            <div class="atw-mx-auto atw-flex atw-flex-col atw-justify-center atw-w-full atw-h-full ">
                                <div class="atw-flex atw-flex-col atw-my-1 atw-justify-center atw-items-center">
                                    <span class="<?php echo wpadverts_get_file_fa_icon( $attach ) ?> atw-text-6xl atw-text-gray-600 atw-mb-3"></span>
                                    <span class="atw-text-sm"><?php echo _e("File: ", "wpadverts" ) ?><span class="atw-font-bold"><?php echo basename( $attach->guid ) ?></span></span>
                                </div>
                                <div class="atw-flex atw-mt-3 atw-justify-center atw-h-auto">
                                    <span class="atw-inline-block atw-border atw-text-bold atw-border-solid atw-border-gray-600 atw-text-gray-600 atw-bg-gray-200">
                                        <span onclick="window.location.href='<?php echo esc_attr( $attach->guid ) ?>'" class="atw-cursor-pointer atw-no-underline atw-inline-block atw-px-6 atw-py-3 atw-uppercase"><?php _e("Download", "wpadverts" ) ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </a>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>
        </div>
        <div class="wpa-gallery-nav-slider" class="atw-relative">
                <div class="wpa-block-gallery-left atw-ml-1 atw-hidden atw-absolute atw-inset-y-0 atw-left-0 atw-items-center">
                    <a href="#" class="wpa-block-gallery-left-btn atw-flex-none atw-box-border atw-no-underline atw-block atw-ml-1 atw-px-1 atw-w-8 atw-h-8 atw-shadow atw-rounded-full atw-text-center" style="background-color:rgba(7, 17, 33, 0.46)">
                        <span class="fas fa-chevron-left atw-inline-block atw-text-white atw-text-lg atw-pt-0.5"></span>
                    </a>
                </div>
                <div class="wpa-block-gallery-right atw-mr-1 atw-hidden atw-absolute atw-inset-y-0 atw-right-0 atw-items-center">
                    <a href="#" class="wpa-block-gallery-right-btn atw-flex-none atw-box-border atw-no-underline atw-block atw-mr-1 atw-px-1 atw-w-8 atw-h-8 atw-shadow atw-rounded-full atw-text-center" style="background-color:rgba(7, 17, 33, 0.46)">
                        <span class="fas fa-chevron-right atw-inline-block atw-text-white atw-text-lg atw-pt-0.5"></span>
                    </a>
                </div>

                <div class="atw-absolute <?php echo esc_attr( $nav_position ) ?>">
                    <div class="atw-flex atw-items-center atw-bg-white atw-rounded-lg atw-mx-3 atw-my-2 atw-px-2 atw-py-1 atw-border atw-border-solid atw-border-gray-100">
                        <span class="atw-text-sm fas fa-camera atw-pr-2"></span>
                        <span class="atw-text-xs atw-font-semibold "><span class="wpa-block-gallery-current">1</span> / <?php echo count( $images ) ?></span>
                    </div>
                </div>
        </div>
    </div>

    <div class="wpa-block-gallery-nav wpa-picture-grid atw-relative <?php if(!$thumb_show): ?>atw-hidden<?php endif; ?>">
        <div id="<?php echo sprintf("wpa-block-gallery-nav--%d", get_the_ID()) ?>" class="wpa-block-gallery-nav-container atw-scroll-smooth atw-flex atw-flex-row atw-flex-nowrap atw-justify-start atw-overflow-x-scroll atw-py-3">
            <?php $i = 0 ?>
            <?php foreach($images as $attach): ?>
       
                    <div class="<?php echo esc_attr($thumb_width . " " . $thumb_height) ?> atw-flex-none">
                        <div data-item="<?php echo ($i++) ?>" class="wpa-block-gallery-nav-item atw-cursor-pointer atw-flex atw-justify-center atw-h-full atw-border atw-border-solid atw-border-gray-200 atw-bg-gray-100 atw-round atw-mx-1">
                            
                            <?php $mime = adverts_get_attachment_mime( $attach ) ?>

                            <?php if( $mime == "image" ): ?>
                                
                                <div class="atw-relative atw-w-full atw-h-full">
                                    <?php $image = wp_get_attachment_image_src( $attach->ID, $thumb_img_size ); ?>
                                    <img title="<?php echo esc_attr($attach->post_excerpt ?? $attach->post_title) ?>" draggable="false" src="<?php echo esc_attr( $image[0] ) ?>" class="atw-h-full atw-w-full atw-max-h-full atw-max-w-full atw-object-center <?php echo esc_attr( $thumb_fit ) ?>" alt="" />
                                </div>

                            <?php elseif( $mime == "video"): ?>

                                <?php $image = wp_get_attachment_image_src( $attach->ID, $thumb_img_size ) ?>
                                <?php //echo "<pre>";print_r($thumb_img_size); echo "</pre>" ?>
                                <div class="atw-relative atw-w-full atw-h-full">

                                    <?php if($image): ?>
                                        <img title="<?php echo esc_attr($attach->post_excerpt ?? $attach->post_title) ?>" draggable="false" src="<?php echo esc_attr( $image[0] ) ?>" class="atw-h-full atw-w-full atw-max-h-full atw-max-w-full atw-object-center <?php echo esc_attr( $thumb_fit ) ?>" alt="" />

                                        <span class="atw-flex atw-absolute atw-inset-0 atw-w-full atw-h-full atw-items-center">
                                            <span class="far fa-circle-play atw-text-gray-100 atw-text-3xl atw-text-center atw-w-full atw-inline-block"></span>
                                        </span>

                                    <?php else: ?>

                                        <span class="atw-flex atw-absolute atw-inset-0 atw-w-full atw-h-full atw-items-center">
                                            <span class="fas fa-video atw-text-gray-600 atw-text-3xl atw-text-center atw-w-full atw-inline-block"></span>
                                        </span>

                                    <?php endif; ?>



                                </div>

                            <?php else: ?>

                                <div class="atw-relative atw-w-full atw-h-full" draggable="false">
                                    <span class="atw-flex atw-absolute atw-inset-0 atw-w-full atw-h-full atw-items-center">
                                        <span class="<?php echo wpadverts_get_file_fa_icon($attach) ?> atw-text-gray-600 atw-text-3xl atw-text-center atw-w-full atw-inline-block"></span>
                                    </span>
                                </div>

                            <?php endif; ?>


                        </div>
                    </div>
            <?php endforeach; ?>
        </div>

        <div class="wpa-block-gallery-nav-interface">
                <div class="wpa-block-gallery-nav-left atw-hidden atw-absolute atw-inset-y-0 atw-left-0 atw-items-center">
                    <a href="#" class="wpa-block-gallery-nav-left-btn atw-flex-none atw-box-border atw-no-underline atw-block atw-ml-1 atw-px-1 atw-w-8 atw-h-8 atw-shadow atw-rounded-full atw-text-center" style="background-color:rgba(7, 17, 33, 0.46)">
                        <span class="fas fa-chevron-left atw-block atw-text-white atw-text-lg atw-pt-0.5"></span>
                    </a>
                </div>
                <div class="wpa-block-gallery-nav-right atw-hidden atw-absolute atw-inset-y-0 atw-right-0 atw-items-center">
                    <a href="#" class="wpa-block-gallery-nav-right-btn atw-flex-none atw-box-border atw-no-underline atw-block atw-mr-1 atw-px-1 atw-w-8 atw-h-8 atw-shadow atw-rounded-full atw-text-center" style="background-color:rgba(7, 17, 33, 0.46)">
                        <span class="fas fa-chevron-right atw-block atw-text-white atw-text-lg atw-pt-0.5"></span>
                    </a>
                </div>
        </div>
    </div>

</div>

<?php if($gallery_bg || $thumb_bg): ?>
<style type="text/css">
<?php if($gallery_bg): ?>
.wpa-block-gallery-slider a { background-color: <?php echo esc_js($gallery_bg) ?>;}
<?php endif; ?>
<?php if($thumb_bg): ?>
.wpa-block-gallery-nav .wpa-block-gallery-nav-item { background-color: <?php echo esc_js($thumb_bg) ?>;}
<?php endif; ?>
</style>

<?php endif; ?>
