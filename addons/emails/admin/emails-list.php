<?php
/**
 * Displays Email Templates List Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Emails / Email Templates panel. 
 * 
 * It is being loaded by Adext_Emails_Admin::list() function.
 * 
 * @see Adext_Emails_Admin::list()
 * @since 1.3
 */
?>
<div class="wrap">
  
<h2 class="nav-tab-wrapper">
    <a href="<?php esc_attr_e( remove_query_arg( array( 'edit', 'emaction' ) ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Email Templates", "adverts") ?></a>
    <a href="<?php esc_attr_e( add_query_arg( array('emaction'=>'options') ) ) ?>" class="nav-tab "><?php _e("Options", "adverts") ?></a>
</h2>
    
<?php adverts_admin_flash() ?>
    
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
            <th style="" class="" scope="col"><?php _e("Email Subject", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Label", "adverts") ?></th>
            <th style="" class="" scope="col"><?php _e("Recipient", "adverts") ?></th>
            <th style="width:25px" class="" scope="col"><span class="dashicons dashicons-email"></span></th>
            <?php do_action('adext_emails_list_thead') ?>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>
        <?php $z = 0; ?>
        <?php foreach( $messages->get_messages() as $j => $message): ?>
        <tr valign="top" class="<?php if($z%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <td class="post-title column-title">
                <strong><a href="<?php echo esc_attr( add_query_arg( "edit", $message["name"] ) ) ?>" title=""><?php echo esc_html( $message["subject"] ) ?></a></strong>
            </td>
            <td>
                <?php echo esc_html( $message["label"] ) ?>
            </td>
            <td>
                <?php if( $message["notify"] == "admin" ): ?>
                admin@example.com
                <?php else: ?>
                <?php _e( "User", "adverts" ) ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if( $message["enabled"] == "1" ): ?>
                <span class="dashicons dashicons-yes" style="font-size:23px"></span>
                <?php else: ?>
                <span class="dashicons dashicons-no" style="font-size:21px"></span>
                <?php endif; ?>
            </td>
            <?php do_action('adext_emails_list_tbody', $item) ?>
        </tr>
        <?php $z++; ?>
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
