    <div class="<?php echo adverts_css_classes( 'advert-item advert-item-col-'.(int)$columns, get_the_ID() ) ?>">

        <?php $image = adverts_get_main_image( get_the_ID() ) ?>
        <div class="advert-img">
            <?php if($image): ?>
                <img src="<?php echo esc_attr($image) ?>" alt="" class="advert-item-grow" />
            <?php endif; ?>
        </div>
     
        <div class="advert-post-title">
            <span title="<?php echo esc_attr( get_the_title() ) ?>" class="advert-link"><?php the_title() ?></span>
            <a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ) ?>" class="advert-link-wrap"></a>
        </div>
        
        <div class="advert-published ">
            
            <span class="advert-date"><?php echo date_i18n( get_option( 'date_format' ), get_post_time( 'U', false, get_the_ID() ) ) ?></span>
            
            <?php $location = get_post_meta( get_the_ID(), "adverts_location", true ) ?>
            <?php if( ! empty( $location ) ): ?>
            <span class="advert-item-col-1-only advert-location adverts-icon-location"><?php echo esc_html( $location ) ?></span>
            <?php endif; ?>
            
            <?php $price = get_post_meta( get_the_ID(), "adverts_price", true ) ?>
            <?php if( $price ): ?>
            <div class="advert-price"><?php echo esc_html( adverts_get_the_price( get_the_ID(), $price ) ) ?></div>
            <?php endif; ?>
        </div>
        
    </div>
