<?php
/**
 * Privacy class; added to let customer export personal data
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Geoip Language Redirect
 * @version 1.0.4
 */

if ( ! defined( 'YITH_WCGEOIP_VERSION' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_Geoip_Privacy' ) ) {
	/**
	 * YITH Geoip Privacy class
	 *
	 * @since 1.0.4
	 */
	class YITH_Geoip_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * Constructor method
		 *
		 * @return \YITH_Geoip_Privacy
		 * @since 1.0.4
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH Geoip Redirect for WooCommerce', 'Privacy Policy Content', 'yith-geoip-language-redirect-for-woocommerce' ) );
		}

		/**
		 * Retrieves privacy example text for stripe plugin
		 *
		 * @return string Privacy message
		 * @since 1.0.4
		 */
		public function get_privacy_message( $section ) {
			$content = '';

			switch( $section ){
				case 'collect_and_store':
					$content =  '<p>' . __( 'While you visit our site, we’ll track:', 'yith-geoip-language-redirect-for-woocommerce' ) . '</p>' .
					            '<ul>' .
					            '<li>' . __( 'IP Address: this way we’ll be able to locate and redirect you accordingly to your location', 'yith-geoip-language-redirect-for-woocommerce' ) . '</li>' .
					            '</ul>' .
					            '<p>' . __( 'We’ll also use cookies to remember the last page you visited.', 'yith-geoip-language-redirect-for-woocommerce' ) . '</p>';
					break;
				case 'has_access':
				case 'share':
				case 'payments':
				default:
					break;
			}

			return apply_filters( 'yith_wcstripe_privacy_policy_content', $content, $section );
		}
	}
}