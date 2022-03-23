<?php if(!empty($terms)): ?>
<div class="adverts-flexbox adverts-categories-all">
<?php foreach($terms as $term): ?>
    <?php $icon = adverts_taxonomy_get("advert_category", $term->term_id, "advert_category_icon", "folder") ?>
    <?php $count = adverts_category_post_count( $term ); ?>
    <div class="adverts-flexbox-item <?php echo $columns ?> <?php echo "adverts-category-slug-".$term->slug ?>">
        <span class="adverts-flexbox-wrap">
            <span class="adverts-category-all-main">
                <span class="<?php echo apply_filters("adverts_category_font_icon", adverts_guess_icon_class($icon), $term, "small") ?>"></span>
                <?php do_action( "adverts_category_pre_title", $term, "small") ?>
                <a class="" href="<?php echo esc_attr(get_term_link($term)) ?>">
                    <?php echo esc_html($term->name) ?>
                    <?php if($show_count): ?>
                    (<?php echo $count ?>)
                    <?php endif; ?>
                </a>
            </span>
            
            <ul class="adverts-flexbox-list">
                <?php
                    $subs = get_terms( apply_filters( 'adverts_categories_query_sub', array( 
                        'taxonomy' => 'advert_category', 
                        'hide_empty' => 0, 
                        'parent' => $term->term_id ,
                        'number' => $sub_count
                    ) ) );
                ?>
            
                <?php foreach($subs as $sub): ?>
                <li>
                    <a href="<?php echo esc_attr(get_term_link($sub)) ?>">
                        <?php echo esc_html($sub->name) ?>
                        <?php if($show_count): ?>
                        (<?php echo $sub->count ?>)
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
                <li>
                    <a href="<?php echo esc_attr(get_term_link($term)) ?>">
                        <?php _e("<em>View All &raquo;</em>", "wpadverts") ?>
                    </a>
                </li>
            </ul>
        </span>
    </div>

<?php endforeach; ?>
</div>
<?php else: ?>
<div class="adverts-grid-row">
    <div class="adverts-col-100">
        <span><?php _e("No categories found.", "wpadverts") ?></span>
    </div>
</div>
<?php endif; ?> 