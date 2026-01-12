<div id="home" class="hreflangtab metabox-holder">
  <h3><span>
    <?php _e('Home Settings', 'hreflang-tags-pro'); ?>
    </span></h3>
  <?php $post = get_post(get_option('page_on_front')); ?>
  <p>
    <?php _e('This is where you can set your hreflang tags for your homepage. This is your static frontpage.','hreflang-tags-pro'); ?>
  </p>
  <p>
    <?php _e('Here is the location of your homepage:','hreflang-tags-pro'); ?>
  </p>
<?php if (is_object($post) && !empty($post)) {
  ?>
  <p><?php echo '<a href="'.get_permalink($post).'" target="_blank">'.$post->post_title.'</a>'; ?></p>
  <?php
}
else {
  ?>
  <p><strong><?php _e('You do not have a static homepage set.','hreflang-tags-pro'); ?></strong></p>
  <?php
}
?>
  <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post" id="hreflang-conf" enctype="multipart/form-data" accept-charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
    <?php settings_fields('hreflang-home-settings-group'); ?>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"> <label for="hreflang_pro_allow_home_tags">
              <?php _e('Allow Home Page Tags', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><label>
              <input type="checkbox" name="hreflang_pro_allow_home_tags" id="hreflang_pro_allow_home_tags" value="1" <?php if (get_option('hreflang_pro_allow_home_tags') == '1') { echo 'checked="checked"'; } ?>/>
              <?php _e('Enable hreflang tags on homepage.','hreflang-tags-pro'); ?>
            </label></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="hreflang_pro_home_tags">
              <?php _e('Home HREFLANG Tags', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><?php hreflang_pro_home_tags(); ?></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
  </form>
</div>
