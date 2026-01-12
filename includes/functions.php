<?php

// plugin activation actions
function hreflang_pro_admin_actions() {
	global $hreflang_pro_settings_page, $hreflang_pro_bulk_editor_page;
	if (current_user_can('manage_options')) {
		$hreflang_pro_settings_page = add_menu_page("Hreflang Tags WP", "Hreflang Tags WP", "manage_options", "hreflang_pro", "hreflang_pro_menu", 'dashicons-translation');
		$hreflang_pro_settings_subpage = add_submenu_page("hreflang_pro", "Hreflang Tags WP Settings", "Settings", "manage_options",  "hreflang_pro", 'hreflang_pro_menu');
		$hreflang_pro_generator_page = add_submenu_page('hreflang_pro','HTML Generator', 'HTML Generator', 'manage_options', 'hreflang_pro_generator' , 'hreflang_pro_generator' );
		$hreflang_pro_bulk_editor_page = add_submenu_page('hreflang_pro','Bulk Editor', 'Bulk Editor', 'manage_options', 'hreflang_pro_bulk_editor' , 'hreflang_pro_bulk_editor' );
		$hreflang_pro_validator_page = add_submenu_page('hreflang_pro','Validation Tool', 'Validation Tool', 'manage_options', 'hreflang_pro_validator' , 'hreflang_pro_validator' );
	}
}

// Add settings link on plugin page
function hreflang_pro_plugin_settings_link($links) {
  $settings_link = '<a href="admin.php?page=hreflang_pro&tab=hreflang_pro_dashboard">Settings</a>';
  $generator_link = '<a href="admin.php?page=hreflang_pro_generator">Generator</a>';
  $editor_link = '<a href="admin.php?page=hreflang_pro_bulk_editor">Bulk Editor</a>';
  array_unshift($links, $settings_link);
  array_unshift($links, $generator_link);
  array_unshift($links, $editor_link);
  return $links;
}

function hreflang_pro_admin_bar() {
	global $wp_admin_bar;

	//Add a link called 'My Link'...
	$wp_admin_bar->add_node(array(
		'id'    => 'hreflang',
		'title' => 'HREFLANG PRO',
		'href'  => admin_url( 'admin.php?page=hreflang_pro', 'http' )
	));

}

function hreflang_pro_textdomain() {
   if (function_exists('load_plugin_textdomain')) {
	load_plugin_textdomain('hreflang-tags-pro', false, dirname( plugin_basename( HREFLANG_PRO_PLUGIN_FILE ) ) . '/languages' );
   }
}

function hreflang_pro_menu(){
	include_once(HREFLANG_PRO_PLUGIN_MAIN_PATH. 'panels/main.php' );
}

function hreflang_pro_generator() {
	include_once(HREFLANG_PRO_PLUGIN_MAIN_PATH. 'screens/generator.php' );
}

function hreflang_pro_bulk_editor(){
	include_once(HREFLANG_PRO_PLUGIN_MAIN_PATH. 'screens/bulk-editor.php' );
}

function hreflang_pro_validator() {
	include_once(HREFLANG_PRO_PLUGIN_MAIN_PATH. 'screens/validator.php' );
}


function hreflang_pro_register_settings() {
	register_setting( 'hreflang-settings-group', 'hreflang_post_types');
	register_setting( 'hreflang-settings-group', 'hreflang_pro_show_admin_bar');
	register_setting( 'hreflang-blog-settings-group', 'hreflang_pro_allow_blog_tags');
	register_setting( 'hreflang-blog-settings-group', 'hreflang_pro_blog_tags');
	register_setting( 'hreflang-shop-settings-group', 'hreflang_pro_allow_shop_tags');
	register_setting( 'hreflang-shop-settings-group', 'hreflang_pro_shop_tags');
	register_setting( 'hreflang-xml-sitemap-settings-group', 'hreflang_pro_enable_xml_sitemap');
	register_setting( 'hreflang-xml-sitemap-settings-group', 'hreflang_pro_disable_tags_head');
	register_setting( 'hreflang-xml-sitemap-settings-group', 'hreflang_pro_xml_sitemap_filename');
}

function add_hreflang_pro_to_head() {
	global $post;
	if (!get_option('hreflang_pro_disable_tags_head') == "1") {
		if (is_category() || is_tax() || is_tag()) {
			$terms = get_queried_object();
			$hreflang_pro_data = get_term_meta($terms->term_id);
			$sortable_hreflang_pro_data = array();
			if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
				foreach($hreflang_pro_data as $key => $value) {
					if (stristr($key, 'hreflang')) {
						$sortable_hreflang_pro_data[$key] = $value;
					}
				}
			}
			hreflang_pro_sorted_array_parse_tags($sortable_hreflang_pro_data);
		}
		else {
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if (!is_home() && !is_author() && !is_tag() && !is_shop()) {
					$hreflang_pro_data = get_post_meta($post->ID);
					$sortable_hreflang_pro_data = array();
					if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
						foreach($hreflang_pro_data as $key => $value) {
							if (stristr($key, 'hreflang')) {
								$sortable_hreflang_pro_data[$key] = $value;
							}
						}
					}
					hreflang_pro_sorted_array_parse_tags($sortable_hreflang_pro_data);
				}
				else {
					if ('1' == get_option('hreflang_pro_allow_blog_tags') && !is_author() && !is_shop()) {
						$hreflang_pro_data = get_option('hreflang_pro_blog_tags');
						$sortable_hreflang_pro_data = array();
						if (is_array($hreflang_pro_data['href']) && !empty($hreflang_pro_data['href'])) {
							for ($n = 0; $n < count($hreflang_pro_data['href']); $n++) {
								if ($hreflang_pro_data['hrefregion'][$n] != '') {
									$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n].'_'.$hreflang_pro_data['hrefregion'][$n]] = array($hreflang_pro_data['href'][$n]);
								}
								else {
									$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n]] = array($hreflang_pro_data['href'][$n]);
								}
							}
						}
						hreflang_pro_sorted_array_parse_tags($sortable_hreflang_pro_data);
					}
					if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
						if ('1' == get_option('hreflang_pro_allow_shop_tags') && is_shop()) {
							$hreflang_pro_data = get_option('hreflang_pro_shop_tags');
							$sortable_hreflang_pro_data = array();
							if (is_array($hreflang_pro_data['href']) && !empty($hreflang_pro_data['href'])) {
								for ($n = 0; $n < count($hreflang_pro_data['href']); $n++) {
									if ($hreflang_pro_data['hrefregion'][$n] != '') {
										$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n].'_'.$hreflang_pro_data['hrefregion'][$n]] = array($hreflang_pro_data['href'][$n]);
									}
									else {
										$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n]] = array($hreflang_pro_data['href'][$n]);
									}
								}
							}
							hreflang_pro_sorted_array_parse_tags($sortable_hreflang_pro_data);
						}
					}
				}
			}
			else {
				if (!is_home() && !is_author() && !is_tag()) {
					if ($post) {
						$hreflang_pro_data = get_post_meta($post->ID);
						$sortable_hreflang_pro_data = array();
						if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
							foreach($hreflang_pro_data as $key => $value) {
								if (stristr($key, 'hreflang')) {
									$sortable_hreflang_pro_data[$key] = $value;
								}
							}
						}
						hreflang_pro_sorted_array_parse_tags($sortable_hreflang_pro_data);
					}
				}
				else {
					if ('1' == get_option('hreflang_pro_allow_blog_tags') && !is_author()) {
						$hreflang_pro_data = get_option('hreflang_pro_blog_tags');
						$sortable_hreflang_pro_data = array();
						if (is_array($hreflang_pro_data['href']) && !empty($hreflang_pro_data['href'])) {
							for ($n = 0; $n < count($hreflang_pro_data['href']); $n++) {
								if ($hreflang_pro_data['hrefregion'][$n] != '') {
									$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n].'_'.$hreflang_pro_data['hrefregion'][$n]] = array($hreflang_pro_data['href'][$n]);
								}
								else {
									$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n]] = array($hreflang_pro_data['href'][$n]);
								}
							}
						}
						hreflang_pro_sorted_array_parse_tags($sortable_hreflang_pro_data);
					}
				}
			}
		}		
	}
}

function hreflang_pro_sorted_array_parse_tags($array) {
	krsort($array);
	if (is_array($array) && !empty($array)) {
		$metatag = "\n".'<!-- This webpage contains hreflang tags by the '.PLUGIN_NAME.' plugin '.HREFLANG_PRO_VERSION.' - '.PLUGIN_URI.' -->'."\n";
		foreach($array as $key=>$value) {
			$key_array = explode('-',$key);
			if (count($key_array) == 3) {
				$lang = 'x-default';
			}
			else {
				$lang = $key_array[1];
			}
			if ($lang == 'Select one') {
				continue;
			}
			$metatag .= '<link rel="alternate" href="'.$value[0].'" hreflang="'.str_replace('_','-', $lang).'" />'."\n";
		}
		$metatag .= '<!-- / '.PLUGIN_NAME.' plugin. -->'."\n";
		echo $metatag;
	}
}

function hreflang_pro_save_meta_data() {
	global $post;

	// Verify nonce
	if ( ! isset( $_POST['hreflang_pro_nonce'] ) || ! wp_verify_nonce( $_POST['hreflang_pro_nonce'], 'hreflang_pro_save_meta_data_action' ) ) {
		return;
	}

	// Check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	//New added: 4/12/2017 start
	if (isset($_POST['html-lang']) && ($_POST['html-lang'] != '')) {
		$html_lang = update_post_meta($post->ID,'html_lang', sanitize_text_field($_POST['html-lang']));
	}
	else {
		if (is_object($post)) {
			delete_post_meta($post->ID, 'html_lang');
		}
	}
	if (isset($_POST['html-region']) && ($_POST['html-region'] != '')) {
		$html_lang = update_post_meta($post->ID,'html_region', sanitize_text_field($_POST['html-region']));
	}
	else {
		if (is_object($post)) {
			delete_post_meta($post->ID, 'html_region');			
		}
	}
	//New added: 4/12/2017 end
	// Sanitize arrays first
	$hreflang_href = isset($_POST['hreflang-href']) && is_array($_POST['hreflang-href']) ? array_map('esc_url_raw', $_POST['hreflang-href']) : array();
	$hreflang_lang = isset($_POST['hreflang-lang']) && is_array($_POST['hreflang-lang']) ? array_map('sanitize_text_field', $_POST['hreflang-lang']) : array();
	$hreflang_region = isset($_POST['hreflang-region']) && is_array($_POST['hreflang-region']) ? array_map('sanitize_text_field', $_POST['hreflang-region']) : array();

	if (!empty($hreflang_href)) {
		$i = 0;
		$hreflang_pro_data = get_post_meta($post->ID);
		if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
			foreach($hreflang_pro_data as $key=>$value ) {
				if (stristr($key,'hreflang')) {
					$key_array = explode('-',$key);
					if (count($key_array) == 2) {
						$lang_region = $key_array[1];
						if (stristr($lang_region, '_')) {
							$lang_region_array = explode('_',$key_array[1]);
							$lang = $lang_region_array[0];
							$region = $lang_region_array[1];
							delete_post_meta($post->ID, 'hreflang-'.$lang.'_'.$region);
						}
						else {
							$lang = $lang_region;
							$region = '';
							delete_post_meta($post->ID, 'hreflang-'.$lang);
						}
					}
					elseif (count($key_array) == 3) {
						delete_post_meta($post->ID, 'hreflang-x-default');
					}
				}
			}
		}
		foreach($hreflang_href as $href) {
			if (trim($href) == '' || $href == 'Select one') {
				$i++;
				continue;
			}
			else {
				if (isset($hreflang_region[$i]) && ($hreflang_region[$i] != '')) {
					update_post_meta($post->ID,'hreflang-'.$hreflang_lang[$i].'_'.$hreflang_region[$i],$href);
					$i++;
				}
				else {
					update_post_meta($post->ID,'hreflang-'.$hreflang_lang[$i],$href);
					$i++;
				}
			}
		}
	}
}

