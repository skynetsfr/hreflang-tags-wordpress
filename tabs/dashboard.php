  <div id="dashboard" class="hreflangtab metabox-holder">
  <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post" id="hreflang-conf" enctype="multipart/form-data" accept-charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
  <?php settings_fields('hreflang-settings-group'); ?>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"> <label for="hreflang_post_types">
              <?php _e('Content Types', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td>
        <?php
		$post_types = get_post_types( array( 'public' => true ),'objects');
		if ( is_array( $post_types ) && $post_types !== array() ) {
			foreach ( $post_types as $post_type ) {
				echo '<input type="checkbox" name="hreflang_post_types[]" id="hreflang_post_types_'.$post_type->name.'" value="'.$post_type->name.'"';
				if (is_array(get_option('hreflang_post_types'))) { 
					if (in_array($post_type->name, get_option('hreflang_post_types'))) {
						echo ' checked="checked"';
					}
				} 
				echo '/>
				<label for="hreflang_post_types_'.$post_type->name.'">'.$post_type->label.'</label>
				<br>';
			}
		}
		$taxonomies = get_taxonomies( array( 'public' => true),'objects');
		if (is_array($taxonomies) && $taxonomies !== array()) {
			foreach ($taxonomies as $taxonomy) {
				echo '<input type="checkbox" name="hreflang_post_types[]" id="hreflang_post_types_'.$taxonomy->name.'" value="'.$taxonomy->name.'"';
				if (is_array(get_option('hreflang_post_types'))) { 
					if (in_array($taxonomy->name, get_option('hreflang_post_types'))) {
						echo ' checked="checked"';
					}
				} 
				echo '/>
				<label for="hreflang_post_types_'.$taxonomy->name.'">'.$taxonomy->label.'</label>
				<br>';
			}
		}
		?>
            <br>
            <span class="hreflang_pro_settings_description">
            <?php _e('These are the types of content that you want to set HREFLANG Tags metaboxes for.','hreflang-tags-pro'); ?>
            </span></td>
        </tr>
      </tbody>
    </table>
  </div>
  <p class="submit">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
  </p>
  </form>