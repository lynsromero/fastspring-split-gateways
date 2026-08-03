<?php
/**
 * FastSpring Split Gateways - Admin settings router + assets.
 *
 * Routes the branded API/Advanced settings sections through the standard
 * WooCommerce checkout settings page and enqueues the admin styling/scripts
 * only on FastSpring settings screens.
 *
 * @package fastspring-split-gateways
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_FastSpring_Admin_Settings' ) ) :

	/**
	 * WC_FastSpring_Admin_Settings
	 */
	class WC_FastSpring_Admin_Settings {

		/**
		 * Whether the current admin screen is a FastSpring settings screen.
		 *
		 * @return bool
		 */
		public static function is_fs_screen() {
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';
			if ( strpos( $screen_id, 'wc-settings' ) === false ) {
				return false;
			}
			$section = isset( $_REQUEST['section'] ) ? sanitize_title( wp_unslash( $_REQUEST['section'] ) ) : '';
			return (bool) preg_match( '/^(fs_|fastspring)/', $section );
		}

		/**
		 * Boots the router and the non-gateway settings pages.
		 */
		public static function init() {
			add_action( 'woocommerce_settings_checkout', array( __CLASS__, 'output' ) );
			add_action( 'woocommerce_update_options_checkout', array( __CLASS__, 'save' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
			add_action( 'admin_head', array( __CLASS__, 'add_inline_styles' ) );

			new WC_FastSpring_API_Settings();
			new WC_FastSpring_Advanced_Settings();
		}

		/**
		 * Outputs the settings for the current (non-gateway) section.
		 */
		public static function output() {
			global $current_section;
			do_action( 'woocommerce_fs_settings_checkout_' . $current_section );
		}

		/**
		 * Saves the settings for the current (non-gateway) section.
		 */
		public static function save() {
			global $current_section;
			if ( $current_section && ! did_action( 'woocommerce_update_options_checkout_' . $current_section ) ) {
				do_action( 'woocommerce_update_options_checkout_' . $current_section );
			}
		}

		/**
		 * Enqueues the branded admin assets on FastSpring settings screens.
		 */
		public static function enqueue_scripts() {
			if ( ! self::is_fs_screen() ) {
				return;
			}

			wp_enqueue_style(
				'wc-fastspring-admin',
				FSSG_WC_FASTSPRING_PLUGIN_URL . '/assets/css/admin.css',
				array( 'woocommerce_admin_styles' ),
				FSSG_WC_FASTSPRING_VERSION
			);

			wp_enqueue_script(
				'wc-fastspring-admin',
				FSSG_WC_FASTSPRING_PLUGIN_URL . '/assets/js/admin-settings.js',
				array( 'jquery' ),
				FSSG_WC_FASTSPRING_VERSION,
				true
			);

			wp_localize_script(
				'wc-fastspring-admin',
				'wc_fs_admin_params',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'wc_fs_admin' ),
					'messages' => array(
						'connection_test'  => __( 'Testing connection...', 'fastspring-split-gateways' ),
						'connection_fail'  => __( 'Connection failed. Please check your storefront path and credentials.', 'fastspring-split-gateways' ),
						'generate_secret'  => __( 'Generating secret...', 'fastspring-split-gateways' ),
						'secret_generated' => __( 'Webhook secret generated. Save your settings to keep the new secret.', 'fastspring-split-gateways' ),
					),
				)
			);
		}

		/**
		 * Hides the default WooCommerce page header/nav on FastSpring screens.
		 */
		public static function add_inline_styles() {
			if ( ! self::is_fs_screen() ) {
				return;
			}
			?>
			<style>
				body[class*="woocommerce_page_wc-settings-checkout-section-fs_"] .woocommerce-layout__header,
				body[class*="woocommerce_page_wc-settings-checkout-section-fastspring"] .woocommerce-layout__header {
					display: none;
				}

				body[class*="woocommerce_page_wc-settings-checkout-section-fs_"] .woo-nav-tab-wrapper,
				body[class*="woocommerce_page_wc-settings-checkout-section-fastspring"] .woo-nav-tab-wrapper {
					display: none !important;
				}

				#wpcontent #wpbody {
					margin-top: 0;
				}
			</style>
			<?php
		}
	}

	WC_FastSpring_Admin_Settings::init();

endif;