function hreflang_pro_meta_box() {
	global $post,$langcode2Name,$allregions;
	$keys = array();
	wp_nonce_field('hreflang_pro_save_meta_data_action', 'hreflang_pro_nonce');
	$hreflang_pro_data = get_post_meta($post->ID);
	$html_lang = get_post_meta($post->ID,'html_lang',true);
	$html_region = get_post_meta($post->ID,'html_region',true);
	echo '<div class="hreflang-pro-html-lang">';
	echo '<label for="meta-box-dropdown">'.__('HTML Tag Language','hreflang-tags-pro').' </label>';
	echo '<select name="html-lang" id="html-lang">';
	echo '<option value="">'.__('Select one','hreflang-tags-by-dcgws').'</option>';
	foreach ($langcode2Name as $language) {
		echo '<option value="'.$language['code'].'"';
		if ($language['code'] == $html_lang) {
			 echo ' selected="selected"';
		}
		echo '>'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
	}
	echo '</select>';
	echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').' </label>';
	echo '<select name="html-region" id="html-region">';
	echo '<option value>'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
	foreach ($allregions as $region) {
		echo '<option value="'.$region->alpha2Code.'"';
		if ($region->alpha2Code == $html_region) {
			 echo ' selected="selected"';
		}
		echo '>'.$region->name.'</option>';
	}
	echo '</select>';
	echo ' <span>'.__('You can use this to set the &lt;html lang=""&gt; attribute for improved SEO.','hreflang-tags-pro').'</span>';
	echo '</div><hr/>';
	$metatag = '';
	echo '<div class="href-container">';
	if (is_array($hreflang_pro_data)) {
		foreach($hreflang_pro_data as $key=>$value ) {
			if (stristr($key,'hreflang')) {
				$keys[] = $key;
				$values[] = $value;
			}
	  	}
   	}
	if (count($keys) == 0) {
		echo '<div id="hreflang-1" class="href-lang">';
		echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label> ';
    	echo '<input name="hreflang-href[]" type="text" value="">';
	    echo ' <label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label> ';
	    echo '<select name="hreflang-lang[]" class="hreflang-lang">';
     	echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
			foreach ($langcode2Name as $language) {
				echo '<option value="'.$language['code'].'">'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
			}
			echo '</select>';
			echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
	    echo '<select name="hreflang-region[]" class="hreflang-region">';
				echo '<option value>'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
				foreach ($allregions as $region) {
					echo '<option value="'.$region->alpha2Code.'">'.$region->name.'</option>';
				}
    	echo '</select>';
    	echo ' <button class="add-new-hreflang-tag"><span class="dashicons dashicons-plus"></span></button>';
		echo '</div>';
	}
	$n = 0;
	if (is_array($keys) && $keys != array()) {
		foreach ($keys as $key) {
			$key_array = explode('-',$key);
			$href_lang = $key_array[1];
			$href_array = explode('_',$href_lang);
			if (count($href_array) > 1) {
				$href_lang = $href_array[0];
				$href_region = $href_array[1];
			}
			else {
				$href_region = '';
			}
			if (count($key_array) == 3) {
				$href_lang = 'x-default';
			}
			echo '<div id="hreflang-'.($n+1).'" class="href-lang">';
			echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label> ';
			echo '<input name="hreflang-href[]" type="text" value="'.$values[$n][0].'">';
			echo ' <label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label> ';
	    echo '<select name="hreflang-lang[]" class="hreflang-lang">';
     	echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
			foreach ($langcode2Name as $language) {
				echo '<option value="'.$language['code'].'"';
				if ($language['code'] == $href_lang) {
					 echo ' selected="selected"';
				}
				echo '>'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
			}
    	echo '</select>';
			echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
	    echo '<select name="hreflang-region[]" class="hreflang-region">';
			echo '<option value="" '.($href_region == '' ? 'selected="selected"' : '').'>'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
			foreach ($allregions as $region) {
				echo '<option value="'.$region->alpha2Code.'"';
				if ($region->alpha2Code == $href_region) {
					 echo ' selected="selected"';
				}
				echo '>'.$region->name.'</option>';
			}
    	echo '</select>';
   			echo '<span id="validate-'.($n+1).'" class="validation-response-holder">';
   			if ( ($n+1) == count($keys)) {
   				echo ' <button class="add-new-hreflang-tag"><span class="dashicons dashicons-plus"></span></button>';
   				echo ' <button class="remove-new-hreflang-tag"><span class="dashicons dashicons-minus"></span></button>';
		    }
		    echo '</span>';
			echo '</div>';
	    	$n++;
		}
		echo '<div id="validation-box"><p><button class="button button-primary" data-id="'.$post->ID.'" id="validate-hreflang" onclick="return false;">'.__('Validate','hreflang-tags-pro').'</button><span id="validation-response"></span></p></div>';
	}
	echo '</div>';
}

function add_hreflang_pro_meta_box() {
	if (is_array(get_option('hreflang_post_types'))) {
		foreach (get_option('hreflang_post_types') as $hreflang_pro_post_type) {
			add_meta_box('hreflang-meta-box','HREFLANG Tags','hreflang_pro_meta_box',$hreflang_pro_post_type, 'advanced', 'high', null);
		}
	}
}

function add_hreflang_pro_to_category_form() {
	global $allregions, $langcode2Name;
	?>
	<div class="hreflang-taxonomy-container href-container-cat">
		<div class="hreflang-taxonomy-header">
			<h3><?php _e('HREFLANG Tags', 'hreflang-tags-pro'); ?></h3>
		</div>

		<div id="hreflang-cat-1" class="hreflang-entry href-lang">
			<div class="hreflang-entry-fields">
				<div class="hreflang-field field-url">
					<label><?php _e('Alternative URL', 'hreflang-tags-pro'); ?></label>
					<input name="hreflang-href[]" type="text" value="" placeholder="https://example.com/category">
				</div>

				<div class="hreflang-field field-lang">
					<label><?php _e('Language', 'hreflang-tags-pro'); ?></label>
					<select name="hreflang-lang[]" class="hreflang-lang">
						<option><?php _e('Select one', 'hreflang-tags-by-dcgws'); ?></option>
						<?php foreach ($langcode2Name as $language): ?>
							<option value="<?php echo esc_attr($language['code']); ?>">
								<?php echo esc_html(preg_replace("/\([^)]+\)/", "", $language['name'])); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="hreflang-field field-region">
					<label><?php _e('Region', 'hreflang-tags-pro'); ?></label>
					<select name="hreflang-region[]" class="hreflang-region">
						<option value=""><?php _e('No region/default', 'hreflang-tags-by-dcgws'); ?></option>
						<?php foreach ($allregions as $region): ?>
							<option value="<?php echo esc_attr($region->alpha2Code); ?>">
								<?php echo esc_html($region->name); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="hreflang-actions">
					<button type="button" class="hreflang-btn add-new-cat-hreflang-tag">
						<span class="dashicons dashicons-plus"></span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}


function add_hreflang_pro_to_category_edit_form($term) {
	global $allregions, $langcode2Name;

	$keys = array();
	$values = array();
	$hreflang_pro_data = get_term_meta($term->term_id);

	if (is_array($hreflang_pro_data)) {
		foreach($hreflang_pro_data as $key => $value) {
			if (stristr($key, 'hreflang')) {
				$keys[] = $key;
				$values[] = $value;
			}
		}
	}
	?>
	<tr class="term-hreflang-wrap">
		<th scope="row">
			<label><?php _e('HREFLANG Tags', 'hreflang-tags-pro'); ?></label>
		</th>
		<td class="term-hreflang-data">
			<div class="hreflang-taxonomy-container">
				<div class="hreflang-taxonomy-header">
					<h3><?php _e('HREFLANG Tags', 'hreflang-tags-pro'); ?></h3>
				</div>
				<?php
				if (count($keys) == 0) {
					// No existing data - show empty form
					?>
					<div id="hreflang-cat-edit-1" class="hreflang-entry href-lang">
						<div class="hreflang-entry-fields">
							<div class="hreflang-field field-url">
								<label><?php _e('Alternative URL', 'hreflang-tags-pro'); ?></label>
								<input name="hreflang-href[]" type="text" value="" placeholder="https://example.com/category">
							</div>
							<div class="hreflang-field field-lang">
								<label><?php _e('Language', 'hreflang-tags-pro'); ?></label>
								<select name="hreflang-lang[]" class="hreflang-lang">
									<option><?php _e('Select one', 'hreflang-tags-by-dcgws'); ?></option>
									<?php foreach ($langcode2Name as $language): ?>
										<option value="<?php echo esc_attr($language['code']); ?>">
											<?php echo esc_html(preg_replace("/\([^)]+\)/", "", $language['name'])); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="hreflang-field field-region">
								<label><?php _e('Region', 'hreflang-tags-pro'); ?></label>
								<select name="hreflang-region[]" class="hreflang-region">
									<option value=""><?php _e('No region/default', 'hreflang-tags-by-dcgws'); ?></option>
									<?php foreach ($allregions as $region): ?>
										<option value="<?php echo esc_attr($region->alpha2Code); ?>">
											<?php echo esc_html($region->name); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="hreflang-actions">
								<button type="button" class="hreflang-btn add-new-cat-edit-hreflang-tag" title="<?php _e('Add Entry', 'hreflang-tags-pro'); ?>">
									<span class="dashicons dashicons-plus"></span>
								</button>
							</div>
						</div>
					</div>
					<?php
				} else {
					// Display existing data
					$n = 0;
					foreach ($keys as $key) {
						$key_array = explode('-', $key);
						$href_lang = $key_array[1];
						$href_array = explode('_', $href_lang);

						if (count($href_array) > 1) {
							$href_lang = $href_array[0];
							$href_region = $href_array[1];
						} else {
							$href_region = '';
						}

						if (count($key_array) == 3) {
							$href_lang = 'x-default';
						}
						?>
						<div id="hreflang-cat-edit-<?php echo ($n + 1); ?>" class="hreflang-entry href-lang">
							<div class="hreflang-entry-fields">
								<div class="hreflang-field field-url">
									<label><?php _e('Alternative URL', 'hreflang-tags-pro'); ?></label>
									<input name="hreflang-href[]" type="text" value="<?php echo esc_attr($values[$n][0]); ?>" placeholder="https://example.com/category">
								</div>
								<div class="hreflang-field field-lang">
									<label><?php _e('Language', 'hreflang-tags-pro'); ?></label>
									<select name="hreflang-lang[]" class="hreflang-lang">
										<option><?php _e('Select one', 'hreflang-tags-by-dcgws'); ?></option>
										<?php foreach ($langcode2Name as $language): ?>
											<option value="<?php echo esc_attr($language['code']); ?>" <?php selected($language['code'], $href_lang); ?>>
												<?php echo esc_html(preg_replace("/\([^)]+\)/", "", $language['name'])); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="hreflang-field field-region">
									<label><?php _e('Region', 'hreflang-tags-pro'); ?></label>
									<select name="hreflang-region[]" class="hreflang-region">
										<option value="" <?php selected($href_region, ''); ?>><?php _e('No region/default', 'hreflang-tags-by-dcgws'); ?></option>
										<?php foreach ($allregions as $region): ?>
											<option value="<?php echo esc_attr($region->alpha2Code); ?>" <?php selected($region->alpha2Code, $href_region); ?>>
												<?php echo esc_html($region->name); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="hreflang-actions">
									<?php if (($n + 1) == count($keys)): ?>
										<button type="button" class="hreflang-btn add-new-cat-edit-hreflang-tag" title="<?php _e('Add Entry', 'hreflang-tags-pro'); ?>">
											<span class="dashicons dashicons-plus"></span>
										</button>
										<button type="button" class="hreflang-btn btn-remove remove-new-cat-edit-hreflang-tag" title="<?php _e('Remove Entry', 'hreflang-tags-pro'); ?>">
											<span class="dashicons dashicons-minus"></span>
										</button>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php
						$n++;
					}
				}
				?>
			</div>
		</td>
	</tr>
	<?php
}

