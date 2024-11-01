<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

function yith_wcgeoip_print_error( $text ) {
	error_log( print_r( $text, true ) );
}

/**
 * Get template for GeoIP Language Redirect plugin
 *
 * @param $filename string Template name (with or without extension)
 * @param $args     mixed Array of params to use in the template
 * @param $section  string Subdirectory where to search
 */

function yith_wcgeoip_get_template( $filename, $args = array(), $section = '' ) {
	$ext = strpos( $filename, '.php' ) === false ? '.php' : '';

	$template_name = $section . '/' . $filename . $ext;
	$template_path = WC()->template_path() . 'yith-wcgeoip/';
	$default_path  = YITH_WCGEOIP_TEMPLATE_PATH;

	if ( defined( 'YITH_WCGEOIP_PREMIUM' ) ) {
		$premium_template = str_replace( '.php', '-premium.php', $template_name );
		$located_premium  = wc_locate_template( $premium_template, $template_path, $default_path );
		$template_name    = file_exists( $located_premium ) ? $premium_template : $template_name;
	}

	wc_get_template( $template_name, $args, $template_path, $default_path );
}


function yith_wcgeoip_get_status_list() {
	$status_list = array();

	$status_list[302] = array(
		'code'     => 302,
		'desc'     => _x( 'Found', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	$status_list[300] = array(
		'code'     => 300,
		'desc'     => _x( 'Multiple choice', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	$status_list[301] = array(
		'code'     => 301,
		'desc'     => _x( 'Moved Permanently', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	$status_list[303] = array(
		'code'     => 303,
		'desc'     => _x( 'See Other', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	$status_list[304] = array(
		'code'     => 304,
		'desc'     => _x( 'Not Modified', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	$status_list[305] = array(
		'code'     => 305,
		'desc'     => _x( 'Use Proxy', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	$status_list[306] = array(
		'code'     => 306,
		'desc'     => _x( 'Switch Proxy', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	$status_list[307] = array(
		'code'     => 307,
		'desc'     => _x( 'Temporary Redirect', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	$status_list[308] = array(
		'code'     => 308,
		'desc'     => _x( 'Permanent Redirect', 'HTTP Status code, no transalation needed', 'yith-geoip-language-redirect-for-woocommerce' ),
		'protocol' => 'HTTP/1.1'
	);

	return $status_list;
}

function yith_wcgeoip_get_current_ip() {
	$result = false;
	foreach (
		array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR'
		) as $key
	) {
		if ( array_key_exists( $key, $_SERVER ) === true ) {
			foreach ( array_map( 'trim', explode( ',', $_SERVER[ $key ] ) ) as $ip ) {
				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
					$result = $ip;
				}
			}
		}
	}

	return apply_filters( 'yith_wcgeoip_customer_ip', $result );
}