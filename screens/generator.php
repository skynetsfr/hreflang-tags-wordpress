<?php
# --------------------------------------- #
# prevent file from being accessed directly
# --------------------------------------- #
if ( 'generator.php' == basename( $_SERVER[ 'SCRIPT_FILENAME' ] ) )
	die( 'Please do not access this file directly. Thanks!' );

if ( !is_admin() ) {
	Die();
}
?>

<div class="wrap hreflang-tags-pro-wrap">
	<?php
	require_once( ABSPATH . 'wp-admin/options-head.php' );
	?>
	<h1 id="hreflang-title">
		<?php
		echo esc_html( PLUGIN_NAME );
		if ( defined( 'HREFLANG_PRO_VERSION' ) ) {
			echo ' ' . esc_html( HREFLANG_PRO_VERSION );
		}
		?>
	</h1>
	<h3><span><?php _e('HREFLANG Tag Generator for HTML sites', 'hreflang-tags-pro'); ?></span></h3>
	<div class="hreflang-container">
		<div class="hreflang-content" id="hreflang-main-content">
			<?php hreflang_pro_html_generator(); ?>
		</div>
	</div>
</div>