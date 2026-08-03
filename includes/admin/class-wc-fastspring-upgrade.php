<?php
/**
 * FastSpring Split Gateways - Upgrade routines.
 *
 * Runs idempotent data migrations when the plugin version changes.
 *
 * @package fastspring-split-gateways
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_FastSpring_Upgrade' ) ) :

	/**
	 * WC_FastSpring_Upgrade
	 */
	class WC_FastSpring_Upgrade {

		/**
		 * Boots the upgrade hooks.
		 */
		public static function init() {
			add_action( 'woocommerce_fastspring_updated', array( __CLASS__, 'migrate_icon_slugs' ) );
		}

		/**
		 * Rewrites legacy absolute icon URLs (`/assets/icons/<slug>.svg`) to
		 * bare slugs so the icon picker and get_icon() resolve them locally.
		 *
		 * Array icons (credit card multiselect) are left untouched.
		 */
		public static function migrate_icon_slugs() {
			$gateway_ids = array(
				'fastspring_paypal',
				'fastspring_card',
				'fastspring_amazon',
				'fastspring_wire',
				'fastspring_googlepay',
				'fastspring_alipay',
			);

			foreach ( $gateway_ids as $gateway_id ) {
				$option_key = 'woocommerce_' . $gateway_id . '_settings';
				$settings   = get_option( $option_key, array() );

				if ( ! is_array( $settings ) || ! isset( $settings['icon'] ) ) {
					continue;
				}

				$icon = $settings['icon'];

				if ( is_string( $icon ) && preg_match( '#/assets/icons/([a-z0-9-]+)\.svg$#i', $icon, $matches ) ) {
					$settings['icon'] = $matches[1];
					update_option( $option_key, $settings );
				}
			}
		}
	}

	WC_FastSpring_Upgrade::init();

endif;
