<?php
/**
 * Plugin Name: YITH GeoIP Language Redirect for WooCommerce
 * Plugin URI: https://yithemes.com/themes/plugins/yith-geoip-language-redirect-for-woocommerce/
 * Description: Redirect IP from page to specific pages.
 * Author: YITHEMES
 * Text Domain: yith-geoip-language-redirect-for-woocommerce
 * Version: 1.0.4
 * Author URI: http://yithemes.com/
 * WC requires at least: 2.6.4
 * WC tested up to: 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$wp_upload_dir = wp_upload_dir();
/* === DEFINE === */
! defined( 'YITH_WCGEOIP_VERSION' )              && define( 'YITH_WCGEOIP_VERSION', '1.0.4' );
! defined( 'YITH_WCGEOIP_INIT' )                 && define( 'YITH_WCGEOIP_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCGEOIP_SLUG' )                 && define( 'YITH_WCGEOIP_SLUG', 'yith-geoip-language-redirect-for-woocommerce' );
! defined( 'YITH_WCGEOIP_SECRETKEY' )            && define( 'YITH_WCGEOIP_SECRETKEY', 'ydq1FqEKDQkIgcgfInrd' );
! defined( 'YITH_WCGEOIP_FILE' )                 && define( 'YITH_WCGEOIP_FILE', __FILE__ );
! defined( 'YITH_WCGEOIP_PATH' )                 && define( 'YITH_WCGEOIP_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCGEOIP_TEMPLATE_PATH' )        && define( 'YITH_WCGEOIP_TEMPLATE_PATH', YITH_WCGEOIP_PATH . 'templates/' );
! defined( 'YITH_WCGEOIP_OPTIONS_PATH' )         && define( 'YITH_WCGEOIP_OPTIONS_PATH', YITH_WCGEOIP_PATH . 'panel' );
! defined( 'YITH_WCGEOIP_URL' )                  && define( 'YITH_WCGEOIP_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCGEOIP_ASSETS_URL' )           && define( 'YITH_WCGEOIP_ASSETS_URL', YITH_WCGEOIP_URL . 'assets/' );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCGEOIP_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WCGEOIP_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCGEOIP_PATH  );

/* Load YWCM text domain */
load_plugin_textdomain( 'yith-geoip-language-redirect-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if ( ! function_exists( 'YITH_Geoip' ) ) {
    /**
     * Unique access to instance of YITH_Vendors class
     *
     * @return YITH_Geoip | YITH_Geoip_Premium
     * @since 1.0.0
     */
    function YITH_Geoip() {
        // Load required classes and functions

        require_once( YITH_WCGEOIP_PATH . 'includes/class.yith-geoip-language-redirect.php' );
        return YITH_Geoip::instance();
    }
}

/**
 * Instance main plugin class
 */
if(class_exists('WooCommerce')) {
    YITH_Geoip();
}
