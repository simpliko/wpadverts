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
    <a href="<?php esc_attr_e( remove_query_arg( array( 'edit', 'adaction' ) ) ) ?>" class="nav-tab "><?php _e("Options", "adverts") ?></a>
    <a href="<?php esc_attr_e( add_query_arg( array('adaction'=>'list') ) ) ?>" class="nav-tab nav-tab-active">
        <?php _e("Pricing", "adverts") ?>
        <a class="add-new-h2" href="<?php esc_attr_e( add_query_arg( array( 'add'=>1 ) ) ) ?>"><?php _e("Add New") ?></a> 
    </a>
</h2>
    
<?php adverts_admin_flash() ?>
    
<script type="text/javascript">
    Wpjb.DeleteType = "<?php _e("listing", "adverts") ?>";
</script>

<form method="post" action="<?php esc_attr_e( add_query_arg( array( 'noheader'=>1, 'pg'=>null ) ) ) ?>" id="posts-filter">
<input type="hidden" name="noheader" value="1" />

<div class="tablenav">

<div class="alignleft actions">
    <select name="action" id="wpjb-action1">
        <option selected="selected" value=""><?php _e("Bulk Actions") ?></option>
        <option value="delete"><?php _e("Delete") ?></option>
    </select>

    <input type="submit" class="button-secondary action" id="wpjb-doaction1" value="<?php _e("Apply", "adverts") ?>"/>

</div>

<div class="clear"/>&nbsp;</div>

<table cellspacing="0" class="widefat post fixed">
    <?php foreach(array("thead", "tfoot") as $tx): ?>
    <<?php echo $tx; ?>>
        <tr>
            <td style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"/></td>
            <th style="" class="" scope="col"><?php _e("Title", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Price", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Visible", "adverts") ?></th>
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
                <strong><a title='<?php _e("Edit") ?>  "(<?php esc_attr_e($item->post_title) ?>)"' href="<?php esc_attr_e(add_query_arg('edit', $item->ID)) ?>" class=""><?php echo esc_html($item->post_title) ?></a></strong>
                <div class="row-actions">
                    <span class="edit"><a title="<?php _e("Edit") ?>" href="<?php esc_attr_e(add_query_arg('edit', $item->ID)) ?>"><?php _e("Edit") ?></a> | </span>
                    <span class=""><a href="<?php esc_attr_e( add_query_arg( array( "delete" => $item->ID,"noheader" => 1 ) ) ) ?>" title="<?php _e("Delete") ?>" class="adverts-delete"><?php _e("Delete") ?></a> | </span>
                </div>
            </td>
            
            <td class="">
                <?php $price = get_post_meta( $item->ID, 'adverts_price', true ) ?>
                <?php if($price): ?> 
                <?php echo adverts_price( $price ) ?>
                <?php else: ?>
                <?php _e("Free", "adverts") ?>
                <?php endif; ?>
            </td>
            <td class="">
                <?php $visible = get_post_meta( $item->ID, 'adverts_visible', true ) ?>
                <?php printf( _n( '1 day', '%s days', $visible, 'adverts' ), $visible ) ?>
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
            <option selected="selected" value=""><?php _e("Bulk Actions", "adverts") ?></option>
            <option value="delete"><?php _e("Delete", "adverts") ?></option>
        </select>
        <input type="submit" class="button-secondary action" id="wpjb-doaction2" value="<?php _e("Apply", "adverts") ?>"/>

        <br class="clear"/>
    </div>

    <br class="clear"/>
</div>


</form>

</div>
