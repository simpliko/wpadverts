<?php
/**
 * Displays Payments History Page
 * 
 * This file is a template for wp-admin / Classifieds / Payment History panel. 
 * 
 * It is being loaded by adext_payments_page_history function.
 * 
 * @see adext_payments_page_history()
 * @since 0.1
 */
?>
<div class="wrap">
  
<h2 class="">
    <?php _e("Payment History", "adverts") ?>
</h2>
    
<?php adverts_admin_flash() ?>


<ul class="subsubsub">
    <li><a <?php if($filter == ""): ?>class="current"<?php endif; ?> href="<?php esc_attr_e(remove_query_arg('status')) ?>"><?php _e("All") ?></a><span class="count">(<?php echo array_sum($status_list) ?>)</span> | </li>
    <?php foreach($status_list as $status => $count): ?>
    <li><a <?php if($filter == $status): ?>class="current"<?php endif; ?> href="<?php esc_attr_e(add_query_arg(array('status'=>$status))) ?>"><?php esc_html_e(get_post_status_object($status)->label) ?></a><span class="count">(<?php echo (int)$count ?>)</span> | </li>
    <?php endforeach; ?>
</ul>

<form method="post" action="<?php esc_attr_e( add_query_arg( array( 'noheader'=>1, 'pg'=>null ) ) ) ?>" id="posts-filter">
<input type="hidden" name="noheader" value="1" />
    
<div class="tablenav">
    
<div class="alignleft actions">
    <select name="action" id="wpjb-action1">
        <option selected="selected" value=""><?php _e("Bulk Actions") ?></option>
        <?php foreach(array("pending", "completed", "failed", "refunded") as $status): ?>
        <option value="set-status-<?php echo $status ?>"><?php esc_html_e( sprintf( __("Set status: %s", "adverts"), get_post_status_object( $status )->label ) ) ?></option>
        <?php endforeach; ?>
        <option value="delete"><?php _e("Delete") ?></option>
    </select>

    <input type="submit" class="button-secondary action" id="wpjb-doaction1" value="<?php _e("Apply", "adverts") ?>"/>

</div>

<div class="alignleft actions">
    <select name="month">
        <option value=""><?php _e("All dates") ?></option>
        <option value="this-month" <?php selected($month, "this-month") ?>><?php _e("This month", "adverts") ?></option>
        <option value="last-month" <?php selected($month, "last-month") ?>><?php _e("Last month", "adverts") ?></option>
        <?php foreach($months as $m): ?>
        <option value="<?php esc_attr_e($m["value"]) ?>" <?php selected($month, $m["value"]) ?>><?php esc_html_e($m["label"]) ?></option>
        <?php endforeach; ?>
    </select>  
    <input type="submit" name="filter_action" id="post-query-submit" class="button" value="<?php _e("Filter") ?>" />
</div>
    
</div>
    
<table cellspacing="0" class="widefat post fixed">
    <?php foreach(array("thead", "tfoot") as $tx): ?>
    <<?php echo $tx; ?>>
        <tr>
            <td style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"/></td>
            <th style="" class="" scope="col"><?php _e("ID", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("User", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Email", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Date", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Amount", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Type", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Status", "adverts") ?></th>
            <?php do_action('adext_payment_list_thead') ?>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>
        <?php foreach($loop->posts as $i => $item): ?>
	<tr valign="top" class="<?php if($i%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <th class="check-column" scope="row">
                <input type="checkbox" value="<?php echo $item->ID ?>" name="item[]"/>
            </th>
            <td class="">
                <strong><a title='<?php _e("View Order", "adverts") ?>' href="<?php esc_attr_e(add_query_arg('edit', $item->ID)) ?>" class=""><?php echo esc_html("#".str_pad($item->ID, 6, "0", STR_PAD_LEFT)) ?></a></strong>
                <div class="row-actions" style="">
                    <span class="edit"><a href="<?php esc_attr_e(add_query_arg('edit', $item->ID)) ?>"><?php _e("View Order", "adverts") ?></a> | </span>
                    <span class=""><a href="<?php esc_attr_e( add_query_arg( array( "delete" => $item->ID,"noheader" => 1 ) ) ) ?>" title="<?php _e("Delete") ?>" class="adverts-delete"><?php _e("Delete") ?></a> | </span>
                </div>
            </td>
            
            <td class="">
                <?php esc_html_e($item->post_title) ?>
            </td>
            
            <td class="">
                <?php esc_html_e( get_post_meta( $item->ID, 'adverts_email', true ) ) ?>
            </td>
            
            <td>
                <?php echo date_i18n( get_option( 'date_format' ), get_post_time( 'U', false, $item->ID ) )  ?>
            </td>
            
            <td class="">
                <?php echo adverts_price( get_post_meta( $item->ID, '_adverts_payment_total', true ) ) ?>
            </td>  
            
            <td class="">
                <?php $listing_id = get_post_meta( $item->ID, "_adverts_pricing_id", true ); ?>
                <?php $listing = get_post( $listing_id ) ?>
                
                <?php if( $listing->post_type == "adverts-pricing" ): ?>
                <?php _e( "Posting", "adverts" ) ?>
                <?php elseif( $listing->post_type == "adverts-renewal" ): ?>
                <?php _e( "Renewal", "adverts" ) ?>
                <?php else: ?>
                —
                <?php endif; ?>
            </td>  

            <td>
                <?php $post_status = get_post_status_object( $item->post_status ) ?>
                <?php esc_html_e( $post_status->label ); ?>
            </td>
            <?php do_action('adext_payment_list_tbody', $item) ?>
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
            <?php foreach(array("pending", "completed", "failed", "refunded") as $status): ?>
            <option value="set-status-<?php echo $status ?>"><?php esc_html_e( sprintf( __("Set status: %s", "adverts"), get_post_status_object( $status )->label ) ) ?></option>
            <?php endforeach; ?>
            <option value="delete"><?php _e("Delete", "adverts") ?></option>
        </select>
        <input type="submit" class="button-secondary action" id="wpjb-doaction2" value="<?php _e("Apply", "adverts") ?>"/>

        <br class="clear"/>
    </div>

    <br class="clear"/>
</div>






</form>
    
</div>