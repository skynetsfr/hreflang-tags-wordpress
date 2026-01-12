<div id="blog" class="hreflangtab metabox-holder">
  <h3><span>
    <?php _e('XML Sitemap Settings', 'hreflang-tags-pro'); ?>
    </span></h3>
  <p>
    <?php _e('You can also create an XML Sitemap that will gather the data from your saved HREFLANG Tags and display them in an XML Sitemap.','hreflang-tags-pro'); ?>
  </p>
  <form action="<?php echo esc_url(admin_url('options.php')); ?>" method="post" id="hreflang-conf" enctype="multipart/form-data" accept-charset="<?php echo esc_attr(get_bloginfo('charset')); ?>">
    <?php settings_fields('hreflang-xml-sitemap-settings-group'); ?>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row"> <label for="hreflang_pro_enable_xml_sitemap">
              <?php _e('XML Sitemap', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><label>
              <input type="checkbox" name="hreflang_pro_enable_xml_sitemap" id="hreflang_pro_enable_xml_sitemap" value="1" <?php if (get_option('hreflang_pro_enable_xml_sitemap') == '1') { echo 'checked="checked"'; } ?>/>
              <?php _e('Enable XML Sitemap','hreflang-tags-pro'); ?>
            </label></td>
        </tr>
        <tr valign="top">
          <th scope="row"> <?php hreflang_pro_generate_sitemap_button(); ?> </th>
        </tr>
        <tr valign="top">
          <th scope="row"> <label for="xml_sitemap_location">
              <?php _e('XML Sitemap Location', 'hreflang-tags-pro'); ?>
              : </label>
          </th>
		  <td><label><?php hreflang_pro_get_sitemap_url(); ?></label></td>
        </tr>
        <tr valign="top">
          <th scope="row"> <label for="hreflang_pro_disable_tags_head">
              <?php _e('Disable HEAD Tags', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><label>
              <input type="checkbox" name="hreflang_pro_disable_tags_head" id="hreflang_pro_disable_tags_head" value="1" <?php if (get_option('hreflang_pro_disable_tags_head') == '1') { echo 'checked="checked"'; } ?>/>
              <?php _e('Disable HREFLANG Tags in page &lt;head&gt; section.','hreflang-tags-pro'); ?>
            </label></td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="hreflang_pro_xml_sitemap_filename">
              <?php _e('XML Sitemap Filename', 'hreflang-tags-pro'); ?>
            </label>
          </th>
          <td><label>
              <input type="text" name="hreflang_pro_xml_sitemap_filename" id="hreflang_pro_xml_sitemap_filename" size="50" value="<?php 
			  if (get_option('hreflang_pro_xml_sitemap_filename') != '') { 
				  echo get_option('hreflang_pro_xml_sitemap_filename');
			  }
			  else {
				  echo "hreflang-tags-pro-sitemap";																														
			  }?>" placeholder="Custom Filename"/>
              <br>
              <?php _e('Your XML Sitemap custom filename. If empty, the default filename will be hreflang-tags-pro-sitemap.xml','hreflang-tags-pro'); ?>
            </label></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
    </p>
  </form>
</div>
