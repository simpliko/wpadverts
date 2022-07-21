<style type="text/css">
.wpa-block-categories-item {
    background: <?php echo $color_bg ?>;
    border-color: <?php echo $color_border ?>;
}
.wpa-block-categories-icon:before {
    color: <?php echo $color_icon ?>;
}
.wpa-block-categories-title,
.wpa-block-categories-title > a {
    color: <?php echo $color_text ?>
}
</style>

<?php if( ! empty( $terms ) ): ?>
<div class="atw-grid atw-gap-0 <?php echo "$class_margin_neg $class_cols" ?>">
    <?php foreach( $terms as $k => $term ): ?>
    <div class="wpa-block-categories-item  <?php echo "$item_display $class_margin" ?> atw-flex atw-rounded  atw-shadow-none atw-border atw-border-solid <?php echo esc_attr( "adverts-category-slug-".$term->slug ) ?>">
        <?php if( $show_icons ): ?>
            <?php $icon = adverts_taxonomy_get("advert_category", $term->term_id, "advert_category_icon", $default_icon ) ?>
            <?php $font_icon = apply_filters("wpadverts/block/categories/tpl/font-icon", adverts_guess_icon_class($icon), $term, "big"); ?>
            <?php if( is_string( $font_icon ) ): ?>
                <span class="wpa-block-categories-icon <?php echo "$class_icon_size $class_icon_padding $font_icon" ?>"></span>
            <?php endif; ?>
            <?php do_action( "wpadverts/block/categories/tpl/icon", $term, "big", $atts ) ?>
        <?php endif; ?>
        
        <span class="wpa-block-categories-title atw-font-bold atw-no-underline <?php echo "$class_item_padding" ?>">

            <a class="atw-font-bold atw-no-underline wpadverts-category-link" href="<?php echo esc_attr(get_term_link($term)) ?>">
                <?php echo esc_html($term->name) ?>
            </a>
            
            <?php if($show_count): ?>
                (<?php echo adverts_category_post_count( $term ) ?>)
            <?php endif; ?>
        </span>
    </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<div class="atw-p-3 atw-text-center"><?php _e( "No categories found.", "wpadverts" ) ?></div>
<?php endif; ?>
