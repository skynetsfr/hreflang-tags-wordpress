<div id="blog" class="hreflangtab metabox-holder">
  <h3><span>
    <?php _e('WooCommerce Settings', 'hreflang-tags-pro'); ?>
    </span></h3>
  <p>
    <?php _e('These is where you can set your hreflang tags for your WooCommerce main shop page. This is generally the landing page for your WooCommerce powered shop.','hreflang-tags-pro'); ?>
  </p>
  <p>
    <?php _e('Here is the location of your shop page:','hreflang-tags-pro'); ?>
  </p>
  <p><?php echo '<a href="'.get_permalink( wc_get_page_id( 'shop' ) ).'" target="_blank">'.get_the_title( wc_get_page_id( 'shop' ) ).'</a>'; ?></p>
  <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post" id="hreflang-conf" enctype="multipart/form-data" accept-charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
    <?php settings_fields('hreflang-shop-settings-group'); ?>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"> <label for="hreflang_pro_allow_shop_tags">
              <?php _e('Allow Shop Page Tags', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><label>
              <input type="checkbox" name="hreflang_pro_allow_shop_tags" id="hreflang_pro_allow_shop_tags" value="1" <?php if (get_option('hreflang_pro_allow_shop_tags') == '1') { echo 'checked="checked"'; } ?>/>
              <?php _e('Enable hreflang tags on shop page.','hreflang-tags-pro'); ?>
            </label></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="hreflang_pro_shop_tags">
              <?php _e('Shop HREFLANG Tags', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><?php hreflang_pro_shop_tags(); ?></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
  </form>
</div>
