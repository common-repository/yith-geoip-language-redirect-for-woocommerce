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
 * @class      YITH_Geoip
 * @package    Yithemes
 * @since      Version 2.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Geoip' ) ) {
	/**
	 * Class YITH_Geoip
	 *
	 * @author Francisco Mateo
	 */
	class YITH_Geoip {
		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0
		 */
		public $version = YITH_WCGEOIP_VERSION;

		/**
		 * Plugin DB version
		 *
		 * @const string
		 * @since 1.0.0
		 */
		const YITH_WCGEOIP_DB_VERSION = '1.0.2';

		/**
		 * Main Instance
		 *
		 * @var YITH_Geoip
		 * @since  1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main Admin Instance
		 *
		 * @var YITH_Geoip_Admin
		 * @since 1.0
		 */
		public $admin = null;

		/**
		 * Main Frontpage Instance
		 *
		 * @var YITH_Geoip_Frontend
		 * @since 1.0
		 */
		public $frontend = null;

		/**
		 * Construct
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 */
		public function __construct() {

			/* === Require Main Files === */
			$require = apply_filters( 'yith_wcgeoip_require_class',
				array(
					'common'   => array(
						'includes/functions.yith-wcgeoip.php',
						'includes/class.yith-geoip-language-redirect-rules.php'
					),
					'frontend' => array(
						'includes/class.yith-geoip-language-redirect-frontend.php'
					),
					'admin'    => array(
						'includes/class.yith-geoip-language-redirect-admin.php'
					)

				)
			);

			$this->_require( $require );

			/* === Load Plugin Framework === */
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'privacy_loader' ), 20 );

			/* === Plugins Init === */
			add_action( 'init', array( $this, 'init' ) );


		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Geoip Main instance
		 * @author Francisco Mateo
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Add the main classes file
		 *
		 * Include the admin and frontend classes
		 *
		 * @param $main_classes array The require classes file path
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 *
		 * @return void
		 * @access protected
		 */
		protected function _require( $main_classes ) {
			foreach ( $main_classes as $section => $classes ) {
				foreach ( $classes as $class ) {
					if ( 'common' == $section || ( 'frontend' == $section && ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) || ( 'admin' == $section && is_admin() ) && file_exists( YITH_WCGEOIP_PATH . $class ) ) {
						require_once( YITH_WCGEOIP_PATH . $class );
					}
				}
			}
			do_action( 'yith_wcgeoip_require' );
		}

		/**
		 * Load plugin framework
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 * @return void
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Load privacy class
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 * @return void
		 */
		public function privacy_loader() {
			if( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once( YITH_WCGEOIP_PATH . 'includes/class.yith-geoip-language-redirect-privacy.php' );
				new YITH_Geoip_Privacy();
			}
		}

		/**
		 * Class Initialization
		 *
		 * Instance the admin class
		 *
		 * @author Francisco Mateo
		 * @since  1.0
		 * @return void
		 * @access protected
		 */
		public function init() {
			$this->_install_tables();

			if ( is_admin() ) {
				$this->admin = new YITH_Geoip_Admin();
			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				session_start();
				$this->frontend = new YITH_Geoip_Frontend();
			}
		}

		protected function _install_tables() {
			global $wpdb;

			// adds tables name in global $wpdb
			$wpdb->yith_geoip_rules = $wpdb->prefix . 'yith_wcgeoip_rules';

			// skip if current db version is equal to plugin db version
			$current_db_version = get_option( 'yith_wcgeoip_db_version' );
			if ( $current_db_version == self::YITH_WCGEOIP_DB_VERSION ) {
				return;
			} else {
				update_option( 'yith_wcgeoip_db_version', self::YITH_WCGEOIP_DB_VERSION );
			}

			// assure dbDelta function is defined
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// retrieve table charset
			$charset_collate = $wpdb->get_charset_collate();

			// adds geoip_rules table
			$sql = "CREATE TABLE $wpdb->yith_geoip_rules (
                    ID bigint(20) NOT NULL AUTO_INCREMENT,
                    country VARCHAR(255) NOT NULL,
                    country_excluded INT,
                    origin VARCHAR (255) NOT NULL,
                    origin_type VARCHAR(255),
                    destination VARCHAR(255) NOT NULL,
                    destination_type VARCHAR (255),
                    status VARCHAR (20),
                    only_one INT,
                    device VARCHAR (255),
                    order_rule bigint(20),
                    PRIMARY KEY ID (ID),
                    INDEX `country` (country),
                    INDEX  `origin` (origin)
                ) $charset_collate;";

			dbDelta( $sql );

		}
	}
}