<?php
/**
 * Gateway icon picker field.
 *
 * Renders preset SVG radio cards plus an optional custom image uploader.
 * The actual value submitted is the hidden input (preset slug or `custom:<url>`).
 *
 * Expects: $field_key (string), $value (string), $data (array), $this (gateway).
 *
 * @package fastspring-split-gateways
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icon_options = isset( $data['options'] ) && is_array( $data['options'] ) ? $data['options'] : array();
$icons_url    = FS_SPLIT_GATEWAY_URL . 'assets/icons/';
$selected     = '';
$custom_url   = '';

if ( is_string( $value ) && isset( $icon_options[ $value ] ) ) {
	$selected = $value;
} elseif ( is_string( $value ) && strpos( $value, 'custom:' ) === 0 ) {
	$selected   = 'custom';
	$custom_url = substr( $value, 7 );
} elseif ( is_string( $value ) && '' !== $value ) {
	$selected   = 'custom';
	$custom_url = $value;
} else {
	$selected = (string) $this->default_icon;
}
?>
<tr valign="top">
	<th scope="row" class="titledesc">
		<label><?php echo wp_kses_post( $data['title'] ); ?></label>
	</th>
	<td class="forminp wc-fs-icon-picker">
		<input type="hidden" class="wc-fs-icon-value" id="<?php echo esc_attr( $field_key ); ?>" name="<?php echo esc_attr( $field_key ); ?>" value="<?php echo esc_attr( is_string( $value ) ? $value : '' ); ?>" />

		<div class="wc-fs-icon-options">
			<?php foreach ( $icon_options as $slug => $label ) : ?>
				<label class="wc-fs-icon-option<?php echo $selected === $slug ? ' selected' : ''; ?>">
					<input type="radio" name="wc_fs_icon_choice" value="<?php echo esc_attr( $slug ); ?>"<?php checked( $selected, $slug ); ?> />
					<img src="<?php echo esc_url( $icons_url . $slug . '.svg' ); ?>" alt="<?php echo esc_attr( $label ); ?>" />
					<span><?php echo esc_html( $label ); ?></span>
				</label>
			<?php endforeach; ?>
			<label class="wc-fs-icon-option wc-fs-icon-custom<?php echo 'custom' === $selected ? ' selected' : ''; ?>">
				<input type="radio" name="wc_fs_icon_choice" value="custom"<?php checked( $selected, 'custom' ); ?> />
				<span><?php esc_html_e( 'Custom image', 'woocommerce' ); ?></span>
			</label>
		</div>

		<div class="wc-fs-icon-custom-field"<?php echo 'custom' === $selected ? '' : ' style="display:none"'; ?>>
			<input class="input-text regular-input wc-fs-icon-custom-url" type="text" value="<?php echo esc_attr( $custom_url ); ?>" style="width: 300px; margin-right: 10px;" />
			<button type="button" class="button wc-fs-icon-upload-btn"><?php esc_html_e( 'Upload Icon', 'woocommerce' ); ?></button>
		</div>

		<?php if ( ! empty( $data['description'] ) ) : ?>
			<p class="description"><?php echo wp_kses_post( $data['description'] ); ?></p>
		<?php endif; ?>
	</td>
</tr>
