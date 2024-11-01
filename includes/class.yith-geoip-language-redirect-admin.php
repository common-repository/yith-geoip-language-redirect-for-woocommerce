<?php
/*
 * This file belongs to the YITH Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGEOIP_PATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Geoip_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francsico Mateo
 *
 */

if ( ! class_exists( 'YITH_Geoip_Admin' ) ) {
	/**
	 * Class YITH_Geoip_Admin
	 *
	 * @author Francsico Mateo
	 */
	class YITH_Geoip_Admin {

		/**
		 * @var Panel page
		 */
		protected $_panel_page = 'yith_wcgeoip_panel';

		protected $_panel = null;

		/**
		 * @var GeoIP Rules
		 */
		protected $geoip_rules = null;


		/**
		 * @var doc_url
		 */
		protected $doc_url = 'http://docs.yithemes.com/yith-geoip-language-redirect-for-woocommerce';

		/**
		 * @var string Official plugin documentation
		 */
		protected $_official_documentation = 'http://docs.yithemes.com/yith-geoip-language-redirect-for-woocommerce';

		/**
		 * Construct
		 *
		 * @author Francsico Mateo
		 * @since  1.0
		 */
		public function __construct() {

			/* === Set admin Ajax calls === */
			add_action( 'wp_ajax_print_rule_row_action', array( $this, 'print_rule_row_action' ) );
			add_action( 'wp_ajax_finder_source_action', array( $this, 'finder_source_action' ) );
			add_action( 'wp_ajax_save_rules_action', array( $this, 'save_rules_action' ) );
			add_action( 'wp_ajax_save_settings_action', array( $this, 'save_settings_action' ) );

			/* === Register Panel Settings === */
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			/* === Get Rules Panel === */
			add_action( 'yith_wcgeoip_rules_panel', array( $this, 'get_rules_panel' ) );

			$this->geoip_rules = new YITH_Geoip_Rules();

            /* === Premium Tab === */
            add_action( 'yith_wcgeoip_premium_tab', array( $this, 'show_premium_landing' ) );
		}

		protected function enqueue_rules_scripts() {
			$path   = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '/unminified' : '';
			$prefix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

            //wp_register_style(  'yith-wc-style-regex', YITH_WCGEOIP_ASSETS_URL . 'regex_colorizer/nobg.css', null, YITH_WCGEOIP_VERSION);
            //wp_register_script( 'yith-wc-script-regex', YITH_WCGEOIP_ASSETS_URL . '/regex_colorizer' . $path . '/regex-colorizer' . $prefix . '.js', array(), YITH_WCGEOIP_VERSION, true);

			wp_register_style( 'yith-wc-style-geopip-rules', YITH_WCGEOIP_ASSETS_URL . 'css/style-geoip-rules.css', null, YITH_WCGEOIP_VERSION );
			wp_register_script( 'yith-wc-script-geopip-rules', YITH_WCGEOIP_ASSETS_URL . '/js' . $path . '/script-geoip-rules' . $prefix . '.js', array( 'jquery', 'jquery-ui-sortable' ), YITH_WCGEOIP_VERSION, true );

			wp_enqueue_style( 'yith-wc-style-geopip-rules' );
			wp_enqueue_script( 'yith-wc-script-geopip-rules' );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @author   Francsico Mateo
		 * @since    1.0
		 * @return void
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {
			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$menu_title = _x( 'GeoIP Language Redirect', 'shortened plugin name', 'yith-geoip-language-redirect-for-woocommerce' );

			$admin_tabs = apply_filters( 'yith_wcgeoip_admin_tabs', array(
                    'rules'     => _x( 'Rules', 'tab name', 'yith-geoip-language-redirect-for-woocommerce' ),
                    'premium'   => _x('Premium', 'tab name', 'yith-geoip-language-redirect-for-woocommerce')
				)
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => $menu_title,
				'menu_title'       => $menu_title,
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCGEOIP_OPTIONS_PATH,
				'links'            => $this->get_sidebar_link()
			);


			/* === Fixed: not updated theme/old plugin framework  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Sidebar links
		 *
		 * @return   array The links
		 * @since    1.2.1
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function get_sidebar_link() {
			$links = array(
				array(
					'title' => __( 'Plugin documentation', 'yith-geoip-language-redirect-for-woocommerce' ),
					'url'   => $this->_official_documentation,
				),
				array(
					'title' => __( 'Help Center', 'yith-geoip-language-redirect-for-woocommerce' ),
					'url'   => 'http://support.yithemes.com/hc/en-us/categories/202568518-Plugins',
				),
			);

			return $links;
		}

		/**
		 * Load rules template on panel options
		 *
		 */
		public function get_rules_panel() {
			$this->enqueue_rules_scripts();
			$args = array(
				'rules' => $this->geoip_rules->get_rules( array() ),
			);
			yith_wcgeoip_get_template( 'rules-panel', $args, 'admin' );
		}

		public function print_rule_row_action() {
			if ( isset( $_POST['index'] ) ) {
				$args = array(
					'index'    => $_POST['index'],
					'rule_row' => array()
				);

				yith_wcgeoip_get_template( 'rule-row', $args, 'admin' );
			}
			die();
		}

		public function save_rules_action() {
			$rules_to_save   = explode( ",", $_POST['_rules_to_save'] );
			$rules_to_remove = explode( ",", $_POST['_rules_to_remove'] );

			$created = array();
			foreach ( $rules_to_save as $rule_to_save ) {
				$rule = isset( $_POST['_rules'][ $rule_to_save ]['rule_ID'] ) ? $_POST['_rules'][ $rule_to_save ] : array();
				if ( ! empty( $rule ) ) {
					if ( ! empty( $rule['origin'] ) & ! empty( $rule['destination'] ) ) {
						if ( 'new' != $rule['rule_ID'] ) {
							$this->geoip_rules->update( $rule['rule_ID'], $rule );
						} else {
							$inserted  = $this->geoip_rules->insert( $rule );
							$created[] = array(
								'index'   => $rule_to_save,
								'id_rule' => $inserted
							);
						}
					}
				}
			}

			foreach ( $rules_to_remove as $rule_to_remove ) {
				$this->geoip_rules->delete( $rule_to_remove );
			}

			wp_send_json( $created );
			die();
		}

		public function save_settings_action() {

			$exclude_ip_list = array();
			if ( isset( $_POST['data']['list_exclude_ips'] ) ) {
				$exclude_ip_list = explode( ',', $_POST['data']['list_exclude_ips'] );
			}
			$once_redirect_cookie_duration = $_POST['data']['once_redirect_cookie_duration'];

			update_option( 'list_exclude_ips', $exclude_ip_list );
			update_option( 'once_redirect_cookie_duration', $once_redirect_cookie_duration );

			die();
		}
	}
}