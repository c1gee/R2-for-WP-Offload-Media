<?php
/**
 * Plugin Name: Cloudflare R2 Support for WP Offload Media
 * Description: Adds support for Cloudflare R2 custom endpoints in WP Offload Media
 * Version: 1.0.0
 * Author: Chris Gee
 * Author URI: https://www.rixxo.com
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Cloudflare R2 endpoint support to WP Offload Media
 */
class Cloudflare_R2_Support {
    
    public function __construct() {
        add_filter('as3cf_aws_init_service_client_args', array($this, 'add_r2_endpoint'), 10, 1);
        add_filter('as3cf_aws_init_client_args', array($this, 'add_r2_endpoint'), 10, 1);
    }
    
    /**
     * Add R2 endpoint to AWS client configuration
     */
    public function add_r2_endpoint($args) {
        // Get the R2 endpoint from AS3CF_SETTINGS
        $endpoint = $this->get_r2_endpoint();
        
        if (!empty($endpoint)) {
            $args['endpoint'] = $endpoint;
            
            // For R2, we need to ensure the region is set to 'auto' or a valid region
            if (empty($args['region'])) {
                $args['region'] = 'auto';
            }
            
            // R2 requires these specific settings for S3 compatibility
            $args['use_path_style_endpoint'] = true;
            $args['signature_version'] = 'v4';
            
            // Handle time skew issues that commonly occur with R2
            $args['http'] = array(
                'timeout' => 30,
                'connect_timeout' => 10,
            );
            
            // Disable SSL verification if needed (only for development/testing)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $args['http']['verify'] = false;
            }
            
            // Add retry configuration for R2
            $args['retries'] = array(
                'mode' => 'adaptive',
                'max_attempts' => 3,
            );
        }
        
        return $args;
    }
    
    /**
     * Get R2 endpoint from AS3CF_SETTINGS
     */
    private function get_r2_endpoint() {
        // Check if AS3CF_SETTINGS is defined
        if (!defined('AS3CF_SETTINGS')) {
            return '';
        }
        
        // Get the settings array
        $settings = maybe_unserialize(AS3CF_SETTINGS);
        
        // Check if it's an array and has an r2_endpoint
        if (is_array($settings) && isset($settings['r2_endpoint'])) {
            return $settings['r2_endpoint'];
        }
        
        return '';
    }
}

// Initialize the plugin
new Cloudflare_R2_Support();