function  href_save_term_meta_data($term_id) {
	// Check permissions
	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	// Sanitize arrays first
	$hreflang_href = isset($_POST['hreflang-href']) && is_array($_POST['hreflang-href']) ? array_map('esc_url_raw', $_POST['hreflang-href']) : array();
	$hreflang_lang = isset($_POST['hreflang-lang']) && is_array($_POST['hreflang-lang']) ? array_map('sanitize_text_field', $_POST['hreflang-lang']) : array();
	$hreflang_region = isset($_POST['hreflang-region']) && is_array($_POST['hreflang-region']) ? array_map('sanitize_text_field', $_POST['hreflang-region']) : array();

	if (!empty($hreflang_href)) {
		$i = 0;
		$hreflang_pro_data = get_term_meta($term_id);
		if (is_array($hreflang_pro_data)) {
			foreach($hreflang_pro_data as $key=>$value ) {
				if (stristr($key,'hreflang')) {
					$key_array = explode('-',$key);
					if (count($key_array) == 2) {
						$lang_region = $key_array[1];
						if (stristr($lang_region, '_')) {
							$lang_region_array = explode('_',$key_array[1]);
							$lang = $lang_region_array[0];
							$region = $lang_region_array[1];
							delete_term_meta($term_id, 'hreflang-'.$lang.'_'.$region);
						}
						else {
							$lang = $lang_region;
							$region = '';
							delete_term_meta($term_id, 'hreflang-'.$lang);
						}
					}
					elseif (count($key_array) == 3) {
						delete_term_meta($term_id, 'hreflang-x-default');
					}
				}
			}
		}
		$i = 0;
		foreach($hreflang_href as $href) {
			if (trim($href) == '' || $href == 'Select one') {
				$i++;
				continue;
			}
			else {
				if (isset($hreflang_region[$i]) && ($hreflang_region[$i] != '')) {
					update_term_meta($term_id,'hreflang-'.$hreflang_lang[$i].'_'.$hreflang_region[$i],$href);
					$i++;
				}
				else {
					update_term_meta($term_id,'hreflang-'.$hreflang_lang[$i],$href);
					$i++;
				}
			}
		}
	}
}

function hreflang_pro_html_generator() {
	global $allregions, $langcode2Name;
	echo '<div class="href-container-gen">';
	echo '<div id="hreflang-gen-1" class="href-lang">';
	echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label> ';
	echo '<input name="hreflang-href[]" class="hreflang-href" type="text" value=""> ';
	echo '<label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label> ';
	echo '<select name="hreflang-lang[]" class="hreflang-lang" class="hreflang-lang">';
	echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
	foreach ($langcode2Name as $language) {
		echo '<option value="'.$language['code'].'">'.$language['name'].'</option>';
	}
	echo '</select>';
	echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
	echo '<select name="hreflang-region[]" class="hreflang-region" class="hreflang-region">';
	echo '<option value="" '.($html_region == '' ? 'selected="selected"' : '').'>'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
	foreach ($allregions as $region) {
		echo '<option value="'.$region->alpha2Code.'">'.$region->name.'</option>';
	}
	echo '</select>';
	echo '<button class="add-new-gen-hreflang-tag"><span class="dashicons dashicons-plus"></span></button>';
	echo '</div>';
	echo '</div>';
	echo '<p class="submit">';
	echo '<button id="generate_tags" class="button button-primary" onClick="return false;">'.__('Generate Tags','hreflang-tags-pro').'</button>';
	echo '</p>';
	echo '<div class="hreflang-response">';
	echo '<textarea id="hreflang-html" onclick="this.select()" style="display:none;" cols="67"></textarea>';
	echo '</div>';
}

function hreflang_pro_blog_tags() {
	global $allregions,$langcode2Name;

	$blog_tags = get_option('hreflang_pro_blog_tags');
	echo '<div class="href-container-blog">';
	if (is_array($blog_tags)) {
		if (count($blog_tags['href']) == 0) {
			echo '<div id="hreflang-blog-1" class="href-lang">';
			echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label>';
			echo '<input name="hreflang_pro_blog_tags[href][]" class="hreflang-href" type="text" value="">';
			echo '<label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label>';
			echo '<select name="hreflang_pro_blog_tags[hreflang][]" class="hreflang-lang" class="hreflang-lang">';
			echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
			foreach ($langcode2Name as $language) {
				echo '<option value="'.$language['code'].'">'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
			}
			echo '</select>';
			echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
			echo '<select name="hreflang_pro_blog_tags[hrefregion][]" class="hreflang-region" class="hreflang-region">';
			echo '<option value="">'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
			foreach ($allregions as $region) {
				echo '<option value="'.$region->alpha2Code.'">'.$region->name.'</option>';
			}
			echo '</select>';
			echo '<button class="add-new-blog-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button>';
			echo '</div>';
		}
		else {
			for ($n = 0; $n < count($blog_tags['href']); $n++) {
				echo '<div id="hreflang-blog-'.$n.'" class="href-lang">';
				echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label>';
				echo '<input name="hreflang_pro_blog_tags[href][]" class="hreflang-href" type="text" value="'.$blog_tags['href'][$n].'">';
				echo '<label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label>';
				echo '<select name="hreflang_pro_blog_tags[hreflang][]" class="hreflang-lang" class="hreflang-lang">';
				echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
				foreach ($langcode2Name as $language) {
					echo '<option value="'.$language['code'].'"';
					if ($language['code'] == $blog_tags['hreflang'][$n]) {
						 echo ' selected="selected"';
					}
					echo '>'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
				}
				echo '</select>';
				echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
			echo '<select name="hreflang_pro_blog_tags[hrefregion][]" class="hreflang-region">';
				echo '<option value="" '.($blog_tags['hrefregion'][$n] == '' ? 'selected="selected"' : '').'>'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
				foreach ($allregions as $region) {
					echo '<option value="'.$region->alpha2Code.'"';
					if ($region->alpha2Code == $blog_tags['hrefregion'][$n]) {
						 echo ' selected="selected"';
					}
					echo '>'.$region->name.'</option>';
				}
			echo '</select>';
				if ( ($n+1) == count($blog_tags['href'])) {
					echo '<button class="add-new-blog-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button>';
					echo '<button class="remove-new-blog-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-minus"></span></button>';
				}
				echo '</div>';
			}
		}		
	}
	else {
		echo '<div id="hreflang-blog-1" class="href-lang">';
		echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label>';
		echo '<input name="hreflang_pro_blog_tags[href][]" class="hreflang-href" type="text" value="">';
		echo '<label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label>';
		echo '<select name="hreflang_pro_blog_tags[hreflang][]" class="hreflang-lang" class="hreflang-lang">';
		echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
		foreach ($langcode2Name as $language) {
			echo '<option value="'.$language['code'].'">'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
		}
		echo '</select>';
		echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
		echo '<select name="hreflang_pro_blog_tags[hrefregion][]" class="hreflang-region" class="hreflang-region">';
		echo '<option value="">'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
		foreach ($allregions as $region) {
			echo '<option value="'.$region->alpha2Code.'">'.$region->name.'</option>';
		}
		echo '</select>';
		echo '<button class="add-new-blog-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button>';
		echo '</div>';
	}
	echo '</div>';
}

