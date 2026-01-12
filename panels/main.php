<?php
# --------------------------------------- #
# prevent file from being accessed directly
# --------------------------------------- #
if ( 'main.php' == basename( $_SERVER[ 'SCRIPT_FILENAME' ] ) )
	die( 'Please do not access this file directly. Thanks!' );

if ( !is_admin() ) {
	Die();
}
?>

<?php
$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'hreflang_pro_dashboard';
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
	<h3><span><?php _e('General Settings', 'hreflang-tags-pro'); ?></span></h3>
	<div class="hreflang-container">
		<div class="hreflang-content" id="hreflang-main-content">
			<h2 class="nav-tab-wrapper">
		<a href="?page=hreflang_pro&tab=hreflang_pro_dashboard" class="nav-tab <?php echo $active_tab == 'hreflang_pro_dashboard' ? 'nav-tab-active' : ''; ?>"><?php _e('Dashboard', 'hreflang-tags-pro') ?></a>
		<a href="?page=hreflang_pro&tab=hreflang_pro_home" class="nav-tab <?php echo $active_tab == 'hreflang_pro_home' ? 'nav-tab-active' : ''; ?>"><?php _e('Home', 'hreflang-tags-pro') ?></a>
		<a href="?page=hreflang_pro&tab=hreflang_pro_blog" class="nav-tab <?php echo $active_tab == 'hreflang_pro_blog' ? 'nav-tab-active' : ''; ?>"><?php _e('Blog', 'hreflang-tags-pro') ?></a>
		<?php if ( hreflang_pro_is_woocommerce_activated() ) { ?>
		<a href="?page=hreflang_pro&tab=hreflang_pro_woocommerce_shop" class="nav-tab <?php echo $active_tab == 'hreflang_pro_woocommerce_shop' ? 'nav-tab-active' : ''; ?>"><?php _e('WooCommerce/Shop', 'hreflang-tags-pro') ?></a>
		<?php
		}
		?>
		<a href="?page=hreflang_pro&tab=hreflang_pro_sitemap" class="nav-tab <?php echo $active_tab == 'hreflang_pro_sitemap' ? 'nav-tab-active' : ''; ?>"><?php _e('XML Sitemap', 'hreflang-tags-pro') ?></a>
</h2>
		
			<?php
			if ( !defined( 'HREFLANG_PRO_PLUGIN_MAIN_PATH' ) )
				define( 'HREFLANG_PRO_PLUGIN_MAIN_PATH', plugin_dir_path( __FILE__ ) );

			if ( $active_tab == 'hreflang_pro_dashboard' ) {
				if ( file_exists( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/dashboard.php' ) ) {
					include_once( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/dashboard.php' );
				} else {
					echo 'File is missing';
				}
			}

			if ( $active_tab == 'hreflang_pro_home' ) {
				if ( file_exists( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/home.php' ) ) {
					include_once( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/home.php' );
				} else {
					echo 'File is missing';
				}
			}

			if ( $active_tab == 'hreflang_pro_blog' ) {
				if ( file_exists( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/blog.php' ) ) {
					include_once( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/blog.php' );
				} else {
					echo 'File is missing';
				}
			}

			if ( $active_tab == 'hreflang_pro_woocommerce_shop' ) {
				if ( file_exists( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/woocommerce.php' ) ) {
					include_once( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/woocommerce.php' );
				} else {
					echo 'File is missing';
				}
			}

			if ( $active_tab == 'hreflang_pro_sitemap' ) {
				if ( file_exists( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/sitemap.php' ) ) {
					include_once( HREFLANG_PRO_PLUGIN_MAIN_PATH . 'tabs/sitemap.php' );
				} else {
					echo 'File is missing';
				}
			}

			?>
		</div>
	</div>

</div>