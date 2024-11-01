<?php
/*
 * This file belongs to the YITH Framework.
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
 * @class      YITH_Geoip_Rules
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mateo
 *
 */

if ( ! class_exists( 'YITH_Geoip_Rules' ) ) {
	/**
	 * Class YITH_Geoip_Rules
	 *
	 * @author Francisco Mateo
	 */
	class YITH_Geoip_Rules {

		/**
		 * Construct
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 */
		public function __construct() {
		}

		public function insert( $rules ) {
			global $wpdb;
			$inserted = $wpdb->insert(
				$wpdb->yith_geoip_rules,
				array(
					'country'          => ( isset( $rules['country'] ) ) ? $rules['country'] : '',
					'country_excluded' => ( isset( $rules['country_excluded'] ) ) ? true : false,
					'origin'           => ( isset( $rules['origin'] ) ) ? $rules['origin'] : '',
					'origin_type'      => ( isset( $rules['origin_type'] ) ) ? $rules['origin_type'] : 'custom_url',
					'destination'      => ( isset( $rules['destination'] ) ) ? $rules['destination'] : '',
					'destination_type' => ( isset( $rules['destination_type'] ) ) ? $rules['destination_type'] : 'custom_url',
					'status'           => ( isset( $rules['status'] ) ) ? $rules['status'] : '',
					'device'           => ( isset( $rules['device'] ) ) ? $rules['device'] : 'all_devices',
					'only_one'         => ( isset( $rules['only_one'] ) ) ? true : false,
					'order_rule'       => ( isset( $rules['order'] ) ) ? $rules['order'] : - 1
				)
			);

			return $wpdb->insert_id;
		}

		public function update( $id_rule, $rules ) {
			global $wpdb;
			$updated = $wpdb->update(
				$wpdb->yith_geoip_rules,
				array(
					'country'          => ( isset( $rules['country'] ) ) ? $rules['country'] : '',
					'country_excluded' => ( isset( $rules['country_excluded'] ) ) ? true : false,
					'origin'           => ( isset( $rules['origin'] ) ) ? $rules['origin'] : '',
					'origin_type'      => ( isset( $rules['origin_type'] ) ) ? $rules['origin_type'] : 'custom_url',
					'destination'      => ( isset( $rules['destination'] ) ) ? $rules['destination'] : '',
					'destination_type' => ( isset( $rules['destination_type'] ) ) ? $rules['destination_type'] : 'custom_url',
					'status'           => ( isset( $rules['status'] ) ) ? $rules['status'] : '',
					'device'           => ( isset( $rules['device'] ) ) ? $rules['device'] : 'all_devices',
					'only_one'         => ( isset( $rules['only_one'] ) ) ? true : false,
					'order_rule'       => ( isset( $rules['order'] ) ) ? $rules['order'] : - 1
				),
				array( 'ID' => $id_rule )
			);

			return $updated;

		}

		public function delete( $id_rule ) {
			global $wpdb;

			$deleted = $wpdb->delete(
				$wpdb->yith_geoip_rules,
				array( 'ID' => $id_rule )
			);

			return $deleted;
		}

		public function get_rule( $id_rule ) {
			global $wpdb;
			$query  = $wpdb->prepare( "SELECT * FROM $wpdb->yith_geoip_rules WHERE ID = %d", $id_rule );
			$is_get = $wpdb->get_row(
				$query,
				OBJECT
			);

			return $is_get;
		}

		public function get_rules( $args ) {
			global $wpdb;
			$default_args = array(
				'country'          => '',
				'origin'           => '',
				'origin_type'      => '',
				'destination'      => '',
				'destination_type' => '',
				'status'           => '',
				'only_one'         => '',
				'device'           => '',
				'orderby'          => 'order_rule',
				'order'            => 'ASC',
				'limit'            => 0,
				'offset'           => 0
			);

			$rules = wp_parse_args( $args, $default_args );

			$query     = '';
			$query_arg = array();
			$query     = "SELECT * FROM $wpdb->yith_geoip_rules WHERE 1=1";


			if ( ! empty( $rules['country'] ) ) {
				$query .= ' AND ((country = %s and country_excluded = 0) OR (country <> %s and country_excluded = 1))';
				//$query .= ' AND id = any (select id from wp_yith_wcgeoip_rules where (country = %s and country_excluded = 0) OR (country <> %s and country_excluded = 1))';
				//$query .= ' AND (country = %s and country_excluded = 0) OR (country <> %s and country_excluded = 1)';
				$query_arg[] = $rules['country'];
				$query_arg[] = $rules['country'];
			}

			if ( ! empty( $rules['origin'] ) ) {
				if ( 'custom_url_regex' == $rules['origin_type'] ) {
					$query       .= ' AND %s RLIKE origin';
					$query_arg[] = $rules['origin'];
				} else {
					$query       .= ' AND origin LIKE %s';
					$query_arg[] = '%' . $rules['origin'] . '';
				}
				//$query_arg[] = '%' . $rules['origin'] . '%';
			}

			if ( ! empty( $rules['origin_type'] ) ) {
				$query       .= ' AND origin_type LIKE %s';
				$query_arg[] = '%' . $rules['origin_type'];
			}

			if ( ! empty( $rules['destination'] ) ) {
				$query       .= ' AND destination LIKE %s';
				$query_arg[] = '%' . $rules['destination'] . '%';
			}

			if ( ! empty( $rules['destination_type'] ) ) {
				$query       .= ' AND destination_type LIKE %s';
				$query_arg[] = '%' . $rules['destination_type'] . '%';
			}

			if ( ! empty( $rules['status'] ) ) {
				$query       .= ' AND status LIKE %s';
				$query_arg[] = '%' . $rules['status'] . '%';
			}
			if ( ! empty( $rules['device'] ) ) {
				$query       .= ' AND device LIKE %s';
				$query_arg[] = '%' . $rules['device'] . '%';
			}
			if ( ! empty( $rules['only_one'] ) ) {
				$query       .= ' AND only_one LIKE %s';
				$query_arg[] = '%' . $rules['only_one'] . '%';
			}

			if ( ! empty( $rules['orderby'] ) ) {
				$query .= sprintf( ' ORDER BY %s %s', $rules['orderby'], $rules['order'] );
			}

			if ( ! empty( $rules['limit'] ) ) {
				$query .= sprintf( ' LIMIT %d, %d', ! empty( $rules['offset'] ) ? $rules['offset'] : 0, $rules['limit'] );
			}

			$prepared_query = ! empty( $query_arg ) ? $wpdb->prepare( $query, $query_arg ) : $query;
			$res            = $wpdb->get_results( $prepared_query, ARRAY_A );

			return $res;
		}
	}
}
