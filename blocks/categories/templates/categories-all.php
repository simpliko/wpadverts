<?php if( ! empty( $terms ) ): ?>
<div class="atw-grid atw-grid-cols-2 md:atw-grid-cols-4 atw--m-2">
    <?php foreach( $terms as $k => $term ): ?>
    <div class="atw-flex atw-rounded atw-m-2 atw-flex-col atw-bg-gray-100 atw-shadow-none atw-border atw-border-gray-200 atw-border-solid <?php echo esc_attr( "adverts-category-slug-".$term->slug ) ?>">
        <?php $icon = adverts_taxonomy_get("advert_category", $term->term_id, "advert_category_icon", "fa-folder") ?>
        <span class="atw-text-center atw-text-6xl atw-py-3 atw-text-red-600 <?php echo apply_filters("adverts_category_font_icon", adverts_guess_icon_class($icon), $term, "big") ?>"></span>
        <?php do_action( "adverts_category_pre_title", $term, "big") ?>
        <span class="atw-pb-3 atw-text-center atw-text-black atw-font-bold atw-no-underline">

            <a class="atw-text-black atw-font-bold atw-no-underline adverts-category-link" href="<?php echo esc_attr(get_term_link($term)) ?>">
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