function hreflang_pro_shop_tags() {
	global $allregions,$langcode2Name;

	echo '<div class="href-container-shop">';
	$shop_tags = get_option('hreflang_pro_shop_tags');
	if (is_array($shop_tags)) {
		if (count($shop_tags['href']) == 0) {
			echo '<div id="hreflang-shop-1" class="href-lang">';
			echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label>';
			echo '<input name="hreflang_pro_shop_tags[href][]" class="hreflang-href" type="text" value="">';
			echo '<label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label>';
			echo '<select name="hreflang_pro_shop_tags[hreflang][]" class="hreflang-lang" class="hreflang-lang">';
			echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
			foreach ($langcode2Name as $language) {
				echo '<option value="'.$language['code'].'">'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
			}
			echo '</select>';
			echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
			echo '<select name="hreflang_pro_shop_tags[hrefregion][]" class="hreflang-region" class="hreflang-region">';
			echo '<option value="">'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
			foreach ($allregions as $region) {
				echo '<option value="'.$region->alpha2Code.'">'.$region->name.'</option>';
			}
			echo '</select>';
			echo '<button class="add-new-shop-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button>';
			echo '</div>';
		}
		else {
			for ($n = 0; $n < count($shop_tags['href']); $n++) {
				echo '<div id="hreflang-shop-'.$n.'" class="href-lang">';
				echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label>';
				echo '<input name="hreflang_pro_shop_tags[href][]" class="hreflang-href" type="text" value="'.$shop_tags['href'][$n].'">';
				echo '<label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label>';
				echo '<select name="hreflang_pro_shop_tags[hreflang][]" class="hreflang-lang" class="hreflang-lang">';
				echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
				foreach ($langcode2Name as $language) {
					echo '<option value="'.$language['code'].'"';
					if ($language['code'] == $shop_tags['hreflang'][$n]) {
						 echo ' selected="selected"';
					}
					echo '>'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
				}
				echo '</select>';
				echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
			echo '<select name="hreflang_pro_shop_tags[hrefregion][]" class="hreflang-region" class="hreflang-region">';
				echo '<option value="" '.($shop_tags['hrefregion'][$n] == '' ? 'selected="selected"' : '').'>'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
				foreach ($allregions as $region) {
					echo '<option value="'.$region->alpha2Code.'"';
					if ($region->alpha2Code == $shop_tags['hrefregion'][$n]) {
						 echo ' selected="selected"';
					}
					echo '>'.$region->name.'</option>';
				}
			echo '</select>';
				if ( ($n+1) == count($shop_tags['href'])) {
					echo '<button class="add-new-shop-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button>';
					echo '<button class="remove-new-shop-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-minus"></span></button>';
				}
				echo '</div>';
			}
		}
	}
	else {
		echo '<div id="hreflang-shop-1" class="href-lang">';
		echo '<label for="hreflang-href">'.__('Alternative URL','hreflang-tags-pro').'</label>';
		echo '<input name="hreflang_pro_shop_tags[href][]" class="hreflang-href" type="text" value="">';
		echo '<label for="meta-box-dropdown">'.__('Language','hreflang-tags-pro').'</label>';
		echo '<select name="hreflang_pro_shop_tags[hreflang][]" class="hreflang-lang" class="hreflang-lang">';
		echo '<option>'.__('Select one','hreflang-tags-by-dcgws').'</option>';
		foreach ($langcode2Name as $language) {
			echo '<option value="'.$language['code'].'">'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
		}
		echo '</select>';
		echo ' <label for="meta-box-dropdown">'.__('Region','hreflang-tags-pro').'</label> ';
		echo '<select name="hreflang_pro_shop_tags[hrefregion][]" class="hreflang-region" class="hreflang-region">';
		echo '<option value="">'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
		foreach ($allregions as $region) {
			echo '<option value="'.$region->alpha2Code.'">'.$region->name.'</option>';
		}
		echo '</select>';
		echo '<button class="add-new-shop-hreflang-tag" onClick="return false;"><span class="dashicons dashicons-plus"></span></button>';
		echo '</div>';
	}
	echo '</div>';
}

function hreflang_pro_enqueue($hook) {
	if (is_admin() && is_user_logged_in()) {
		wp_enqueue_script( 'hreflang_pro_tags_js', plugin_dir_url( HREFLANG_PRO_PLUGIN_FILE ) . 'assets/js/hreflang-tags-pro.js',array('jquery') );
		wp_localize_script( 'hreflang_pro_tags_js', 'hreflang_ajax', array(
			'nonce' => wp_create_nonce( 'hreflang_pro_ajax_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );
		wp_enqueue_style( 'hreflang_pro_styles', plugin_dir_url( HREFLANG_PRO_PLUGIN_FILE ) . 'assets/css/hreflang-tags-pro.css');

		// Load improved taxonomy CSS on term edit/add pages
		if ( strpos( $hook, 'term.php' ) !== false || strpos( $hook, 'edit-tags.php' ) !== false ) {
			wp_enqueue_style( 'hreflang_taxonomy_styles', plugin_dir_url( HREFLANG_PRO_PLUGIN_FILE ) . 'assets/css/hreflang-taxonomy.css', array(), '2.0.0' );
		}
	}
}

/**
 * Register REST API fields for hreflang data
 */
function hreflang_pro_register_rest_fields() {
	// Get post types that have hreflang enabled, or use all public post types
	$post_types = get_option('hreflang_post_types');

	if (!is_array($post_types) || empty($post_types)) {
		// Get all public post types that support custom fields
		$post_types = get_post_types(array(
			'public' => true,
			'show_in_rest' => true
		));
	}

	// Filter to only include actual post types (not taxonomies)
	$valid_post_types = array();
	foreach ($post_types as $post_type) {
		if (post_type_exists($post_type)) {
			$valid_post_types[] = $post_type;
		}
	}

	if (empty($valid_post_types)) {
		return;
	}

	// Register hreflang_tags field
	register_rest_field(
		$valid_post_types,
		'hreflang_tags',
		array(
			'get_callback'    => 'hreflang_pro_rest_get_hreflang_tags',
			'update_callback' => 'hreflang_pro_rest_update_hreflang_tags',
			'schema'          => array(
				'description' => __('HREFLANG alternate language URLs', 'hreflang-tags-pro'),
				'type'        => 'object',
				'context'     => array('view', 'edit'),
			),
		)
	);

	// Register html_lang field
	register_rest_field(
		$valid_post_types,
		'html_lang',
		array(
			'get_callback'    => 'hreflang_pro_rest_get_html_lang',
			'update_callback' => 'hreflang_pro_rest_update_html_lang',
			'schema'          => array(
				'description' => __('HTML language attribute', 'hreflang-tags-pro'),
				'type'        => 'string',
				'context'     => array('view', 'edit'),
			),
		)
	);

	// Register html_region field
	register_rest_field(
		$valid_post_types,
		'html_region',
		array(
			'get_callback'    => 'hreflang_pro_rest_get_html_region',
			'update_callback' => 'hreflang_pro_rest_update_html_region',
			'schema'          => array(
				'description' => __('HTML region attribute', 'hreflang-tags-pro'),
				'type'        => 'string',
				'context'     => array('view', 'edit'),
			),
		)
	);
}

/**
 * Get hreflang tags for REST API
 */
function hreflang_pro_rest_get_hreflang_tags($object, $field_name, $request) {
	$post_id = $object['id'];
	$hreflang_data = get_post_meta($post_id);
	$hreflang_tags = array();

	if (is_array($hreflang_data)) {
		foreach ($hreflang_data as $key => $value) {
			if (stristr($key, 'hreflang-')) {
				// Extract language code from meta key
				$lang_code = str_replace('hreflang-', '', $key);
				// Get the URL value
				$url = is_array($value) ? $value[0] : $value;
				$hreflang_tags[$lang_code] = $url;
			}
		}
	}

	return $hreflang_tags;
}

/**
 * Update hreflang tags via REST API
 */
function hreflang_pro_rest_update_hreflang_tags($value, $object, $field_name) {
	$post_id = $object->ID;

	if (!current_user_can('edit_post', $post_id)) {
		return new WP_Error(
			'rest_cannot_edit',
			__('Sorry, you are not allowed to edit this post.', 'hreflang-tags-pro'),
			array('status' => rest_authorization_required_code())
		);
	}

	// Delete existing hreflang meta
	$existing_meta = get_post_meta($post_id);
	if (is_array($existing_meta)) {
		foreach ($existing_meta as $key => $val) {
			if (stristr($key, 'hreflang-')) {
				delete_post_meta($post_id, $key);
			}
		}
	}

	// Add new hreflang meta
	if (is_array($value)) {
		foreach ($value as $lang_code => $url) {
			$sanitized_lang = sanitize_text_field($lang_code);
			$sanitized_url = esc_url_raw($url);

			if (!empty($sanitized_url)) {
				update_post_meta($post_id, 'hreflang-' . $sanitized_lang, $sanitized_url);
			}
		}
	}

	return true;
}

/**
 * Get html_lang for REST API
 */
function hreflang_pro_rest_get_html_lang($object, $field_name, $request) {
	$post_id = $object['id'];
	return get_post_meta($post_id, 'html_lang', true);
}

/**
 * Update html_lang via REST API
 */
function hreflang_pro_rest_update_html_lang($value, $object, $field_name) {
	$post_id = $object->ID;

	if (!current_user_can('edit_post', $post_id)) {
		return new WP_Error(
			'rest_cannot_edit',
			__('Sorry, you are not allowed to edit this post.', 'hreflang-tags-pro'),
			array('status' => rest_authorization_required_code())
		);
	}

	$sanitized_value = sanitize_text_field($value);

	if (empty($sanitized_value)) {
		delete_post_meta($post_id, 'html_lang');
	} else {
		update_post_meta($post_id, 'html_lang', $sanitized_value);
	}

	return true;
}

/**
 * Get html_region for REST API
 */
function hreflang_pro_rest_get_html_region($object, $field_name, $request) {
	$post_id = $object['id'];
	return get_post_meta($post_id, 'html_region', true);
}

/**
 * Update html_region via REST API
 */
function hreflang_pro_rest_update_html_region($value, $object, $field_name) {
	$post_id = $object->ID;

	if (!current_user_can('edit_post', $post_id)) {
		return new WP_Error(
			'rest_cannot_edit',
			__('Sorry, you are not allowed to edit this post.', 'hreflang-tags-pro'),
			array('status' => rest_authorization_required_code())
		);
	}

	$sanitized_value = sanitize_text_field($value);

	if (empty($sanitized_value)) {
		delete_post_meta($post_id, 'html_region');
	} else {
		update_post_meta($post_id, 'html_region', $sanitized_value);
	}

	return true;
}


function hreflang_pro_bulk_enqueue($hook) {
	global $hreflang_pro_bulk_editor_page;
    if ( $hreflang_pro_bulk_editor_page != $hook ) {
        return;
    }
	if (is_admin() && is_user_logged_in()) {
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'wplink' );
		wp_register_style( 'tinymce_stylesheet', 'https://cdn.jsdelivr.net/npm/tinymce@5/skins/ui/oxide/content.min.css' );
		wp_enqueue_style( 'tinymce_stylesheet' );
    	wp_enqueue_script( 'hreflang_pro_bulk_editor_js', plugin_dir_url( HREFLANG_PRO_PLUGIN_FILE ) . 'assets/js/hreflang-bulk-editor-120.js',array('jquery') );
		wp_localize_script( 'hreflang_pro_bulk_editor_js', 'hreflang_ajax', array(
			'nonce' => wp_create_nonce( 'hreflang_pro_ajax_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );
	}
}

function hreflang_pro_save_from_bulk_editor() {
	// Security checks
	check_ajax_referer( 'hreflang_pro_ajax_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
	}

	$site_url = get_site_url();
	$site_url_components = parse_url($site_url);

	// Sanitize post_id
	$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => 'Invalid post ID' ), 400 );
	}

	// Sanitize arrays
	$urls = isset($urls) && is_array($urls) ? array_map('esc_url_raw', $urls) : array();
	$langs = isset($langs) && is_array($langs) ? array_map('sanitize_text_field', $langs) : array();
	$regions = isset($regions) && is_array($regions) ? array_map('sanitize_text_field', $regions) : array();

	if (isset($_POST['html_lang']) && ($_POST['html_lang'] != "")) {
		$html_lang = sanitize_text_field( $_POST['html_lang'] );
		if (isset($_POST['html_region']) && ($_POST['html_region'] != "")) {
			$html_region = sanitize_text_field( $_POST['html_region'] );
		}
		else {
			$html_region = "";
		}
		$hreflang_pro_data = get_post_meta($post_id);
		if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
			foreach($hreflang_pro_data as $key=>$value ) {
				if ($key == "html_lang" || $key == "html_region") {
					delete_post_meta($post_id, $key, $value);
				}
			}
		}
		update_post_meta($post_id, "html_lang", $html_lang);
		if ($html_region != "") {
			update_post_meta($post_id, "html_region", $html_region);
		}
	}
	if (isset($urls) && !empty($urls)) {
		$i = 0;
		$hreflang_pro_data = get_post_meta($post_id);
		if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
			foreach($hreflang_pro_data as $key=>$value ) {
				if (stristr($key,'hreflang')) {
					$key_array = explode('-',$key);
					if (count($key_array) == 2) {
						$lang_region = $key_array[1];
						if (stristr($lang_region, '_')) {
							$lang_region_array = explode('_',$key_array[1]);
							$lang = $lang_region_array[0];
							$region = $lang_region_array[1];
							delete_post_meta($post_id, 'hreflang-'.$lang.'_'.$region);
						}
						else {
							$lang = $lang_region;
							$region = '';
							delete_post_meta($post_id, 'hreflang-'.$lang);
						}
					}
					elseif (count($key_array) == 3) {
						delete_post_meta($post_id, 'hreflang-x-default');
					}
				}
			}
		}
		if (is_array($urls)) {
			foreach($urls as $url) {
				if (trim($url) == '') {
					$i++;
					continue;
				}
				elseif (trim($url) == '{get_permalink}') {
					if (isset($regions[$i]) && ($regions[$i] != '')) {
						update_post_meta($post_id,'hreflang-'.$langs[$i].'_'.$regions[$i],get_permalink($post_id));
						$i++;
					}
					else {
						update_post_meta($post_id,'hreflang-'.$langs[$i],get_permalink($post_id));
						$i++;
					}
				}
				elseif (stristr(trim($url),'{scheme}') || stristr(trim($url),'{host}') || stristr(trim($url),'{slug}')) {
					$permalink = get_permalink($post_id);
					$components = parse_url($permalink);
					//Get true path
					$components['path'] = str_replace($site_url_components['path'],'',$components['path']);
					if (stristr(trim($url),'{scheme}') && stristr(trim($url),'{host}') && stristr(trim($url),'{slug}')) {
						$new_text = hreflang_tags_pro_get_string_between($url,'{host}','{slug}');
						if (strlen($new_text) > 0) {
							$slug = str_replace($new_text,'',$components["path"]);
						}
						else {
							$slug = $components["path"];
						}
						$permalink = str_replace(array('{scheme}','{host}','{slug}'),array($components["scheme"],$components["host"],$slug),$url);
						if (isset($regions[$i]) && ($regions[$i] != '')) {
							update_post_meta($post_id,'hreflang-'.$langs[$i].'_'.$regions[$i],$permalink);
							$i++;
						}
						else {
							update_post_meta($post_id,'hreflang-'.$langs[$i],$permalink);
							$i++;
						}
					}
					elseif ( stristr(trim($url),'{host}') && stristr(trim($url),'{slug}') && !stristr(trim($url),'{scheme}') ) {
						$new_text = hreflang_tags_pro_get_string_between($url,'{host}','{slug}');
						if (strlen($new_text) > 0) {
							$slug = str_replace($new_text,'',$components["path"]);
						}
						else {
							$slug = $components["path"];
						}
						$permalink = str_replace(array('{host}','{slug}'),array($components["host"],$slug),$url);
						if (isset($regions[$i]) && ($regions[$i] != '')) {
							update_post_meta($post_id,'hreflang-'.$langs[$i].'_'.$regions[$i],$permalink);
							$i++;
						}
						else {
							update_post_meta($post_id,'hreflang-'.$langs[$i],$permalink);
							$i++;
						}
					}
					elseif ( !stristr(trim($url),'{host}') && stristr(trim($url),'{slug}') && stristr(trim($url),'{scheme}') ) {
						$new_text = hreflang_tags_pro_get_string_between($url,'{scheme}','{slug}');
						if (strlen($new_text) > 0) {
							$slug = str_replace($new_text,'',$components["path"]);
						}
						else {
							$slug = $components["path"];
						}
						$permalink = str_replace(array('{scheme}','{slug}'),array($components["scheme"],$slug),$url);
						if (isset($regions[$i]) && ($regions[$i] != '')) {
							update_post_meta($post_id,'hreflang-'.$langs[$i].'_'.$regions[$i],$permalink);
							$i++;
						}
						else {
							update_post_meta($post_id,'hreflang-'.$langs[$i],$permalink);
							$i++;
						}
					}
					elseif ( !stristr(trim($url),'{host}') && stristr(trim($url),'{slug}') && !stristr(trim($url),'{scheme}') ) {
						$slug = $components["path"];
						$permalink = str_replace('{slug}',$slug,$url);
						if (isset($regions[$i]) && ($regions[$i] != '')) {
							update_post_meta($post_id,'hreflang-'.$langs[$i].'_'.$regions[$i],$permalink);
							$i++;
						}
						else {
							update_post_meta($post_id,'hreflang-'.$langs[$i],$permalink);
							$i++;
						}
					}
				}
				else {
					if (isset($regions[$i]) && ($regions[$i] != '')) {
						update_post_meta($post_id,'hreflang-'.$langs[$i].'_'.$regions[$i],$url);
						$i++;
					}
					else {
						update_post_meta($post_id,'hreflang-'.$langs[$i],$url);
						$i++;
					}
				}
			}
		}
	}
	else {
		//No entries, delete all
		if (isset($post_id) && isset($_POST['action']) && ($_POST['action'] == 'hreflang_pro_save_from_bulk_editor')) {
			$i = 0;
			$hreflang_pro_data = get_post_meta($post_id);
			if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
				foreach($hreflang_pro_data as $key=>$value ) {
					if ($key == "html_lang" || $key == "html_region") {
						delete_post_meta($post_id, $key, $value);
					}
					if (stristr($key,'hreflang')) {
						$key_array = explode('-',$key);
						if (count($key_array) == 2) {
							$lang_region = $key_array[1];
							if (stristr($lang_region, '_')) {
								$lang_region_array = explode('_',$key_array[1]);
								$lang = $lang_region_array[0];
								$region = $lang_region_array[1];
								delete_post_meta($post_id, 'hreflang-'.$lang.'_'.$region);
							}
							else {
								$lang = $lang_region;
								$region = '';
								delete_post_meta($post_id, 'hreflang-'.$lang);
							}
						}
						elseif (count($key_array) == 3) {
							delete_post_meta($post_id, 'hreflang-x-default');
						}
					}
				}
			}
		}
	}
	header( "Content-Type: application/json" );
	echo json_encode(array('result' => 'success', 'site_url' => $site_url));
	die();
}

