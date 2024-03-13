<?php
/**
 * Displays Payment Pricing List Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Payments / Pricing panel. 
 * 
 * It is being loaded by adext_payments_page_pricing function.
 * 
 * @see adext_payments_page_pricing()
 * @since 0.1
 */
?>
<div class="wrap">
  
<h2 class="nav-tab-wrapper">
    <a href="<?php echo esc_attr( remove_query_arg( array( 'edit', 'adaction' ) ) ) ?>" class="nav-tab "><?php _e("Options", "wpadverts") ?></a>
    <a href="<?php echo esc_attr( add_query_arg( array('adaction'=>'list') ) ) ?>" class="nav-tab nav-tab-active">
        <?php _e("Pricing", "wpadverts") ?>
        <a class="add-new-h2" href="<?php echo esc_attr( add_query_arg( array( 'add'=>1 ) ) ) ?>"><?php _e("Add New") ?></a> 
    </a>
</h2>
    
<?php adverts_admin_flash() ?>
    
<form method="post" action="<?php echo esc_attr( add_query_arg( array( 'noheader'=>1, 'pg'=>null ) ) ) ?>" id="posts-filter">
<?php wp_nonce_field( "wpadverts-pricing-bulk-action", "_nonce" ) ?>
<input type="hidden" name="noheader" value="1" />

<div class="tablenav">

<div class="alignleft actions">
    <select name="action" id="wpjb-action1">
        <option selected="selected" value=""><?php _e("Bulk Actions") ?></option>
        <option value="delete"><?php _e("Delete") ?></option>
    </select>

    <input type="submit" class="button-secondary action" id="wpjb-doaction1" value="<?php _e("Apply", "wpadverts") ?>"/>

</div>

<div class="clear">&nbsp;</div>

<table cellspacing="0" class="widefat post fixed">
    <?php foreach(array("thead", "tfoot") as $tx): ?>
    <<?php echo $tx; ?>>
        <tr>
            <td style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"/></td>
            <th style="" class="" scope="col"><?php _e("Title", "wpadverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Type", "wpadverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Price", "wpadverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Visible", "wpadverts") ?></th>
            <?php do_action('adext_pricing_list_thead') ?>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>
        <?php foreach($loop->posts as $i => $item): ?>
	<tr valign="top" class="<?php if($i%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <th class="check-column" scope="row">
                <input type="checkbox" value="<?php echo $item->ID ?>" name="item[]"/>
            </th>
            <td class="post-title column-title">
                <strong><a title='<?php _e("Edit") ?>  "(<?php echo esc_attr($item->post_title) ?>)"' href="<?php echo esc_attr(add_query_arg('edit', $item->ID)) ?>" class=""><?php echo esc_html($item->post_title) ?></a></strong>
                <div class="row-actions">
                    <span class="edit"><a title="<?php _e("Edit") ?>" href="<?php echo esc_attr(add_query_arg('edit', $item->ID)) ?>"><?php _e("Edit") ?></a> | </span>
                    <span class=""><a href="<?php echo esc_attr( add_query_arg( array( "delete" => $item->ID,"noheader" => 1, "_nonce" => wp_create_nonce( sprintf( "delete-pricing-%d", $item->ID ) ) ) ) ) ?>" title="<?php _e("Delete") ?>" class="adverts-delete"><?php _e("Delete") ?></a> | </span>
                </div>
            </td>
            
            <td class="">
                <?php if( $item->post_type == "adverts-pricing" ): ?>
                <?php _e( "New Post", "wpadverts" ) ?>
                <?php elseif( $item->post_type == "adverts-renewal" ): ?>
                <?php _e( "Renewal", "wpadverts" ) ?>
                <?php endif; ?>
            </td>
            
            <td class="">
                <?php $price = get_post_meta( $item->ID, 'adverts_price', true ) ?>
                <?php if($price): ?> 
                <?php echo adverts_price( $price ) ?>
                <?php else: ?>
                <?php _e("Free", "wpadverts") ?>
                <?php endif; ?>
            </td>

            <td class="">
                <?php $visible = get_post_meta( $item->ID, 'adverts_visible', true ) ?>
                <?php printf( _n( '1 day', '%s days', $visible, "wpadverts" ), $visible ) ?>
            </td>
            
            <?php do_action('adext_pricing_list_tbody', $item) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="tablenav">
    <div class="tablenav-pages">
        <?php
            echo paginate_links( array(
                'base' => remove_query_arg('pg') . "%_%",
                'format' => '&pg=%#%',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'current' => max( 1, adverts_request( 'pg', 1 ) ),
                'total' => $loop->max_num_pages,
            ));
        ?>
    </div>


    <div class="alignleft actions">
        <select name="action2" id="wpjb-action2">
            <option selected="selected" value=""><?php _e("Bulk Actions", "wpadverts") ?></option>
            <option value="delete"><?php _e("Delete", "wpadverts") ?></option>
        </select>
        <input type="submit" class="button-secondary action" id="wpjb-doaction2" value="<?php _e("Apply", "wpadverts") ?>"/>

        <br class="clear"/>
    </div>

    <br class="clear"/>
</div>


</form>

</div>
