<?php
/**
 * Branded FastSpring settings header + navigation.
 *
 * @package fastspring-split-gateways
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wc-fs-header">
	<div class="wc-fs-header__row">
		<div class="wc-fs-header__logo">
			<img src="<?php echo esc_url( FSSG_WC_FASTSPRING_PLUGIN_URL . '/assets/img/fastspring-logo.png' ); ?>" alt="FastSpring" />
		</div>
		<div class="wc-fs-header__logo-fs">
			<span class="wc-fs-header__poweredby"><?php esc_html_e( 'Powered by', 'fastspring-split-gateways' ); ?></span>&nbsp;&nbsp;
			<img src="<?php echo esc_url( FSSG_WC_FASTSPRING_PLUGIN_URL . '/assets/img/fastspring-logo.png' ); ?>" alt="FastSpring" />
		</div>
	</div>
	<div class="wc-fs-header__row">
		<nav class="wc-fs-nav">
			<?php foreach ( $tabs as $id => $tab ) : $idx++; ?>
				<a class="wc-fs-nav__item <?php echo esc_attr( $id ); ?><?php if ( $current_section === $id || ( ! $tab_active && $last === $idx ) ) { echo ' active'; $tab_active = true; } ?>"
					href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $id ) ); ?>"><?php echo esc_html( $tab ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</div>