function hreflang_pro_delete_from_bulk_editor() {
	// Security checks
	check_ajax_referer( 'hreflang_pro_ajax_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
	}

	// Sanitize post_id
	$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => 'Invalid post ID' ), 400 );
	}

	$site_url = get_site_url();
	$hreflang_pro_data = get_post_meta($post_id);
	if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
		foreach($hreflang_pro_data as $key=>$value ) {
			if ($key == "html_lang" || $key == "html_region") {
				delete_post_meta($post_id, $key);
			}
			if (stristr($key,'hreflang')) {
				$key_array = explode('-',$key);
				if (count($key_array) == 2) {
					$lang_region = $key_array[1];
					if (stristr($lang_region, '_')) {
						$lang_region_array = explode('_',$key_array[1]);
						$lang = $lang_region_array[0];
						$region = $lang_region_array[1];
						delete_post_meta($post_id, 'hreflang-'.$lang.'_'.$region);
					}
					else {
						$lang = $lang_region;
						$region = '';
						delete_post_meta($post_id, 'hreflang-'.$lang);
					}
				}
				elseif (count($key_array) == 3) {
					delete_post_meta($post_id, 'hreflang-x-default');
				}
			}
		}
	}
	header( "Content-Type: application/json" );
	echo json_encode(array('result' => 'success', 'site_url' => $site_url));
	die();
}

function hreflang_pro_array_sort($array, $on, $order=SORT_ASC){

    $new_array = array();
    $sortable_array = array();
	if (is_array($array)) {
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}
	}
    return $new_array;
}

