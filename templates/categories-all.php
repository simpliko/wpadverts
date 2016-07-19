<?php if(!empty($terms)): ?>
<div class="adverts-flexbox adverts-categories-all">
<?php foreach($terms as $term): ?>
    <?php $icon = adverts_taxonomy_get("advert_category", $term->term_id, "advert_category_icon", "folder") ?>
    <?php $count = adverts_category_post_count( $term ); ?>
    <div class="adverts-flexbox-item <?php echo $columns ?> <?php echo "adverts-category-slug-".$term->slug ?>">
        <span class="adverts-flexbox-wrap">
            <span class="adverts-category-all-main">
                <span class="adverts-icon-<?php esc_html_e($icon) ?>"></span>
                <a class="" href="<?php esc_attr_e(get_term_link($term)) ?>">
                    <?php esc_html_e($term->name) ?>
                    <?php if($show_count): ?>
                    (<?php echo $count ?>)
                    <?php endif; ?>
                </a>
            </span>
            
            <ul class="adverts-flexbox-list">
                <?php
                    $subs = get_terms( 'advert_category', array( 
                        'hide_empty' => 0, 
                        'parent' => $term->term_id ,
                        'number' => $sub_count
                    ) );
                ?>
            
                <?php foreach($subs as $sub): ?>
                <li>
                    <a href="<?php esc_attr_e(get_term_link($sub)) ?>">
                        <?php esc_html_e($sub->name) ?>
                        <?php if($show_count): ?>
                        (<?php echo $sub->count ?>)
                        <?php endif; ?>
                    </a>
                </li>
                <?php endforeach; ?>
                <li>
                    <a href="<?php esc_attr_e(get_term_link($term)) ?>">
                        <?php _e("<em>View All &raquo;</em>", "adverts") ?>
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
        <span><?php _e("No categories found.", "adverts") ?></span>
    </div>
</div>
<?php endif; ?> 