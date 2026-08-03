<?php
/**
 * Action button row for FastSpring settings (AJAX driven).
 *
 * @package fastspring-split-gateways
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
	</th>
	<td class="forminp">
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
			<button type="button" class="<?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>"
				id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>"
				<?php echo $this->get_custom_attribute_html( $data ); // WPCS: XSS ok. ?>><?php echo wp_kses_post( $data['label'] ); ?></button>
			<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
		</fieldset>
	</td>
</tr>