function validate_hreflang_tags() {
	// Security check with nonce
	if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) ) {
		wp_send_json_error( array( 'message' => 'Security token missing. Please refresh the page.' ), 403 );
	}

	// Verify nonce with wp_verify_nonce (more reliable than check_ajax_referer)
	$nonce_check = wp_verify_nonce( $_POST['nonce'], 'hreflang_pro_ajax_nonce' );

	// wp_verify_nonce returns:
	// 1 = nonce generated in past 12 hours
	// 2 = nonce generated in past 24 hours
	// false = invalid nonce
	if ( $nonce_check === false ) {
		wp_send_json_error( array(
			'message' => 'Security check failed. Please refresh the page and try again.',
			'nonce_age' => 'expired or invalid'
		), 403 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized - you do not have permission to edit posts' ), 403 );
	}

	// Check if user is logged in
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'You must be logged in to validate hreflang tags' ), 403 );
	}

	// Sanitize post_id
	$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => 'Invalid post ID' ), 400 );
	}

	$post = get_post($post_id);
	if ( ! $post ) {
		wp_send_json_error( array( 'message' => 'Post not found' ), 404 );
	}

	$post_meta = get_post_meta($post->ID);
	$data = array();

	if ( ! class_exists('DOMDocument') ) {
		wp_send_json_error( array( 'message' => 'DOMDocument class not available on this server' ), 500 );
	}

	foreach ( $post_meta as $key => $value ) {
		if ( stristr( $key, 'hreflang' ) ) {
			$key_array = explode( '-', $key );
			$lang = $key_array[1];
			$url = $value[0];

			// Use wp_remote_get instead of loadHTMLFile for better error handling
			$response = wp_remote_get( $url, array(
				'timeout'     => 10,
				'redirection' => 5,
				'user-agent'  => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
				'sslverify'   => false
			) );

			// Check for errors
			if ( is_wp_error( $response ) ) {
				$data[$lang]['result'] = 'success';
				$data[$lang]['validate'] = 'fail';
				$data[$lang]['message'] = 'Connection error: ' . $response->get_error_message();
				continue;
			}

			$response_code = wp_remote_retrieve_response_code( $response );
			$body = wp_remote_retrieve_body( $response );

			// Check HTTP status codes
			if ( $response_code == 404 ) {
				$data[$lang]['result'] = 'success';
				$data[$lang]['validate'] = 'fail';
				$data[$lang]['message'] = 'Missing. Header response 404.';
				continue;
			} else if ( $response_code == 301 || $response_code == 302 ) {
				$data[$lang]['result'] = 'success';
				$data[$lang]['validate'] = 'fail';
				$data[$lang]['message'] = 'Redirect detected. Header response ' . $response_code . '.';
				continue;
			} else if ( $response_code != 200 ) {
				$data[$lang]['result'] = 'success';
				$data[$lang]['validate'] = 'fail';
				$data[$lang]['message'] = 'Unexpected response code: ' . $response_code;
				continue;
			}

			// Parse HTML
			$dom = new DOMDocument();
			libxml_use_internal_errors( true );
			$dom->loadHTML( $body );
			libxml_clear_errors();

			$xpath = new DomXpath( $dom );
			$hrefs = $xpath->query( "//link[@hreflang]" );

			if ( $hrefs->length > 0 ) {
				$urls = array();
				foreach ( $hrefs as $href ) {
					$urls[] = $href->getAttribute( 'href' );
				}

				if ( ! in_array( get_permalink( $post->ID ), $urls ) ) {
					$data[$lang]['result'] = 'success';
					$data[$lang]['validate'] = 'fail';
					$data[$lang]['message'] = 'No return tags found. <span class="dashicons dashicons-warning"></span> <a target="_blank" href="https://webmasters.googleblog.com/2014/07/troubleshooting-hreflang-annotations-in.html">How to fix</a>';
				} else {
					$data[$lang]['result'] = 'success';
					$data[$lang]['validate'] = 'pass';
				}
			} else {
				$data[$lang]['result'] = 'success';
				$data[$lang]['validate'] = 'fail';
				$data[$lang]['message'] = 'No hreflang tags found on target page. <span class="dashicons dashicons-warning"></span> <a target="_blank" href="https://webmasters.googleblog.com/2014/07/troubleshooting-hreflang-annotations-in.html">How to fix</a>';
			}
		}
	}

	// Always return JSON response
	if ( empty( $data ) ) {
		wp_send_json_error( array( 'message' => 'No hreflang tags found to validate' ), 400 );
	}

	wp_send_json( $data );
}
//New added: 4/12/2017 start
function hreflang_tags_pro_html_tag($output) {
	global $post;
	if ($post) {
		$html_lang = get_post_meta($post->ID,'html_lang',true);
		$html_region = get_post_meta($post->ID,'html_region',true);
		if ($html_region != '') {
			$region = "-".$html_region;
		}
		if ($html_lang != '' && $html_region != '') {
			$output = 'lang="'.$html_lang.'-'.$html_region.'"';
		}
		else if ($html_lang != '' && $html_region == '') {
			$output = 'lang="'.str_replace("_","-",$html_lang).'"';
		}
	}
	return $output;
}
add_filter('language_attributes','hreflang_tags_pro_html_tag');
function hreflang_tags_pro_change_og_locale( $locale ) {
	global $post;
	if ($post) {
		$html_lang = get_post_meta($post->ID,'html_lang',true);
		$html_region = get_post_meta($post->ID,'html_region',true);
		if ($html_region != '') {
			$region = "-".$html_region;
		}
		if ($html_lang != '' && $html_region != '') {
			$locale = $html_lang.'_'.$html_region;
		}
		else if ($html_lang != '' && $html_region == '') {
			$locale = $html_lang;
		}
	}
	return $locale;
}
add_filter( 'wpseo_locale', 'hreflang_tags_pro_change_og_locale' );
function hreflang_tags_pro_bulk_editor_screen_options() {
	$option = 'per_page';

	$args = array(
		'label' => 'Number of Posts per Page in Bulk Editor',
		'default' => 20,
		'option' => 'hreflang_tags_pro_bulk_editor_posts_per_page'
	);

	add_screen_option( $option, $args );
}
function hreflang_tags_pro_validator_screen_options() {
	$option = 'per_page';

	$args = array(
		'label' => 'Number of Posts per Page in Validator Tool',
		'default' => 20,
		'option' => 'hreflang_tags_pro_validator_posts_per_page'
	);

	add_screen_option( $option, $args );
}
add_filter('set-screen-option', 'hreflang_tags_pro_set_screen_option', 10, 3);

function hreflang_tags_pro_set_screen_option($status, $option, $value) {

    if ( 'hreflang_tags_pro_bulk_editor_posts_per_page' == $option ) return $value;
    if ( 'hreflang_tags_pro_validator_posts_per_page' == $option ) return $value;

    return $status;

}
//New added: 4/12/2017 end
function hreflang_tags_pro_adminbar_validate_hreflang_tags($post) {
	$data = array();

	if (is_home()) {
		$post = get_post(get_option('page_for_posts'));
		$home_tags = get_option('hreflang_pro_blog_tags');
		for($n = 0; $n < count($home_tags['href']); $n++) {
			if ($home_tags['hrefregion'][$n] != '') {
				$lang = $home_tags['hreflang'][$n].'_'.$home_tags['hrefregion'][$n];
			}
			else {
				$lang = $home_tags['hreflang'][$n];
			}
			$url = $home_tags['href'][$n];
			if (!class_exists('DOMDocument')) {
				$data['result'] = 'fail';
				$data['message'] = 'The required DOMDocument class is not available on this server.';
			}
			else {
				$dom = new DOMDocument();
				libxml_use_internal_errors(true);
				@$dom->loadHTMLFile($url);
				if (strpos($http_response_header[0], '404')) {
					$data[$lang]['result'] = 'success';
					$data[$lang]['validate'] = 'fail';
					$data[$lang]['message'] = 'Missing. Header response 404.';
				}
				else if (strpos($http_response_header[0], '301')) {
					$data[$lang]['result'] = 'success';
					$data[$lang]['validate'] = 'fail';
					$data[$lang]['message'] = 'Missing. Header response 301.';
				}
				else {
					$xpath = new DomXpath($dom);
					$hrefs = $xpath->query("//link[@hreflang]");
					if ($hrefs->length > 0) {
						$urls = array();
						foreach ($hrefs as $href) {
							$urls[] = $href->getAttribute('href');
							$hreflangs[] = str_replace('-', '_', $href->getAttribute('hreflang'));
						}
						if (!in_array(get_permalink($post->ID), $urls)) {
							$data[$lang]['result'] = 'success';
							$data[$lang]['validate'] = 'fail';
							$data[$lang]['message'] = 'No return tags found. <span class="dashicons dashicons-warning"></span> <a target="_blank" href="https://webmasters.googleblog.com/2014/07/troubleshooting-hreflang-annotations-in.html">How to fix</a>';
						}
						else {
							$data[$lang]['result'] = 'success';
							$data[$lang]['validate'] = 'pass';
						}
					}
					else {
						$data[$lang]['result'] = 'success';
						$data[$lang]['validate'] = 'fail';
						$data[$lang]['message'] = 'No hreflang tags found on target page. <span class="dashicons dashicons-warning"></span> <a target="_blank" href="https://webmasters.googleblog.com/2014/07/troubleshooting-hreflang-annotations-in.html">How to fix</a>';
					}
				}
			}
		}
	}
	elseif ( is_category() ) {
		$post_meta = get_term_meta($post->term_id);
		foreach($post_meta as $key=>$value ) {
			if (stristr($key,'hreflang')) {
				$key_array = explode('-',$key);
				$lang = $key_array[1];
				$url = $value[0];
				if (!class_exists('DOMDocument')) {
					$data['result'] = 'fail';
					$data['message'] = 'The required DOMDocument class is not available on this server.';
				}
				else {
					$dom = new DOMDocument();
					libxml_use_internal_errors(true);
					@$dom->loadHTMLFile($url);
					if (strpos($http_response_header[0], '404')) {
						$data[$lang]['result'] = 'success';
						$data[$lang]['validate'] = 'fail';
						$data[$lang]['message'] = 'Missing. Header response 404.';
					}
					else if (strpos($http_response_header[0], '301')) {
						$data[$lang]['result'] = 'success';
						$data[$lang]['validate'] = 'fail';
						$data[$lang]['message'] = 'Missing. Header response 301.';
					}
					else {
						$xpath = new DomXpath($dom);
						$hrefs = $xpath->query("//link[@hreflang]");
						if ($hrefs->length > 0) {
							$urls = array();
							foreach ($hrefs as $href) {
								$urls[] = $href->getAttribute('href');
								$hreflangs[] = str_replace('-', '_', $href->getAttribute('hreflang'));
							}
							if (!in_array(get_term_link($post->term_id), $urls)) {
								$data[$lang]['result'] = 'success';
								$data[$lang]['validate'] = 'fail';
								$data[$lang]['message'] = 'No return tags found. <span class="dashicons dashicons-warning"></span> <a target="_blank" href="https://webmasters.googleblog.com/2014/07/troubleshooting-hreflang-annotations-in.html">How to fix</a>';
							}
							else {
								$data[$lang]['result'] = 'success';
								$data[$lang]['validate'] = 'pass';
							}
						}
						else {
							$data[$lang]['result'] = 'success';
							$data[$lang]['validate'] = 'fail';
							$data[$lang]['message'] = 'No hreflang tags found on target page. <span class="dashicons dashicons-warning"></span> <a target="_blank" href="https://webmasters.googleblog.com/2014/07/troubleshooting-hreflang-annotations-in.html">How to fix</a>';
						}
					}
				}
			}
		}
	}
	else {
		$post_meta = get_post_meta($post->ID);
		foreach($post_meta as $key=>$value ) {
			if (stristr($key,'hreflang')) {
				$key_array = explode('-',$key);
				$lang = $key_array[1];
				$url = $value[0];
				if (!class_exists('DOMDocument')) {
					$data['result'] = 'fail';
					$data['message'] = 'The required DOMDocument class is not available on this server.';
				}
				else {
					$dom = new DOMDocument();
					libxml_use_internal_errors(true);
					@$dom->loadHTMLFile($url);
					if (strpos($http_response_header[0], '404')) {
						$data[$lang]['result'] = 'success';
						$data[$lang]['validate'] = 'fail';
						$data[$lang]['message'] = 'Missing. Header response 404.';
					}
					else if (strpos($http_response_header[0], '301')) {
						$data[$lang]['result'] = 'success';
						$data[$lang]['validate'] = 'fail';
						$data[$lang]['message'] = 'Missing. Header response 301.';
					}
					else {
						$xpath = new DomXpath($dom);
						$hrefs = $xpath->query("//link[@hreflang]");
						if ($hrefs->length > 0) {
							$urls = array();
							foreach ($hrefs as $href) {
								$urls[] = $href->getAttribute('href');
								$hreflangs[] = str_replace('-', '_', $href->getAttribute('hreflang'));
							}
							if (!in_array(get_permalink($post->ID), $urls)) {
								$data[$lang]['result'] = 'success';
								$data[$lang]['validate'] = 'fail';
								$data[$lang]['message'] = 'No return tags found. <span class="dashicons dashicons-warning"></span> <a target="_blank" href="https://webmasters.googleblog.com/2014/07/troubleshooting-hreflang-annotations-in.html">How to fix</a>';
							}
							else {
								$data[$lang]['result'] = 'success';
								$data[$lang]['validate'] = 'pass';
							}
						}
						else {
							$data[$lang]['result'] = 'success';
							$data[$lang]['validate'] = 'fail';
							$data[$lang]['message'] = 'No hreflang tags found on target page. <span class="dashicons dashicons-warning"></span> <a target="_blank" href="https://webmasters.googleblog.com/2014/07/troubleshooting-hreflang-annotations-in.html">How to fix</a>';
						}
					}
				}
			}
		}
	}
	return $data;
}

