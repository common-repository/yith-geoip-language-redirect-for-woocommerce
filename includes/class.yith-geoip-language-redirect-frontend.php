<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGEOIP_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Geoip_Frontend
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mateo
 *
 */

if ( ! class_exists( 'YITH_Geoip_Frontend' ) ) {
	/**
	 * Class YITH_Geoip_Frontend
	 *
	 * @author Francisco Mateo
	 */
	class YITH_Geoip_Frontend {

		protected $geoip_rules = null;
		private $status_code = null;

		/**
		 * Construct
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 */
		public function __construct() {
			$this->geoip_rules = new YITH_Geoip_Rules();
			add_action( 'template_redirect', array( $this, 'init_redirect_template' ) );
			add_action( 'init', array( $this, 'cookie' ) );

			$check_attachment = apply_filters( 'yith_geoip_check_attachment', true );

			if ( $check_attachment ) {
				add_filter( 'wp_get_attachment_url', array( $this, 'check_attachment_url' ), 10, 2 );
				add_filter( 'wp_video_shortcode_override', array( $this, 'check_attachment_video_or_audio' ), 10, 4 );
				add_filter( 'wp_audio_shortcode_override', array( $this, 'check_attachment_video_or_audio' ), 10, 4 );
				//add_filter('wp_get_attachment_image_attributes', array($this, 'check_attachment_image_attr'), 10, 3);
				add_filter( 'wp_get_attachment_image_src', array( $this, 'check_attachment_image_src' ), 10, 4 );
			}

		}

		public function init_redirect_template() {
			$queried_object = get_queried_object();
			//1º Get info current template loading.
			$current_template = $this->get_template_info( $queried_object );

			if ( ! empty( $current_template['template_type'] ) ) {
				//2º Get country customer.
				$customer = $this->get_country_customer();

				//3º Search rule.
				$rule = $this->get_geoip_rule( $current_template, $customer );

				//5º Redirect to rule conditions.
				if ( ! empty( $rule ) ) {
					//But Wait! just to redirect we should be check once redirect :)
					$redirect = $this->once_redirect( $rule );
					//Oh! We need check the device to redirect to!!!
					$device = $this->device_redirect( $rule );

					if ( $redirect & $device ) {
						$this->redirect_template_to( $rule, $current_template );
					}
				}
			}
		}

		public function get_template_info( $item ) {
			global $wp_query;
			$info_template                  = array();
			$info_template['template_type'] = $this->get_template_type();
			$info_template['url']           = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$info_template['rel_url']       = add_query_arg( null, null );
			$info_template['rel_name']      = '';
			$info_template['origin_type']   = '';
			switch ( $info_template['template_type'] ) {
				case 'page':
					$info_template['id']          = $item->ID;
					$info_template['rel_name']    = $item->post_name;
					$info_template['origin_type'] = $item->post_type;
					$info_template['qo']          = $item;
					break;
				case 'single':
					$info_template['id']          = $item->ID;
					$info_template['rel_name']    = $item->post_name;
					$info_template['origin_type'] = $item->post_type;
					$info_template['qo']          = $item;
					break;
				case 'category':
					$info_template['id']          = $item->term_id;
					$info_template['rel_name']    = $item->slug;
					$info_template['origin_type'] = $item->taxonomy;
					break;
				case 'tag':
					$info_template['id']          = $item->term_id;
					$info_template['rel_name']    = $item->slug;
					$info_template['origin_type'] = $item->taxonomy;
					break;
				case 'tax':
					$info_template['id']          = $item->term_id;
					$info_template['rel_name']    = $item->slug;
					$info_template['origin_type'] = $item->taxonomy;
					break;
				case 'date':
					$query_vars                = $wp_query->query_vars;
					$info_template['day']      = isset( $query_vars['day'] ) ? $query_vars['day'] : false;
					$info_template['monthnum'] = isset( $query_vars['monthnum'] ) ? $query_vars['monthnum'] : false;
					$info_template['year']     = isset( $query_vars['year'] ) ? $query_vars['year'] : false;
					break;
				case 'archive':
					$info_template['rel_name'] = $item->name;
					break;
			}

			return $info_template;
		}

		public function get_country_customer() {
			//$ip_address = WC_Geolocation::get_external_ip_address();
			$ip_address  = yith_wcgeoip_get_current_ip();
			$geolocation = WC_Geolocation::geolocate_ip( $ip_address );

			return $customer_info = array(
				'ip_address'  => $ip_address,
				'geolocation' => $geolocation
			);
		}

		public function get_geoip_rule( $template, $customer ) {
			$customer_country = $customer['geolocation']['country'];

			$rule_custom = array();
			$rule_custom = apply_filters( 'yith_wcgeoip_check_before_custom_url', $rule_custom, $customer_country, $template );
			if ( ! empty( $rule_custom ) ) {
				return $rule_custom;
			}

			// Find rule for custom_url from specific country.
			$rule_custom_url = $this->check_custom_url( $customer_country, $template['url'] );
			if ( ! empty( $rule_custom_url ) ) {
				return $rule_custom_url;
			}

			$rule_after_custom_url = array();
			$rule_after_custom_url = apply_filters( 'yith_wcgeoip_check_after_custom_url', $rule_after_custom_url, $customer_country, $template );
			if ( ! empty( $rule_after_custom_url ) ) {
				return $rule_after_custom_url;
			}

			return array();
		}

		//Hey listen! Once redirect only redirect the page once time until time defined by cookie! x_x
		public function once_redirect( $rule ) {
			$redirect = true;
			if ( $rule['only_one'] ) {
				if ( ! isset( $_COOKIE[ 'yith_geoip_cookies_once_redirect_' . $rule['ID'] ] ) ) {
					$expire = apply_filters( 'yith_wcgeoip_expire_cookie_duration', 0 );
					wc_setcookie( 'yith_geoip_cookies_once_redirect_' . $rule['ID'], $rule['ID'], $expire );
				} else {
					$redirect = false;
				}
			}

			return $redirect;
		}

		public function device_redirect( $rule ) {
			$redirect = true;
			switch ( $rule['device'] ) {
				case 'mobile':
					$redirect = wp_is_mobile();
					break;
				case 'desktop':
					$redirect = ( wp_is_mobile() ) ? false : true;
					break;

			}

			return $redirect;
		}

		public function check_custom_url( $country, $url ) {
			$args = array(
				'country'     => $country,
				'origin_type' => 'custom_url',
				'origin'      => $url
			);

			$rule = $this->geoip_rules->get_rules( $args );

			if ( ! empty( $rule ) ) {
				$rule = $rule[0];
			}

			return $rule;
		}

		public function check_attachment( $url ) {
			$exist_rule = false;
			if ( ! is_admin() ) {
				$customer         = $this->get_country_customer();
				$customer_country = $customer['geolocation']['country'];

				$args = array(
					'country'     => $customer_country,
					'origin_type' => 'custom_url',
					'origin'      => $url
				);

				$rule = $this->geoip_rules->get_rules( $args );

				//Find for especific attachment...
				if ( empty( $rule ) ) {
					$args['origin_type'] = 'attachment';
					$rule                = $this->geoip_rules->get_rules( $args );
				}
				//Find for all_attachment...
				if ( empty( $rule ) ) {
					$args ['origin_type'] = 'all_attachment';
					unset( $args['origin'] ); //unset orign to only match between country and origin type.
					$rule = $this->geoip_rules->get_rules( $args );
				}

				if ( ! empty( $rule ) ) {
					$rule       = $rule[0];
					$exist_rule = ( ! empty( $rule ) ) ? $rule['destination'] : false;
				}
			}

			return $exist_rule;
		}

		public function check_attachment_url( $url ) {
			$new_url = $url;
			if ( ! is_admin() ) {
				$destination = $this->check_attachment( $url );
				$new_url     = ( $destination ) ? $destination : $url;
			}

			return apply_filters( 'yith_wcgeoip_check_attachment_url', $new_url );
		}

		public function check_attachment_video_or_audio( $override, $attr, $content, $instance ) {
			if ( ! is_admin() ) {
				$index_url = array();
				foreach ( $attr as $key => $attr_item ) {
					$ext_item = substr( $attr_item, strpos( $attr_item, '.' ) + 1, strlen( $attr_item ) );
					if ( $key == $ext_item ) {
						$index_url[] = $key;
					}
				}
				$index_changed = '';
				$media_type    = '';
				foreach ( $index_url as $prev_key_url ) {
					$prev_url = $attr[ $prev_key_url ];
					$new_url  = $this->check_attachment_url( $prev_url );
					if ( $prev_url != $new_url ) {
						$media_info = wp_check_filetype( $new_url );
						$media_ext  = $media_info['ext'];
						$media_type = substr( $media_info['type'], 0, strpos( $media_info['type'], '/' ) );

						//Check if ext value exist on attr array
						$exist_key_array = ! empty( $media_ext ) ? array_key_exists( $media_ext, $attr ) ? true : false : false;
						if ( $exist_key_array ) {
							//Exist override him
							$attr[ $media_ext ] = $new_url;
						} else {
							//No exist create a new and delete the older.
							$attr[ $media_ext ] = $new_url;
							unset( $attr[ $prev_key_url ] );
						}

						$index_changed = $media_ext;
					}
				}

				if ( 'image' == $media_type ) {
					$url      = $attr[ $index_changed ];
					$id_image = $this->get_id_image_by_url( $url );

					return wp_get_attachment_image( $id_image, 'full', false, $attr );
				}
				if ( 'audio' == $media_type || 'video' == $media_type ) {
					$function = 'wp_' . $media_type . '_shortcode';

					return $function( $attr, $content );
				}
			}

			return apply_filters( 'yith_wcgeoip_check_attachment_video_or_audio', $override );
		}

		public function check_attachment_image_attr( $attr, $attachment, $size ) {

			if ( ! key_exists( 'srcset', $attr ) ) {
				$attr['srcset'] = '';
			}

			if ( ! key_exists( 'sizes', $attr ) ) {
				$attr['sizes'] = '';
			}

			$attachment_id = $this->get_id_image_by_url( $attr['src'] );
			if ( $attachment_id & empty( $attr['srcset'] ) ) {
				$image_meta = wp_get_attachment_metadata( $attachment_id );
				@list( $width, $height ) = getimagesize( $attr['src'] );
				if ( is_array( $image_meta ) ) {
					$size_array = array( absint( $width ), absint( $height ) );
					$srcset     = wp_calculate_image_srcset( $size_array, $attr['src'], $image_meta, $attachment_id );
					$sizes      = wp_calculate_image_sizes( $size_array, $attr['src'], $image_meta, $attachment_id );
					if ( $srcset && ( $sizes || ! empty( $attr['sizes'] ) ) ) {
						$attr['srcset'] = $srcset;

						if ( empty( $attr['sizes'] ) ) {
							$attr['sizes'] = $sizes;
						}
					}
				}
			}

			return apply_filters( 'check_attachment_image_attr', $attr );
		}

		public function check_attachment_image_src( $image, $attachment_id, $size, $icon ) {
			if ( ! empty( $image ) ) {
				list( $src, $old_width, $old_height ) = $image;

				$new_src = $this->check_attachment( $src );

				if ( $new_src ) {
					@list( $new_width, $new_height ) = getimagesize( $src );
					$attachment_id = $this->get_id_image_by_url( $src );
					$image         = image_downsize( $attachment_id, $size );

					if ( ! $image ) {
						$new_height = ( $new_height != $old_height ) ? $new_height : $old_height;
						$new_width  = ( $new_width != $old_width ) ? $new_width : $old_width;
						$image      = array( $src, $new_width, $new_height );
					}
					if ( $new_src == $src ) {
						$src_whithout_size = str_replace( '-' . $old_width . 'x' . $old_height, '', $src );
						$new_src           = $this->check_attachment_url( $src_whithout_size );
					}
					$image[0] = ( $new_src != $src ) ? $new_src : $src;
				}
			}

			return apply_filters( 'check_attachment_image_src', $image );
		}

		public function get_id_image_by_url( $url ) {
			global $wpdb;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url ) );

			return ! empty( $attachment ) ? $attachment[0] : false;
		}

		public function redirect_template_to( $rule, $origin_template ) {
			$destination = $rule['destination'];
			$url         = apply_filters( 'yith_wcgeoip_set_url_before_compare', $destination, $rule, $origin_template );

			$compare = $this->url_compare( $url, $origin_template['url'] );
			if ( ! $compare ) {
				$this->status_code = ! empty( $rule['status'] ) ? $rule['status'] : '';
				add_filter( 'wp_redirect_status', array( $this, 'set_status_header' ), 10, 2 );
				if ( 'custom_url' == $rule['destination_type'] ) {
					$url = ( preg_match( '/^http/i', $url ) ) ? $url : 'http://' . $url;
					wc_setcookie( 'yith_geoip_cookies_last_url_loaded', $url );
					wp_redirect( $url );
					exit;
				} else {
					wc_setcookie( 'yith_geoip_cookies_last_url_loaded', $url );
					wp_safe_redirect( $url );
					exit;
				}
			}
		}

		public function get_template_type() {
			if ( is_home() ) {
				return 'home';
			}
			if ( is_page() ) {
				return 'page';
			}
			if ( is_single() ) {
				return 'single';
			}
			if ( is_category() ) {
				return 'category';
			}
			if ( is_tag() ) {
				return 'tag';
			}
			if ( is_tax() ) {
				return 'tax';
			}
			if ( is_date() ) {
				return 'date';
			}
			if ( is_archive() ) {
				return 'archive';
			}
		}

		public function set_status_header( $status, $location ) {
			//$status_list = yith_wcgeoip_get_status_list();
			$status = ! empty( $this->status_code ) ? $this->status_code : $status;

			return $status;
		}

		public function url_compare( $url, $origin_url ) {
			$result = substr_compare( $url, $origin_url, strpos( $url, ':' ) + 1, strlen( $url ) ) === 0;

			return $result;
		}
	}
}
