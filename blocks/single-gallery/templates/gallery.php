<div class="">
    <div class="">
        BIG IMAGE
    </div>

    <div id="<?php echo sprintf("wpa-grid-gallery-slider--%d", get_the_ID()) ?>" class="wpa-grid-gallery-slider">
            <?php foreach($images as $image): ?>
                <?php $image = wp_get_attachment_image_src( $image->ID, $data->image_type ); ?>
                <?php if(is_array($image) && isset($image[0])): ?>
                    <div class="<?php echo esc_attr($slider_img_height) ?>">
                        <div class="atw-flex atw-justify-center atw-w-full atw-h-full">
                            <?php if($slider_is_lazy): ?>
                            <img data-src="<?php echo esc_attr( $image[0] ) ?>" class="wpa-lazy-img atw-object-contain" alt="" />
                            <?php else: ?>
                            <img src="<?php echo esc_attr( $image[0] ) ?>" class="atw-object-contain" alt="" />
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
    </div>
</div>