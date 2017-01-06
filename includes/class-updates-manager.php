<?php
/**
 * Updates Manager Class
 * 
 * This class is being used by all premium Adverts addon in order to provide
 * license handling and validation, as well as providing updates from third
 * party site.
 *
 * @package Adverts
 * @subpackage Classes
 * @since 1.0
 * @access public
 */

class Adverts_Updates_Manager
{
    /**
     * URL to API server handling updates.
     *
     * @var string
     */
    public static $url = "https://wpadverts.com/api";
    
    /**
     * Installed addon version
     *
     * @var string
     */
    public $version = null;
    
    /**
     * Plugin name (for example my-plugin/my-plugin.php)
     *
     * @var string
     */
    public $plugin = null;
    
    /**
     * Plugin slug (for example my-plugin)
     * 
     * This most of the time will be the plugin directory.
     *
     * @var string
     */
    public $slug = null;
    
    /**
     * Addon license key
     *
     * @var string
     */
    public $license = null;
    
    /**
     * Construct Updates Manager
     * 
     * Each Adverts addon creates his own instance of this class.
     * 
     * @since 1.0
     * @access public
     * @param string $plugin Plugin name (for example my-plugin/my-plugin.php)
     * @param string $slug Plugin slug, usually plugin directory (for example my-plugin)
     * @param string $version Plugin files version
     * @param string $license License key
     */
    public function __construct($plugin, $slug, $version, $license = null) {
        
        $config = adverts_config( 'config.license' );
        
        if( $license === null && isset($config[$slug]) ) {
            $license = $config[$slug];
        }
        
        $this->plugin = $plugin;
        $this->slug = $slug;
        $this->version = $version;
        $this->license = $license;
    }
    
    /**
     * Initializes updates and license handling
     * 
     * This function registers all required filters and actions to handle updates
     * and license.
     * 
     * @see after_plugin_row_$plugin filter
     * @see pre_set_site_transient_update_plugins filter
     * @see plugins_api filter
     * 
     * @since 1.0
     * @access public
     * @return void
     */
    public function connect() {
        
        $transient = get_site_transient("update_plugins");
        
        add_action('wp_ajax_adverts_license_validate_'.$this->slug, array($this, "validate_license"));
        add_filter("after_plugin_row_".$this->plugin, array($this, "plugin_notice"));
        add_filter("plugin_action_links_".$this->plugin, array($this, "plugin_action_links"));
        
        if( $this->license ) {
            add_filter('pre_set_site_transient_update_plugins', array($this, 'check'));
            add_filter('plugins_api', array($this, 'info'), 10, 3);
        }

        if(isset($transient->response[$this->plugin]) && $transient->response[$this->plugin]->downloads < 0) {
            //add_filter("after_plugin_row_".$this->plugin, array($this, "plugin_notice"));
        }     
        
    }
    
    public function plugin_action_links( $actions ) {
        
        if( $this->license ) {
            $actions["license"] = '<a href="#" title="" class="adverts-license-link"><span class="dashicons dashicons-lock"></span>'.__("License", "adverts").'</a>';
        }
        
        return $actions;
    }
    
    /**
     * AJAX License validation
     * 
     * This function is executed from /wp-admin/admin-ajax.php it handles
     * license key validation.
     * 
     * @see wp_ajax_adverts_license_validate_$slug action
     * @see self::connect()
     * 
     * @access public
     * @since 1.0
     * @return void
     */
    public function validate_license() {
        
        $license = trim( adverts_request("license") );
        $update = false;
        
        $response = new stdClass();
        $response->status = 200;
        $response->error = "";
        $response->message = "";
        
        if( !empty($license) ) {
            $request = $this->remote("license", array("license"=>$license));   
            
            if($request === false) {
                $response->status = 400;
                $response->message = __("Cannot connect to remote server. Please try again later.", "adverts");
            } elseif($request->result == 1) {
                $update = true;
            } else {
                $response->status = $request->result;
                $response->message = $request->message;
            }
            
        } else {
            
            $license = null;
            $update = true;
        }
        
        if($update) {
            $config = adverts_config( 'config.license' );
            $config[$this->slug] = $license;
            adverts_config_set( 'config.license', $config );
            adverts_config_save('config' );
        }
        
        $transient = get_site_transient("update_plugins");

        if($update && $license === null && isset($transient->response[$this->plugin])) {
            unset($transient->checked[$this->plugin]);
            unset($transient->response[$this->plugin]);
            set_site_transient("update_plugins", $transient);
        }
        
        echo json_encode( $response );
        exit;
    }
    