function hreflang_tags_pro_admin_bar() {
	global $wp_admin_bar, $post;
	$allowed_post_types = get_option('hreflang_post_types');

	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	if ('1' !== get_option('hreflang_pro_show_admin_bar')) {
		return;
	}
	$is_frontend = false;
	$is_category = false;
	$is_backend = false;
	$is_home = false;
	if ( is_singular() && isset($post) && is_object($post) && in_array($post->post_type,$allowed_post_types)) {
		$is_frontend = true;
	}

	if ( is_category() && in_array('categories',$allowed_post_types) ) {
		$is_category = true;
		$post = get_queried_object();
	}

	if ( is_admin() && isset($post) && is_object($post) && in_array($post->post_type,$allowed_post_types)) {
		$is_backend = true;
	}

	if ( is_home() && '1' == get_option('hreflang_pro_allow_blog_tags') ) {
		$is_home = true;
	}

	$class = '';
	if ($is_frontend || $is_category || $is_backend || $is_home) {
		$result = hreflang_tags_pro_adminbar_validate_hreflang_tags($post);
		echo '<script>console.log('.json_encode($result).');</script>';
		if ( is_array($result) && !empty($result) ) {
			foreach($result as $language) {
				if ($language['validate'] == 'fail') {
					$validation = 'class="hreflang-tags-pro adminbar-validation fail" title="'.__('Your HREFLANG Tags found on this post are not valid.','hreflang-tags-pro').'"';
					break;
				}
				else {
					$validation = 'class="hreflang-tags-pro adminbar-validation pass" title="'.__('Your HREFLANG Tags found on this post are valid.','hreflang-tags-pro').'"';
					continue;
				}
			}
		}
		else {
			$validation = 'class="hreflang-tags-pro adminbar-validation none" title="'.__('No HREFLANG Tags found on this post.','hreflang-tags-pro').'"';
		}
		$title = '<div id="hreflang-tags-pro-ab-icon" class="ab-item dashicons-translation">
					  <div '.$validation.'><span class="screen-reader-text">' . __( 'HREFLANG Tags Pro', 'hreflang-tags-pro' ) . '</span>
					  </div>
				  </div>';

		$args = array(
			'id'    => 'hreflang-tags-pro-menu',
			'title' => $title,
			'href'  => admin_url().'admin.php?page=hreflang_pro&tab=hreflang_pro_dashboard',
			'meta'  => array( 'class' => 'hreflang-tags-pro-ab' )
		);
		$wp_admin_bar->add_node( $args );
	}
}
function hreflang_tags_pro_taxonomy_forms() {
	if (is_array(get_option('hreflang_post_types'))) {
		foreach(get_option('hreflang_post_types') as $type) {
			if ( !post_type_exists($type) ) {
				add_action($type.'_add_form_fields','add_hreflang_pro_to_category_form',99);
				add_action($type.'_edit_form_fields','add_hreflang_pro_to_category_edit_form',10,1);
			}
		}
	}
}
function hreflang_tags_pro_version_fix() {
	$new_types = array();
	$post_type_hreflang = get_option('hreflang_post_types');
	if (!empty($post_type_hreflang)) {
			foreach($post_type_hreflang as $type) {
			if ($type == 'categories') {
				$new_types[] = 'category';
				continue;
			}
			$new_types[] = $type;
		}
	}else{
		return "Select Content Types";
	}
	
	update_option('hreflang_post_types',$new_types);
}
function hreflang_tags_pro_get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
//--------- Asterisk Function ------------//
function get_starred($str) {
    $str_length = strlen($str);
    return substr($str, 0, 1).str_repeat('*', $str_length).substr($str, $str_length - 1, 1);
}
//--------- End of Asterisk Function ------------//
function hreflang_tags_pro_parse_license_box() {
	$wp_version = get_bloginfo('version');
	$hreflang_tags_version = HREFLANG_PRO_VERSION;

	echo '<ul>
			<li>'.__('WordPress Version','hreflang-tags-pro').': <span>'.$wp_version.'</span></li>
			<li>'.__('Plugin Version','hreflang-tags-pro').': <span>'.$hreflang_tags_version.'</span></li>
			<li>'.__('License Status','hreflang-tags-pro').': <span><b>'.__('Free Version - No License Required','hreflang-tags-pro').'</b></span></li>
		  </ul>';
}
function hreflang_pro_delete_entry_from_bulk_editor() {
	// Security checks
	check_ajax_referer( 'hreflang_pro_ajax_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
	}

	// Sanitize inputs
	$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => 'Invalid post ID' ), 400 );
	}

	$language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : '';
	$region = isset($_REQUEST['region']) ? sanitize_text_field($_REQUEST['region']) : '';
	$ret = array();
	$hreflang_pro_data = get_post_meta($post_id);
	if (is_array($hreflang_pro_data)) {
		foreach($hreflang_pro_data as $key=>$value) {
			if ($region != '') {
				delete_post_meta($post_id, 'hreflang-'.$language.'_'.$region);
				$ret['post_id'] = $post_id;
				$ret['language'] = $language;
				$ret['region'] = $region;
				$ret["result"] = "success";
				header('Content-Type: application/json');
				echo json_encode($ret);
				exit;
			}
			else {
				delete_post_meta($post_id, 'hreflang-'.$language);
				$ret['post_id'] = $post_id;
				$ret['language'] = $language;
				$ret['region'] = $region;
				$ret["result"] = "success";
				header('Content-Type: application/json');
				echo json_encode($ret);
				exit;
			}
		}
	}
}
function hreflang_pro_delete_html_entry_from_bulk_editor() {
	// Security checks
	check_ajax_referer( 'hreflang_pro_ajax_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
	}

	// Sanitize inputs
	$post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => 'Invalid post ID' ), 400 );
	}

	$language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : '';
	$region = isset($_POST['region']) ? sanitize_text_field($_POST['region']) : '';
	$ret = array();
	$hreflang_pro_data = get_post_meta($post_id);
	if (is_array($hreflang_pro_data)) {
		delete_post_meta($post_id, 'html_lang', $language);
		delete_post_meta($post_id, 'html_region', $region);
		$ret['post_id'] = $post_id;
		$ret["result"] = "success";
		header('Content-Type: application/json');
		echo json_encode($ret);
		exit;
	}
}
function hreflang_pro_bulk_get_all_pages() {
	// Security checks
	check_ajax_referer( 'hreflang_pro_ajax_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
	}

	$args = array(
    'post_type'    => get_option('hreflang_post_types'),
    'orderby'      => 'menu_order',
		'posts_per_page' => -1
	);
	$pages = new WP_Query ( $args );
	$posts = $pages->posts;
	$items = array();
	foreach ($posts as $post) {
		$items[] = array('permalink' => get_permalink($post->ID), 'post_title' => $post->post_title, 'post_type' => $post->post_type);
	}
	$ret["items"] = $items;
	header('Content-Type: application/json');
	echo json_encode($ret);
	exit;
}
if ( ! function_exists( 'hreflang_pro_is_woocommerce_activated' ) ) {
	function hreflang_pro_is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { 
			return true; 
		} 
		else { 
			return false; 
		}
	}
}

function hreflang_pro_get_sitemap_url() {
	if (get_option('hreflang_pro_xml_sitemap_filename') == "") {
		if (file_exists(ABSPATH . "hreflang-tags-pro-sitemap.xml")) {
			$url = site_url().'/hreflang-tags-pro-sitemap.xml';
			_e("<a href='$url' target='_blank'>$url</a> [Opens in a new tab]");			
		}
		else {
			_e('XML Sitemap not found. Use the Generate button above to build your XML sitemap based on your saved settings.', 'hreflang-tags-pro');			
		}		
	}
	else {
		$filename = get_option('hreflang_pro_xml_sitemap_filename');
		if (!strpos($filename, ".xml")) {
			$filename = $filename . ".xml";
		}
		if (file_exists(ABSPATH . $filename)) {
			$url = site_url().'/'.$filename;
			_e("<a href='$url' target='_blank'>$url</a> [Opens in a new tab]");			
		}
		else {
			_e('XML Sitemap not found. Use the Generate button above to build your XML sitemap based on your saved settings.', 'hreflang-tags-pro');			
		}		
	}	
}

