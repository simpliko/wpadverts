<?php if(!empty($terms)): ?>
<div class="adverts-flexbox adverts-categories-top">
<?php foreach($terms as $term): ?>
    <?php $icon = adverts_taxonomy_get("advert_category", $term->term_id, "advert_category_icon", "folder") ?>
    <div class="adverts-flexbox-item <?php echo $columns ?> <?php echo "adverts-category-slug-".$term->slug ?>">
        <span class="adverts-flexbox-wrap">
            <span class="adverts-flexbox-icon adverts-icon-<?php esc_html_e($icon) ?>"></span>
            <span class="adverts-flexbox-title">
                <a class="adverts-category-link" href="<?php esc_attr_e(get_term_link($term)) ?>"></a>
                <?php esc_html_e($term->name) ?>
                <?php if($show_count): ?>
                (<?php echo adverts_category_post_count( $term ) ?>)
                <?php endif; ?>
            </span>
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