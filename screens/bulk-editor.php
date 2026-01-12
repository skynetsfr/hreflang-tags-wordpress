<?php
# --------------------------------------- #
# prevent file from being accessed directly
# --------------------------------------- #
if ( 'bulk-editor.php' == basename( $_SERVER[ 'SCRIPT_FILENAME' ] ) )
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
	<h3><span><?php _e('HREFLANG Tags Bulk Editor', 'hreflang-tags-pro'); ?></span></h3>
	<div class="hreflang-container">
		<div class="hreflang-content" id="hreflang-main-content">
			<?php
			global $hreflanguages;
			$hreflang_pro_bulk_lt = new HREFLANG_PRO_Bulk_List_Table();
			$hreflang_pro_bulk_lt->show_page();
			?>
		</div>
		<div id="wp-link-wrap" class="wp-core-ui has-text-field" style="display: none;" role="dialog" aria-labelledby="link-modal-title">
				<form id="wp-link" tabindex="-1">
				<input type="hidden" id="_ajax_linking_nonce" name="_ajax_linking_nonce" value="606b355ed9">
				<input type="hidden" id="wrap_post_id" name="wrap_post_id" value="">
				<input type="hidden" id="wrap_entry_number" name="wrap_entry_number" value="">
				<h1 id="link-modal-title">Insert/edit link</h1>
				<button type="button" id="wp-link-close"><span class="screen-reader-text">Close</span></button>
				<div id="link-selector">
					<div id="link-options">
						<p class="howto" id="wplink-enter-url">Enter the destination URL</p>
						<div>
							<label><span>URL</span>
							<input id="wp-link-url" type="text" aria-describedby="wplink-enter-url"></label>
						</div>
					</div>
					<div id="search-panel">
						<div id="most-recent-results" class="query-results" tabindex="0"></div>
					</div>
				</div>
				<div class="submitbox">
					<div id="wp-link-cancel">
						<button type="button" class="button">Cancel</button>
					</div>
					<div id="wp-link-update">
						<input type="submit" value="Add Link" class="button button-primary" id="wp-link-submit-hreflang" name="wp-link-submit">
					</div>
				</div>
				</form>
				</div>
		<div class="hreflang-content metabox-holder" id="hreflang-sidebar-content">
			<div class="stuffbox">
				<h3 class="hndle"><span><?php _e('Updates &amp; Licensing','hreflang-tags-pro'); ?></span></h3>
				<div class="inside"><?php hreflang_tags_pro_parse_license_box(); ?></div>
			</div>
		</div>
	</div>

</div>