function hreflang_pro_build_sitemap() {
	$args = array(
        'posts_per_page' => -1,
		'post_type'   => array_values(get_option('hreflang_post_types')),
		'meta_query' => array(
			array(
				'compare_key' => 'LIKE',
				'key'         => 'hreflang'
			)
		)
	);
	$postsForSitemap = new WP_Query( $args );
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";    
    foreach( $postsForSitemap->posts as $post ) {
        $sitemap .= "\t" . '<url>' . "\n";
		$sitemap .= "\t\t" . '<loc>' . get_permalink( $post->ID ) . '</loc>'."\n";
		if (is_category() || is_tax() || is_tag()) {
			$terms = get_queried_object();
			$hreflang_pro_data = get_term_meta($terms->term_id);
			$sortable_hreflang_pro_data = array();
			if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
				foreach($hreflang_pro_data as $key => $value) {
					if (stristr($key, 'hreflang')) {
						$sortable_hreflang_pro_data[$key] = $value;
					}
				}
			}
			//XHTML LINK START
			krsort($sortable_hreflang_pro_data);
			if (is_array($sortable_hreflang_pro_data) && !empty($sortable_hreflang_pro_data)) {
				foreach($sortable_hreflang_pro_data as $key=>$value) {
					$key_array = explode('-',$key);
					if (count($key_array) == 3) {
						$lang = 'x-default';
					}
					else {
						$lang = $key_array[1];
					}
					if ($lang == 'Select one') {
						continue;
					}
					$sitemap .= "\t\t".'<xhtml:link rel="alternate" href="'.$value[0].'" hreflang="'.str_replace('_','-', $lang).'" />'."\n";
				}
			}
			//XHTML LINK END
		} 
		else {
			if ( hreflang_pro_is_woocommerce_activated() ) {
				if (!is_home() && !is_author() && !is_tag() && !is_shop()) {
					$hreflang_pro_data = get_post_meta($post->ID);
					$sortable_hreflang_pro_data = array();
					if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
						foreach($hreflang_pro_data as $key => $value) {
							if (stristr($key, 'hreflang')) {
								$sortable_hreflang_pro_data[$key] = $value;
							}
						}
					}
					//XHTML LINK START
					krsort($sortable_hreflang_pro_data);
					if (is_array($sortable_hreflang_pro_data) && !empty($sortable_hreflang_pro_data)) {
						foreach($sortable_hreflang_pro_data as $key=>$value) {
							$key_array = explode('-',$key);
							if (count($key_array) == 3) {
								$lang = 'x-default';
							}
							else {
								$lang = $key_array[1];
							}
							if ($lang == 'Select one') {
								continue;
							}
							$sitemap .= "\t\t".'<xhtml:link rel="alternate" href="'.$value[0].'" hreflang="'.str_replace('_','-', $lang).'" />'."\n";
						}
					}
					//XHTML LINK END
				}
				else {
					if ('1' == get_option('hreflang_pro_allow_blog_tags') && !is_author() && !is_shop()) {
						$hreflang_pro_data = get_option('hreflang_pro_blog_tags');
						$sortable_hreflang_pro_data = array();
						if (is_array($hreflang_pro_data['href']) && !empty($hreflang_pro_data['href'])) {
							for ($n = 0; $n < count($hreflang_pro_data['href']); $n++) {
								if ($hreflang_pro_data['hrefregion'][$n] != '') {
									$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n].'_'.$hreflang_pro_data['hrefregion'][$n]] = array($hreflang_pro_data['href'][$n]);
								}
								else {
									$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n]] = array($hreflang_pro_data['href'][$n]);
								}
							}
						}
						//XHTML LINK START
						krsort($sortable_hreflang_pro_data);
						if (is_array($sortable_hreflang_pro_data) && !empty($sortable_hreflang_pro_data)) {
							foreach($sortable_hreflang_pro_data as $key=>$value) {
								$key_array = explode('-',$key);
								if (count($key_array) == 3) {
									$lang = 'x-default';
								}
								else {
									$lang = $key_array[1];
								}
								if ($lang == 'Select one') {
									continue;
								}
								$sitemap .= "\t\t".'<xhtml:link rel="alternate" href="'.$value[0].'" hreflang="'.str_replace('_','-', $lang).'" />'."\n";
							}
						}
						//XHTML LINK END
					}
					if ( hreflang_pro_is_woocommerce_activated() ) {
						if ('1' == get_option('hreflang_pro_allow_shop_tags') && is_shop()) {
							$hreflang_pro_data = get_option('hreflang_pro_shop_tags');
							$sortable_hreflang_pro_data = array();
							if (is_array($hreflang_pro_data['href']) && !empty($hreflang_pro_data['href'])) {
								for ($n = 0; $n < count($hreflang_pro_data['href']); $n++) {
									if ($hreflang_pro_data['hrefregion'][$n] != '') {
										$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n].'_'.$hreflang_pro_data['hrefregion'][$n]] = array($hreflang_pro_data['href'][$n]);
									}
									else {
										$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n]] = array($hreflang_pro_data['href'][$n]);
									}
								}
							}
							//XHTML LINK START
							krsort($sortable_hreflang_pro_data);
							if (is_array($sortable_hreflang_pro_data) && !empty($sortable_hreflang_pro_data)) {
								foreach($sortable_hreflang_pro_data as $key=>$value) {
									$key_array = explode('-',$key);
									if (count($key_array) == 3) {
										$lang = 'x-default';
									}
									else {
										$lang = $key_array[1];
									}
									if ($lang == 'Select one') {
										continue;
									}
									$sitemap .= "\t\t".'<xhtml:link rel="alternate" href="'.$value[0].'" hreflang="'.str_replace('_','-', $lang).'" />'."\n";
								}
							}
							//XHTML LINK END
						}
					}
				}
			}
			else {
				if (!is_home() && !is_author() && !is_tag()) {
					if ($post) {
						$hreflang_pro_data = get_post_meta($post->ID);
						$sortable_hreflang_pro_data = array();
						if (is_array($hreflang_pro_data) && !empty($hreflang_pro_data)) {
							foreach($hreflang_pro_data as $key => $value) {
								if (stristr($key, 'hreflang')) {
									$sortable_hreflang_pro_data[$key] = $value;
								}
							}
						}
						//XHTML LINK START
						krsort($sortable_hreflang_pro_data);
						if (is_array($sortable_hreflang_pro_data) && !empty($sortable_hreflang_pro_data)) {
							foreach($sortable_hreflang_pro_data as $key=>$value) {
								$key_array = explode('-',$key);
								if (count($key_array) == 3) {
									$lang = 'x-default';
								}
								else {
									$lang = $key_array[1];
								}
								if ($lang == 'Select one') {
									continue;
								}
								$sitemap .= "\t\t".'<xhtml:link rel="alternate" href="'.$value[0].'" hreflang="'.str_replace('_','-', $lang).'" />'."\n";
							}
						}
						//XHTML LINK END
					}
				}
				else {
					if ('1' == get_option('hreflang_pro_allow_blog_tags') && !is_author()) {
						$hreflang_pro_data = get_option('hreflang_pro_blog_tags');
						$sortable_hreflang_pro_data = array();
						if (is_array($hreflang_pro_data['href']) && !empty($hreflang_pro_data['href'])) {
							for ($n = 0; $n < count($hreflang_pro_data['href']); $n++) {
								if ($hreflang_pro_data['hrefregion'][$n] != '') {
									$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n].'_'.$hreflang_pro_data['hrefregion'][$n]] = array($hreflang_pro_data['href'][$n]);
								}
								else {
									$sortable_hreflang_pro_data['hreflang-'.$hreflang_pro_data['hreflang'][$n]] = array($hreflang_pro_data['href'][$n]);
								}
							}
						}
						//XHTML LINK START
						krsort($sortable_hreflang_pro_data);
						if (is_array($sortable_hreflang_pro_data) && !empty($sortable_hreflang_pro_data)) {
							foreach($sortable_hreflang_pro_data as $key=>$value) {
								$key_array = explode('-',$key);
								if (count($key_array) == 3) {
									$lang = 'x-default';
								}
								else {
									$lang = $key_array[1];
								}
								if ($lang == 'Select one') {
									continue;
								}
								$sitemap .= "\t\t".'<xhtml:link rel="alternate" href="'.$value[0].'" hreflang="'.str_replace('_','-', $lang).'" />'."\n";
							}
						}
						//XHTML LINK END
					}
				}
			}
		}
        $sitemap .= "\t" . '</url>' . "\n";
    }     
    $sitemap .= '</urlset>';
	if (get_option('hreflang_pro_xml_sitemap_filename') == "") {
		$fp = fopen( ABSPATH . "hreflang-tags-pro-sitemap.xml", 'w' );
	}
	else {
		// Sanitize filename to prevent path traversal
		$filename = sanitize_file_name( get_option('hreflang_pro_xml_sitemap_filename') );
		if (!strpos($filename, ".xml")) {
			$filename = $filename . ".xml";
		}
		// Use basename to ensure no directory traversal
		$filename = basename( $filename );
		$fp = fopen( ABSPATH . $filename, 'w' );
	}
    fwrite( $fp, $sitemap );
    fclose( $fp );
}

function hreflang_pro_generate_sitemap_button() {
	if (get_option('hreflang_pro_enable_xml_sitemap') == "1") {
		if (get_option('hreflang_pro_xml_sitemap_filename') == "") {
			if (file_exists(ABSPATH . "hreflang-tags-pro-sitemap.xml")) {
				echo "<button name='trigger_sitemap' data-action='update'>";
				_e('Update Sitemap', 'hreflang-tags-pro');
				echo "</button>";
			}
			else {
				echo "<button name='trigger_sitemap' data-action='generate'>";
				_e('Generate Sitemap', 'hreflang-tags-pro');
				echo "</button>";
			}		
		}
		else {
			$filename = get_option('hreflang_pro_xml_sitemap_filename');
			if (!strpos($filename, ".xml")) {
				$filename = $filename . ".xml";
			}
			if (file_exists(ABSPATH . $filename)) {
				echo "<button name='trigger_sitemap' data-action='update'>";
				_e('Update Sitemap', 'hreflang-tags-pro');
				echo "</button>";
			}
			else {
				echo "<button name='trigger_sitemap' data-action='generate'>";
				_e('Generate Sitemap', 'hreflang-tags-pro');
				echo "</button>";
			}		
		}			
	}
	else {
		if (get_option('hreflang_pro_xml_sitemap_filename') == "") {
			if (file_exists(ABSPATH . "hreflang-tags-pro-sitemap.xml")) {
				echo "<button name='trigger_sitemap' data-action='delete'>";
				_e('Delete Sitemap', 'hreflang-tags-pro');
				echo "</button>";
			}
		}
		else {
			$filename = get_option('hreflang_pro_xml_sitemap_filename');
			if (!strpos($filename, ".xml")) {
				$filename = $filename . ".xml";
			}
			if (file_exists(ABSPATH . $filename)) {
				echo "<button name='trigger_sitemap' data-action='delete'>";
				_e('Delete Sitemap', 'hreflang-tags-pro');
				echo "</button>";
			}
		}
	}
}
function hreflang_pro_do_sitemap() {
	// Security checks
	check_ajax_referer( 'hreflang_pro_ajax_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
	}

	$data = array();
	$method = isset($_POST['method']) ? sanitize_text_field($_POST['method']) : '';
	switch($method) {
		case "update":
			hreflang_pro_build_sitemap();
			$data['result'] = "Your XML Sitemap was successfully updated";
			break;
		case "generate":
			hreflang_pro_build_sitemap();
			$data['result'] = "Your XML Sitemap was successfully generated";
			break;
		case "delete":
		if (get_option('hreflang_pro_xml_sitemap_filename') == "") {
			if (file_exists(ABSPATH . "hreflang-tags-pro-sitemap.xml")) {
				if (!unlink(ABSPATH .  "hreflang-tags-pro-sitemap.xml")) {
					$data['result'] = "This file could not be deleted.";
				}
				else {
					$data['result'] = "This file was successfully deleted.";
				}
			}
		}
		else {
			// Sanitize filename to prevent path traversal
			$filename = sanitize_file_name( get_option('hreflang_pro_xml_sitemap_filename') );
			if (!strpos($filename, ".xml")) {
				$filename = $filename . ".xml";
			}
			// Use basename to ensure no directory traversal
			$filename = basename( $filename );
			if (file_exists(ABSPATH . $filename)) {
				if (!unlink(ABSPATH .  $filename)) {
					$data['result'] = "This file could not be deleted.";
				}
				else {
					$data['result'] = "This file was successfully deleted.";
				}
			}
		}
		break;
		default:
			$data['result'] = "Something went wrong.";
			break;

	}
	header( "Content-Type: application/json" );
	echo json_encode($data);
	die();
}