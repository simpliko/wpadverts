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
    <a href="<?php esc_attr_e( remove_query_arg( array( 'edit', 'emaction' ) ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Email Templates", "wpadverts") ?></a>
    <a href="<?php esc_attr_e( add_query_arg( array('emaction'=>'options') ) ) ?>" class="nav-tab "><?php _e("Options", "wpadverts") ?></a>
</h2>
    
<?php adverts_admin_flash() ?>
    


<div class="tablenav">

<div class="alignleft actions">
    <form method="post" action="<?php esc_attr_e( add_query_arg( array( 'pg'=>null ) ) ) ?>">
        <select name="ftype" id="adext-emails-action1">
            <option value=""><?php _e("Filter By Module", "wpadverts") ?></option>
            <?php foreach( Adext_Emails::instance()->get_filter_options() as $opt ): ?>
            <option value="<?php echo esc_html( $opt["key"]) ?>" <?php selected( $opt["key"], adverts_request( "ftype" ) ) ?>><?php echo esc_html( $opt["label"] ) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="fnotify">
            <option value=""><?php _e( "Filter By Recipient", "wpadverts" ) ?></option>
            <option value="user" <?php selected( "user", adverts_request( "fnotify") ) ?>><?php _e( "User", "wpadverts" ) ?></option>
            <option value="admin" <?php selected( "admin", adverts_request( "fnotify") ) ?>><?php _e( "Administrator", "wpadverts" ) ?></option>
        </select>

        <input type="submit" class="button-secondary action" value="<?php _e("Filter", "wpadverts") ?>"/>
    </form>
</div>

<div class="clear"/>&nbsp;</div>

<table cellspacing="0" class="widefat post fixed">
    <?php foreach(array("thead", "tfoot") as $tx): ?>
    <<?php echo $tx; ?>>
        <tr>
            <th style="" class="" scope="col"><?php _e("Email Subject", "wpadverts") ?></th>
            <th style="width:100px" class="" scope="col"><?php _e("Recipient", "wpadverts") ?></th>
            <th style="min-width:65%" class="" scope="col" ><?php _e("Code", "wpadverts") ?></th>
            
            <th style="width:25px" class="" scope="col"><span class="dashicons dashicons-email" title="<?php esc_attr_e( "Message Enabled", "wpadverts" ) ?>"></span></th>
            <?php do_action('adext_emails_list_thead') ?>
        </tr>
    </<?php echo $tx; ?>>
    <?php endforeach; ?>

    <tbody>

        <?php 
            $z = 0; 
            $current_module = null;
            $opts = array();
            foreach(Adext_Emails::instance()->get_filter_options() as $opt ) {
                $opts[ $opt["key"] ] = $opt["label"];
            }
        ?>
        <?php foreach( $messages as $j => $message): ?>
        
        <?php list( $module_name, $email_name ) = explode( "::", $message["name"]); ?>
        <?php if( $current_module !== $module_name ): ?>
        <?php $current_module = $module_name ?>
        <tr valign="top" class="<?php if($z%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <th colspan="4" style="--background-color: white; border-bottom: 2px solid #666">
                <span style="font-size:16px; padding: 5px 0 5px 0; color: #32373c; font-weight: bold; display:inline-block">
                    <?php $mod = ( isset( $opts[ $current_module ] ) ) ? $opts[ $current_module ] : __( "Unknown", "wpadverts" ) ?>
                    <?php echo esc_html( sprintf( __( "Module / %s", "wpadverts" ),  $mod ) ) ?>
                </span>
            </th>
        </tr>
        <?php $z++; ?>
        <?php endif; ?>
        <tr valign="top" class="<?php if($z%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit">
            <td class="post-title column-title">
                <strong><a href="<?php echo esc_attr( add_query_arg( "edit", $message["name"] ) ) ?>" title=""><?php echo esc_html( $message["subject"] ) ?></a></strong>
            </td>
            <td>
                <?php if( $message["notify"] == "user" ): ?>
                <?php esc_html_e( "User", "wpadverts" ) ?>
                <?php elseif( $message["notify"] ): ?>
                <?php esc_html_e( "Administrator", "wpadverts" ) ?>
                <?php else: ?>
                <?php esc_html_e( "Other", "wpadverts" ) ?>
                <?php endif; ?>
            </td>
            <td style="">
                
                <code><?php echo $message["name"] ?></code> 
                <?php if( isset( $message["help"] ) ): ?>
                <a href="<?php echo esc_attr( $message["help"]) ?>" title="<?php esc_attr_e( "Read when this message is sent ...", "wpadverts" ) ?>">
                    <span class="dashicons dashicons-welcome-learn-more" style="font-size:22px"></span>
                </a>
                <?php endif; ?>
            </td>
            
            <td>
                <?php if( $message["enabled"] == "1" ): ?>
                <span class="dashicons dashicons-yes" style="font-size:23px"></span>
                <?php else: ?>
                <span class="dashicons dashicons-no" style="font-size:21px"></span>
                <?php endif; ?>
            </td>
            <?php do_action('adext_emails_list_tbody', $message) ?>
        </tr>
        <?php $z++; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="tablenav">


    <div class="alignleft actions">
        <form method="post" action="<?php esc_attr_e( add_query_arg( array( 'pg'=>null ) ) ) ?>">
            <select name="ftype" id="adext-emails-action2">
                <option selected="selected" value=""><?php _e("Filter By Module", "wpadverts") ?></option>
                <?php foreach( Adext_Emails::instance()->get_filter_options() as $opt ): ?>
                <option value="<?php echo esc_html( $opt["key"]) ?>" <?php selected( $opt["key"], adverts_request( "ftype" ) ) ?>><?php echo esc_html( $opt["label"] ) ?></option>
                <?php endforeach; ?>

            </select>

            <input type="submit" class="button-secondary action" value="<?php _e("Filter", "wpadverts") ?>"/>
        </form>
        <br class="clear"/>
    </div>

    <br class="clear"/>
</div>



</div>
