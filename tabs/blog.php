<div id="blog" class="hreflangtab metabox-holder">
  <h3><span>
    <?php _e('Blog Settings', 'hreflang-tags-pro'); ?>
    </span></h3>
  <?php $post = get_post(get_option('page_for_posts')); ?>
  <p>
    <?php _e('These is where you can set your hreflang tags for your blog page. This is the page where your posts are show in a list. For many, it is the homepage. For those with a static frontpage, it might be somewhere else.','hreflang-tags-pro'); ?>
  </p>
  <p>
    <?php _e('Here is the location of your blog page:','hreflang-tags-pro'); ?>
  </p>
<?php if (is_object($post) && !empty($post)) {
  ?>
  <p><?php echo '<a href="'.get_permalink($post).'" target="_blank">'.$post->post_title.'</a>'; ?></p>
  <?php
}
else {
  ?>
  <p><strong><?php _e('You do not have a blog page set.','hreflang-tags-pro'); ?></strong></p>
  <?php
}
?>
  <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post" id="hreflang-conf" enctype="multipart/form-data" accept-charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
    <?php settings_fields('hreflang-blog-settings-group'); ?>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"> <label for="hreflang_pro_allow_blog_tags">
              <?php _e('Allow Blog Page Tags', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><label>
              <input type="checkbox" name="hreflang_pro_allow_blog_tags" id="hreflang_pro_allow_blog_tags" value="1" <?php if (get_option('hreflang_pro_allow_blog_tags') == '1') { echo 'checked="checked"'; } ?>/>
              <?php _e('Enable hreflang tags on blog page.','hreflang-tags-pro'); ?>
            </label></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="hreflang_pro_blog_tags">
              <?php _e('Blog HREFLANG Tags', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><?php hreflang_pro_blog_tags(); ?></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
  </form>
</div>
