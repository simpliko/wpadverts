<style type="text/css">
    .wpadverts-tl-row td {
    }
    .wpadverts-tl-row td strong {
        display: inline-block;
        line-height: 30px;
    }
    .wpadverts-tl-row td strong a {
        font-size: 15px;
    }
    .wpadverts-tl-col-actions {
        text-align: right;
    }
    
    .wpadverts-tl-row-inner {
        line-height: 30px
    }
</style>


<div class="wrap">
    
    <h2 class="nav-tab-wrapper">
        <a href="<?php esc_attr_e( remove_query_arg( array( 'adaction' ) ) ) ?>" class="nav-tab"><?php _e("Core Options", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'gallery') ) ) ?>" class="nav-tab "><?php _e("Gallery", "wpadverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('adaction' => 'types') ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Types", "wpadverts") ?></a>
    </h2>

    
    <?php adverts_admin_flash() ?>

    <?php foreach( $this->get_data_types() as $data_type_key => $data_type ): ?>
    <div>
        
    <h2 class="">
        <?php echo esc_html( $data_type["title"] ) ?>
        <!--a class="add-new-h2" href="#"><?php echo esc_html( $data_type["button_text"] ) ?></a-->
    </h2>
    <table cellspacing="0" class="widefat post fixed">
        <?php foreach(array("thead", "tfoot") as $tx): ?>
        <<?php echo $tx; ?>>
            <tr>
                <th style="" class="" scope="col"><?php _e("Name", "wpadverts") ?></th>
                <th style="" class="" scope="col">&nbsp;</th>
            </tr>
        </<?php echo $tx; ?>>
        <?php endforeach; ?>

        <tbody>
            <?php foreach($data_type["names"] as $i => $post_type): ?>
            <?php $data = get_post_type_object( $post_type ) ?>
            <tr valign="top" class="<?php if($i%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit wpadverts-tl-row">
                <td class="">

                    <strong>
                        <a title='<?php _e("Edit Classified Type", "wpadverts") ?>' href="<?php echo esc_attr(add_query_arg('edit-post', $post_type)) ?>" class="">
                            <?php echo esc_html( $data->label ) ?>
                        </a>
                    </strong>

                    &nbsp;

                    <code>
                        <?php echo $post_type ?>
                    </code>

                </td>

                <td class="wpadverts-tl-col-actions">
                    <a href="<?php echo esc_attr(add_query_arg('edit-post', $post_type)) ?>" class="button-secondary"><?php _e("Edit", "wpadverts") ?></a>
                    <a href="<?php echo $this->_get_post_type_restore_url( $post_type ) ?>" class="button-secondary"><?php _e("Restore Defaults", "wpadverts") ?></a>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
        
    </div>
    <?php endforeach; ?>

    <div>
    
    <h2 class="">
        <?php _e("Taxonomies", "wpadverts") ?>
    </h2>
    
    <table cellspacing="0" class="widefat post fixed">
        <?php foreach(array("thead", "tfoot") as $tx): ?>
        <<?php echo $tx; ?>>
            <tr>
                <th style="" class="" scope="col"><?php _e("Name", "wpadverts") ?></th>
                <th style="" class="" scope="col"><?php _e("Assigned To", "wpadverts" ) ?></th>
                <th style="" class="" scope="col">&nbsp;</th>
            </tr>
        </<?php echo $tx; ?>>
        <?php endforeach; ?>

        <tbody>
            <?php foreach($taxonomies as $i => $tax): ?>
            <?php $data = get_taxonomy( $tax ); ?>
            <tr valign="top" class="<?php if($i%2==0): ?>alternate <?php endif; ?>  author-self status-publish iedit wpadverts-tl-row">
                <td class="">

                    <strong class="wpadverts-tl-row-inner">
                        <a title='<?php _e("Edit Taxonomy", "wpadverts") ?>' href="<?php echo esc_attr(add_query_arg('edit-taxonomy', $data->name)) ?>" class="">
                            <?php echo esc_html( $data->label ) ?>
                        </a>
                    </strong>

                    &nbsp;

                    <code>
                        <?php echo esc_html( $data->name ) ?>
                    </code>

                </td>

                <td class="">
                    <div class="wpadverts-tl-row-inner">
                        <?php foreach( $data->object_type as $object ): ?>
                        <code class="wpadverts-tl-object-tag"><?php echo $object ?></code>
                        <?php endforeach; ?>
                    </div>
                </td>

                <td class="wpadverts-tl-col-actions">
                    <a href="<?php echo esc_attr( add_query_arg('edit-taxonomy', $data->name ) ) ?>" class="button-secondary"><?php _e("Edit", "wpadverts") ?></a>
                    <a href="<?php echo esc_attr( $this->_get_taxonomy_restore_url( $data->name ) ) ?>" class="button-secondary"><?php _e("Restore Defaults", "wpadverts") ?></a>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
    </div>
</div>