    /**
     * Executes request to remote updates server.
     * 
     * @access public
     * @since 1.0
     * @param string $action API Action (one of: info, version, license)
     * @param array $args Additional request params
     * @return mixed Array with response data or boolean false on fail
     */
    public function remote($action, $args = array()) {
        
        $url = trim(self::$url, "/")."/".$this->slug."/".$action;
        
        $args["site_url"] = get_bloginfo("url");
        $args["site_version"] = $this->version;
        
        if(!isset($args["license"])) {
            $args["license"] = $this->license;
        }
        
        $request = wp_remote_post($url, array("body"=>$args));

        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return json_decode($request["body"]);
	} else {
            return false;
        }
    }
    
    /**
     * Check updates for current plugin
     * 
     * This function performs a GET request to check if there is an update for
     * currently checked addon. It is run via filter when WordPress is
     * collecting data about plugin versions.
     * 
     * @see pre_set_site_transient_update_plugins filter
     * 
     * @access public
     * @since 1.0
     * @param stdClass $transient Plugins transient data
     * @return stdClass Updated transient data
     */
    public function check($transient)
    {
        if (empty($transient->checked) && isset($transient->response[$this->plugin])) {
            return $transient;
        }

        $remote = $this->remote("version");

        if($remote === false || $remote->result == 0) {
            return $transient;
        }
        
        $obj = new stdClass();
        $obj->plugin = $this->plugin;
        $obj->slug = $this->slug;
        $obj->new_version = $remote->data->version;
        $obj->url = self::$url."/".$this->slug."/download/?license=".$this->license;
        $obj->package = self::$url."/".$this->slug."/download/?license=".$this->license;
        $obj->downloads = $remote->data->downloads;
        
        if (version_compare($this->version, $remote->data->version, '<')) {
            $transient->response[$this->plugin] = $obj;
        }
        
        return $transient;
    }

    /**
     * Add our self-hosted description to the filter
     * 
     * This function performs GET request to get information about the available
     * update. In other words data that you can see after clicking "view version x.x.x details".
     *
     * @see plugins_api filter
     * 
     * @param boolean $false
     * @param array $action
     * @param object $arg
     * @return bool|object
     */
    public function info($false, $action, $arg)
    {
        if (!isset($arg->slug) || $arg->slug != $this->slug) {
            return false;
        }
        
        $request = $this->remote("info", array("license"=>$this->license));
        
        if(is_object($request) && isset($request->data)) {
            
            $data = $request->data;
            $data->sections = (array)$data->sections;
            
            return $data;
        } else {
            return false;
        }
    }
    
    /**
     * Displays license data in wp-admin/plugins.php list.
     * 
     * This function is executed via filter and displays addon license
     * information below plugin row.
     * 
     * @see after_plugin_row_$plugin filter
     * 
     * @access public
     * @since 1.0
     * @param mixed $param Don't know, unused anyway.
     * @return void
     */
    public function plugin_notice($param)
    {
        wp_enqueue_script( 'adverts-admin-updates' );
        wp_enqueue_style( 'adverts-admin-updates' );
        
        $transient = get_site_transient("update_plugins");
        $tkey = null;
        
        if(isset($transient->response[$this->plugin])) {
            $tkey = $transient->response[$this->plugin]->downloads;
        }

        ?>

        <?php if(!$this->license): ?>
        <tr class="tr plugin-update-tr adverts-update-row adverts-update-row-license" data-slug="<?php echo $this->slug ?>">
            <td class="plugin-update" colspan="3">
                <div class="adverts-update-notice adverts-update-activate">
                    <input type="text" name="license" placeholder="<?php _e("License ...", "adverts") ?>" />
                    <a href="#" class="button-secondary adverts-update-activate-button"><?php _e("Activate") ?></a>
                    <img src="<?php echo admin_url() ?>/images/wpspin_light-2x.gif" alt="" class="adverts-update-loader adverts-off" />
                    &nbsp;
                    <span><em><?php _e("Activate your License in order to enable automatic updates.", "adverts") ?></em></span>
                </div>
            </td>
        </tr>
        
        <?php else: ?>

        <tr class="tr plugin-update-tr adverts-update-row adverts-off adverts-update-row-license-change" data-slug="<?php echo $this->slug ?>">
            <td class="plugin-update" colspan="3">
                <div class="adverts-update-notice adverts-update-registered">
                    <span class="adverts-inline-edit">
                        <input type="text" name="license" placeholder="<?php _e("License ...", "adverts") ?>" value="<?php echo esc_attr($this->license) ?>" data-value="<?php echo esc_attr($this->license) ?>" /> 
                        <a href="#" class="button-secondary adverts-update-activate-button"><?php _e("Update") ?></a>
                        <a href="#" class="button-secondary adverts-update-button-cancel"><?php _e("Cancel") ?></a>
                        <img src="<?php echo admin_url() ?>/images/wpspin_light-2x.gif" alt="" class="adverts-update-loader adverts-off" />
                    </span>
                </div>
                
            </td>
        </tr>
        
        <?php endif; ?> 
        
        <?php /*if($tkey < 0): ?>
        <tr class="tr plugin-update-tr" data-slug="<?php echo $this->slug ?>">
            <td class="plugin-update" colspan="3">
                <div class="update-message">
                    <span class="adverts-update-error">
                        <?php if( $tkey == -2 ): ?>
                        <?php _e("<strong>Cannot update!</strong> License expired. It seems you last renewed your license over a year ago.", "adverts") ?>
                        <?php elseif( $tkey == -3 ): ?>
                        <?php _e("<strong>Cannot update!</strong> License expired. It seems you last renewed your license over a year ago.", "adverts") ?>
                        <?php endif; ?>
                    </span>
                </div>
            </td>
        </tr>


        <?php endif;*/ ?> 
        
        <?php

    }
    
    
    /**
     * Sorting function
     * 
     * Sorts $a and $b by version number. This is used internally 
     * in update() function.
     * 
     * @access protected
     * @since 1.0
     * @param mixed $a
     * @param mixed $b
     * @return int 
     */
    protected static function sort($a, $b) 
    {
        return version_compare($a, $b);
    }
    
    /**
     * Updating function
     * 
     * This function will be used in future to execute small migration /
     * upgrade tasks when addon is upgraded to latest version.
     * 
     * @throws Exception Thats all it does
     */
    public function update()
    {
        throw new Exception("To be implemented in future.");
    }
    



}

?>
