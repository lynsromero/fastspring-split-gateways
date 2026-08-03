<?php
/**
 * Read-only description row for FastSpring settings.
 *
 * @package fastspring-split-gateways
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<p class="<?php echo esc_attr( $data['class'] ); ?>" style="<?php echo esc_attr( $data['style'] ); ?>"><?php echo wp_kses_post( $data['description'] ); ?></p>
