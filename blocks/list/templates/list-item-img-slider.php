

        <div class="wpa-grid-gallery-slider-wrap atw-relative atw-w-full <?php echo $slider_is_lazy ? " wpa-slider-is-lazy" : "" ?>">

            <div id="<?php echo sprintf("wpa-grid-gallery-slider--%d", get_the_ID()) ?>" class="wpa-grid-gallery-slider">
            <?php $total = 0 ?>
            <?php foreach($images as $image): ?>
                <?php $image = wp_get_attachment_image_src( $image->ID, $data->image_type ); ?>
                <?php if(is_array($image) && isset($image[0])): ?>
                    <?php $total++ ?>
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

            <div class="">
                <!--div class="atw-flex atw-absolute atw-bottom-0 atw-inset-x-0 atw-items-center  atw-justify-center">
                    <div class="wpa-block-gallery-circles atw-flex atw-items-center">
                        <?php for($i=0; $i<$total; $i++): ?>
                        <div class="wpa-block-gallery-circle atw-flex atw-items-center atw-justify-center">
                            <div class="wpa-circle-inner atw-flex atw-rounded-full atw-border atw-border-solid atw-border-white" style="background-color: rgba(35, 46, 63, 0.19)"></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div-->

                <div class="wpa-grid-gallery-left atw-flex atw-absolute atw-inset-y-0 atw-left-0 atw-items-center">
                    <a href="#" class="wpa-grid-gallery-left-btn atw-block atw-ml-1 atw-px-1 atw-w-8 atw-h-8 atw-shadow atw-rounded-full atw-text-center" style="background-color:rgba(7, 17, 33, 0.46)">
                        <span class="fas fa-chevron-left atw-block atw-text-white atw-text-lg atw-pt-0.5"></span>
                    </a>
                </div>
                <div class="wpa-grid-gallery-right atw-flex atw-absolute atw-inset-y-0 atw-right-0 atw-items-center">
                    <a href="#" class="wpa-grid-gallery-right-btn atw-block atw-mr-1 atw-px-1 atw-w-8 atw-h-8 atw-shadow atw-rounded-full atw-text-center"  style="background-color:rgba(7, 17, 33, 0.46)">
                        <span class="fas fa-chevron-right atw-block atw-text-white atw-text-lg atw-pt-0.5"></span>
                    </a>
                </div>

                <div class="atw-absolute atw-bottom-0 atw-right-0 <?php if(!$slider_nav): ?>atw-hidden<?php endif ?>">
                    <div class="atw-flex atw-items-center atw-bg-white atw-rounded-lg atw-mr-2 atw-mb-2 atw-px-2 atw-py-1 atw-border atw-border-solid atw-border-gray-100">
                        <span class="atw-text-sm fas fa-camera atw-pr-2"></span>
                        <span class="atw-text-xs atw-font-semibold "><span class="wpa-grid-gallery-current">1</span> / <?php echo $total ?></span>
                    </div>

                </div>
            </div>
        </div>