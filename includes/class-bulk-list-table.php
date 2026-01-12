<?php
/**
 * @package Hreflang Tags WP\Admin\Bulk Editor
 * @since      1.2.0
 */

/**
 * Implements table for bulk editing.
 */

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class HREFLANG_PRO_Bulk_List_Table extends WP_List_Table {

	/**
	 * The nonce that was passed with the request
	 *
	 * @var string
	 */
	private $nonce;

	/**
	 * Array of post types for which the current user has `edit_others_posts` capabilities.
	 *
	 * @var array
	 */
	private $all_posts;

	/**
	 * Array of post types for which the current user has `edit_posts` capabilities, but not `edit_others_posts`.
	 *
	 * @var array
	 */
	private $own_posts;

	/**
	 * Saves all the metadata into this array.
	 *
	 * @var array
	 */
	protected $meta_data = array();

	/**
	 * The current requested page_url
	 *
	 * @var string
	 */
	private $request_url;

	/**
	 * The current page (depending on $_GET['paged']) if current tab is for current page_type, else it will be 1
	 *
	 * @var integer
	 */
	private $current_page;

	/**
	 * The current post filter, if is used (depending on $_GET['post_type_filter'])
	 *
	 * @var string
	 */
	private $current_filter;
	private $current_slug_filter;
	private $current_allowed_wildcard;
	//private $current_lang;

	/**
	 * The current post status, if is used (depending on $_GET['post_status'])
	 *
	 * @var string
	 */
	private $current_status;

	/**
	 * The current sorting, if used (depending on $_GET['order'] and $_GET['orderby'])
	 *
	 * @var string
	 */
	private $current_order;

	/**
	 * The page_type for current class instance (for example: title / description).
	 *
	 * @var string
	 */
	protected $page_type;

	/**
	 * Based on the page_type ($this->page_type) there will be constructed an url part, for subpages and
	 * navigation
	 *
	 * @var string
	 */
	protected $page_url;
	
	/**
	 * The settings which will be used in the __construct.
	 *
	 * @var array
	 */
	protected $settings = array(
		'singular' => 'hreflang_pro_bulk',
		'plural'   => 'hreflang_pro_bulk',
		'ajax'     => true,
	);

	/**
	 * @var array
	 */
	protected $pagination = array();

	/**
	 * Class constructor
	 */
	function __construct() {
		parent::__construct( $this->settings );

		$this->request_url    = $_SERVER['REQUEST_URI'];
		$this->current_page   = ( ! empty( $_GET['paged'] ) ) ? $_GET['paged'] : 1;
		$this->current_filter = ( ! empty( $_GET['post_type_filter'] ) ) ? $_GET['post_type_filter'] : 1;
		$this->current_slug_filter = ( ! empty( $_GET['slug_filter'] ) ) ? $_GET['slug_filter'] : "";
		$this->current_allowed_wildcard = ( ! empty( $_GET['allowed_wildcard'] ) ) ? $_GET['allowed_wildcard'] : "no";
		$this->current_status = ( ! empty( $_GET['post_status'] ) ) ? $_GET['post_status'] : 1;
		$this->current_order  = array(
			'order'   => ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'asc',
			'orderby' => ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'post_title',
		);
    	//$this->current_lang = ( ! empty( $_GET['lang'] ) ) ? $_GET['lang'] : 'all';

		$this->verify_nonce();

		$this->nonce    = wp_create_nonce( 'bulk-editor-table' );
		$this->page_url = "&nonce={$this->nonce}&type={$this->page_type}#top#{$this->page_type}";

		$this->populate_editable_post_types();

	}

	/**
	 * Verifies nonce if additional parameters have been sent.
	 *
	 * Shows an error notification if the nonce check fails.
	 */
	private function verify_nonce() {
		if ( $this->should_verify_nonce() && ! wp_verify_nonce( filter_input( INPUT_GET, 'nonce' ), 'bulk-editor-table' ) ) {
					__( 'You are not allowed to access this page.', 'hreflang-tags-pro' );
			die;
		}
	}

	/**
	 * Checks if additional parameters have been sent to determine if nonce should be checked or not.
	 *
	 * @return bool
	 */
	private function should_verify_nonce() {
		$possible_params = array(
			'type',
			'paged',
			'post_type_filter',
			'slug_filter',
			'allowed_wildcard',
			'post_status',
			'order',
			'orderby',
		);

		foreach ( $possible_params as $param_name ) {
			if ( filter_input( INPUT_GET, $param_name ) ) {
				return true;
			}
		}
	}

	/**
	 * Prepares the data and renders the page.
	 */
	public function show_page() {
		$this->prepare_page_navigation();
		$this->prepare_items();

		$this->show_master_template_area();
		$this->views();
		$this->display();
	}

	public function show_master_template_area() {
		global $allregions,$langcode2Name;
		?>
    <style type="text/css">
    #hreflang-bulk-master-template-1 .remove-new-hreflang-tag {
      display: none;
    }
    .hreflang-pro-html-lang {
      display: none;
    }
    </style>
			  <div class="href-content-wrapper" id="master-template">
              <h2><span><?php _e('Master Template','hreflang-tags-pro'); ?></span></h2>
              <p><?php _e('This is a very powerful tool which should only be used if you are certain of what you want to do. This can and will completely overwrrite your previous hreflang tags for these posts. Use extreme care when appyling and saving.','hreflang-tags-pro'); ?></p>
              <p><?php _e('You may use the template tag <code>{get_permalink}</code> for the Alternative URL to set a default for each page/post.','hreflang-tags-pro'); ?></p>
              <p><?php _e('You may also use the following template tags <code>{scheme},{host},{slug}</code> for the Alternative URL to set a default for each page/post. I.e. If you enter in the master template box the following: <br><code>{scheme}://{host}/es{slug}</code> then <code>http://www.example.com/cars</code> becomes <code>http://www.example.com/es/cars</code>. These options cannot be used with <code>{get_permalink}</code>, rather the template tag used refers to the URL permalink\'s component.<br><br><p>Notes:</p><ul><li><code>{slug}</code> will always contain the beginning and trailing slashes.</li><li><code>{host}</code> Never contains a trailing slash, but always contains the current domain name and top-level domain, like .com, .org, .es, etc. If you want to reference a different domain or tld, you must manually enter it. I.e. If your default site is <code>http://example.com/cars</code> and your Spanish site is <code>http://ejemplo.es/cars</code>, then you would enter: <code>{scheme}://ejemplo.es{slug}</code></li><li><code>{scheme}</code> If you do not add a scheme, we will add <code>http</code> as the default value.</li></ul><br><br><strong>CAUTION: Please use these tags carefully, as they will replace your existing alternative URLs. You can set the Screen Options to 1 in order to test your settings without changing multiple posts/pages.</strong>','hreflang-tags-pro'); ?></p>
                  <button class="button apply_to_all_page"><?php _e('Apply to All','hreflang-tags-pro'); ?></button>
                  <button class="button save_all_page"><?php _e('Save All','hreflang-tags-pro'); ?></button>
                  <button class="button delete_all_page"><?php _e('Delete All','hreflang-tags-pro'); ?></button>
                  <hr/>
                  <div class="html-content-wrapper" id="html-master-template">
                    <p><span><input type="checkbox" name="html-option-selection"/></span><span><?php _e('I would like to add the HTML attribute to each page in this list.','hreflang-tags-pro'); ?></span></p>
                    <div class="html-master hreflang-pro-html-lang">
                  	<label for="meta-box-dropdown"><?php _e('HTML Tag Language','hreflang-tags-pro'); ?> </label>
                  	<select name="html-lang" id="html-lang-master">
                  	<option value=""><?php _e('Select one','hreflang-tags-by-dcgws'); ?></option>
                  	<?php foreach ($langcode2Name as $language) {
                  		echo '<option value="'.$language['code'].'"';
						if (isset($html_lang)) {
							if ($language['code'] == $html_lang) {
								 echo ' selected="selected"';
							}							
						}
                  		echo '>'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
                  	}
                    ?>
                  	</select>
                  	<label for="meta-box-dropdown"><?php _e('Region','hreflang-tags-pro'); ?> </label>
                  	<select name="html-region" id="html-region-master">';
                  	<option value><?php _e('No region/default','hreflang-tags-by-dcgws'); ?></option>
                  	<?php foreach ($allregions as $region) {
                  		echo '<option value="'.$region->alpha2Code.'"';
						if (isset($html_region)) {
							if ($region->alpha2Code == $html_region) {
								 echo ' selected="selected"';
							}							
						}
                  		echo '>'.$region->name.'</option>';
                  	} ?>
                  	</select>
                  	</div>
                  </div>
                  <div id="hreflang-bulk-master-template-1" class="master href-lang">
                    <label for="hreflang-href"><?php _e('Alternative URL','hreflang-tags-pro'); ?></label>
                    <input name="hreflang-href[]" class="hreflang-href" type="text" value="">
                    <label for="meta-box-dropdown"><?php _e('Language','hreflang-tags-pro'); ?></label>
              	    <select name="hreflang-lang[]" id="hreflang-lang">
                   	<option><?php _e('Select one','hreflang-tags-by-dcgws'); ?></option>
              			<?php	foreach ($langcode2Name as $language) {
              					echo '<option value="'.$language['code'].'">'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
              			} ?>
                  	</select>
              			<label for="meta-box-dropdown"><?php _e('Region','hreflang-tags-pro'); ?></label>
              	    <select name="hreflang-region[]" id="hreflang-region">
              			<option value><?php _e('No region/default','hreflang-tags-by-dcgws'); ?></option>
              			<?php	foreach ($allregions as $region) {
              					echo '<option value="'.$region->alpha2Code.'">'.$region->name.'</option>';
              			} ?>
                  	</select>
                    <button class="add-new-hreflang-tag" data-id="master-template"><span class="dashicons dashicons-plus"></span></button>
                    <button class="remove-new-hreflang-tag" data-id="master-template"><span class="dashicons dashicons-minus"></span></button>
                  </div>
                </div>

	<?php
	}
	/**
	 * Display the list of views available on this table.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function views() {
		$views = $this->get_views();
		/**
		 * Filters the list of available list table views.
		 *
		 * The dynamic portion of the hook name, `$this->screen->id`, refers
		 * to the ID of the current screen, usually a string.
		 *
		 * @since 3.5.0
		 *
		 * @param array $views An array of available list table views.
		 */
		$views = apply_filters( "views_{$this->screen->id}", $views );

		if ( empty( $views ) )
			return;

		$this->screen->render_screen_reader_content( 'heading_views' );

		echo "<ul class='subsubsub'>\n";
		foreach ( $views as $class => $view ) {
			$views[ $class ] = "\t<li class='$class'>$view";
		}
		echo implode( " |</li>\n", $views ) . "</li>\n";
		echo "</ul>";
	}


	/**
	 * Used in the constructor to build a reference list of post types the current user can edit.
	 */
	protected function populate_editable_post_types() {
		$post_types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'object' );

		$this->all_posts = array();
		$this->own_posts = array();

		if ( is_array( $post_types ) && $post_types !== array() ) {
			foreach ( $post_types as $post_type ) {
				if ( ! current_user_can( $post_type->cap->edit_posts ) ) {
					continue;
				}

				if ( current_user_can( $post_type->cap->edit_others_posts ) ) {
					$this->all_posts[] = esc_sql( $post_type->name );
				}
				else {
					$this->own_posts[] = esc_sql( $post_type->name );
				}
			}
		}
	}


	/**
	 * Will shown the navigation for the table like pagenavigation and pagefilter;
	 *
	 * @param string $which Table nav location (such as top).
	 */
	function display_tablenav( $which ) {
		$post_status = sanitize_text_field( filter_input( INPUT_GET, 'post_status' ) );
		$slug_filter = sanitize_text_field( filter_input( INPUT_GET, 'slug_filter' ) );
		$allowed_wildcard = sanitize_text_field( filter_input( INPUT_GET, 'allowed_wildcard' ) );
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<?php if ( 'top' === $which ) { ?>
			<form id="posts-filter" action="" method="get">
				<input type="hidden" name="nonce" value="<?php echo $this->nonce; ?>"/>
				<input type="hidden" name="page" value="hreflang_pro_bulk_editor"/>
				<input type="hidden" name="type" value="<?php echo esc_attr( $this->page_type ); ?>"/>
				<input type="hidden" name="orderby"
				       value="<?php echo esc_attr( filter_input( INPUT_GET, 'orderby' ) ); ?>"/>
				<input type="hidden" name="order"
				       value="<?php echo esc_attr( filter_input( INPUT_GET, 'order' ) ); ?>"/>
				<input type="hidden" name="post_type_filter"
				       value="<?php echo esc_attr( filter_input( INPUT_GET, 'post_type_filter' ) ); ?>"/>
				<input type="hidden" name="slug_filter" value="<?php echo esc_attr( $slug_filter ); ?>"/>
				<?php if ( ! empty( $post_status ) ) { ?>
					<input type="hidden" name="post_status" value="<?php echo esc_attr( $post_status ); ?>"/>
				<?php } ?>
				<?php } ?>

				<?php
				$this->extra_tablenav( $which );
				$this->pagination( $which );
				?>

				<br class="clear"/>
				<?php if ( 'top' === $which ) { ?>
			</form>
		<?php } ?>
		</div>

	<?php
	}

	/**
	 * This function builds the base sql subquery used in this class.
	 *
	 * This function takes into account the post types in which the current user can
	 * edit all posts, and the ones the current user can only edit his/her own.
	 *
	 * @return string $subquery The subquery, which should always be used in $wpdb->prepare(), passing the current user_id in as the first parameter.
	 */
	function get_base_subquery() {
		global $wpdb;

		$all_posts_string = "'" . implode( "', '", $this->all_posts ) . "'";
		$own_posts_string = "'" . implode( "', '", $this->own_posts ) . "'";

		$post_author = esc_sql( (int) get_current_user_id() );

		$subquery = "(
				SELECT *
				FROM {$wpdb->posts}
				WHERE post_type IN ({$all_posts_string})
				UNION ALL
				SELECT *
				FROM {$wpdb->posts}
				WHERE post_type IN ({$own_posts_string}) AND post_author = {$post_author}
			) sub_base";

		return $subquery;
	}


	/**
	 * @return array
	 */
	function get_views() {
		global $wpdb;

		$status_links = array();

		$states          = get_post_stati( array( 'show_in_admin_all_list' => true ) );
		$states['trash'] = 'trash';
		$states          = esc_sql( $states );
		$all_states      = "'" . implode( "', '", $states ) . "'";

		$subquery = $this->get_base_subquery();
		$total_posts = $wpdb->get_var(
			"
					SELECT COUNT(ID) FROM {$subquery}
					WHERE post_status IN ({$all_states})
				"
		);


		$post_status         = filter_input( INPUT_GET, 'post_status' );
		$class               = empty( $post_status ) ? ' class="current"' : '';
		$status_links['all'] = '<a href="' . esc_url( admin_url( 'admin.php?page=hreflang_pro_bulk_editor' . $this->page_url ) ) . '"' . $class . '>' . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts', 'hreflang-tags-pro' ), number_format_i18n( $total_posts ) ) . '</a>';

		$post_stati = get_post_stati( array( 'show_in_admin_all_list' => true ), 'objects' );
		if ( is_array( $post_stati ) && $post_stati !== array() ) {
			foreach ( $post_stati as $status ) {

				$status_name = esc_sql( $status->name );

				$total = (int) $wpdb->get_var(
					$wpdb->prepare(
						"
								SELECT COUNT(ID) FROM {$subquery}
								WHERE post_status = %s
							",
						$status_name
					)
				);

				if ( $total === 0 ) {
					continue;
				}

				$class = '';
				if ( $status_name === $post_status ) {
					$class = ' class="current"';
				}

				$status_links[ $status_name ] = '<a href="' . esc_url( add_query_arg( array( 'post_status' => $status_name ), admin_url( 'admin.php?page=hreflang_pro_bulk_editor' . $this->page_url ) ) ) . '"' . $class . '>' . sprintf( translate_nooped_plural( $status->label_count, $total ), number_format_i18n( $total ) ) . '</a>';
			}
		}
		unset( $post_stati, $status, $status_name, $total, $class );

		$trashed_posts = $wpdb->get_var(
			"
					SELECT COUNT(ID) FROM {$subquery}
					WHERE post_status IN ('trash')
				"
		);

		$class = '';
		if ( 'trash' === $post_status ) {
			$class = 'class="current"';
		}
		$status_links['trash'] = '<a href="' . esc_url( admin_url( 'admin.php?page=hreflang_pro_bulk_editor&post_status=trash' . $this->page_url ) ) . '"' . $class . '>' . sprintf( _nx( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', $trashed_posts, 'posts', 'hreflang-tags-pro' ), number_format_i18n( $trashed_posts ) ) . '</a>';

		return $status_links;
	}


	/**
	 * @param string $which Table nav location (such as top).
	 */
	function extra_tablenav( $which ) {

		if ( 'top' === $which ) {
			$post_types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ) );

			if ( is_array( $post_types ) && $post_types !== array() ) {
				global $wpdb;

				echo '<div class="alignleft actions">';

				$post_types = esc_sql( $post_types );
				$post_types = "'" . implode( "', '", $post_types ) . "'";

				$states          = get_post_stati( array( 'show_in_admin_all_list' => true ) );
				$states['trash'] = 'trash';
				$states          = esc_sql( $states );
				$all_states      = "'" . implode( "', '", $states ) . "'";

				$subquery = $this->get_base_subquery();

				$post_types = $wpdb->get_results(
					"
							SELECT DISTINCT post_type FROM {$subquery}
							WHERE post_status IN ({$all_states})
							ORDER BY 'post_type' ASC
						"
				);

				$post_type_filter = filter_input( INPUT_GET, 'post_type_filter' );
				$slug_filter = filter_input( INPUT_GET, 'slug_filter' );
				$allowed_wildcard = filter_input( INPUT_GET, 'allowed_wildcard' );
				$selected         = ( ! empty( $post_type_filter ) ) ? sanitize_text_field( $post_type_filter ) : '-1';
				$checked = (!empty( $allowed_wildcard ) ) ? sanitize_text_field( $allowed_wildcard ) : 'no';

				$options = '<option value="-1">Show All Post Types</option>';

				if ( is_array( $post_types ) && $post_types !== array() ) {
					foreach ( $post_types as $post_type ) {
						$obj = get_post_type_object( $post_type->post_type );
						$options .= sprintf( '<option value="%2$s" %3$s>%1$s</option>', $obj->labels->name, $post_type->post_type, selected( $selected, $post_type->post_type, false ) );
					}
				}

				echo sprintf( '<select name="post_type_filter">%1$s</select>', $options );
				echo sprintf( 'Filter by slug: <input name="slug_filter" id="slug_filter" type="text" size="25" value="%s" placeholder="Please enter slug"> <input type="checkbox" id="allowed_wildcard" name="allowed_wildcard" value="yes" %s> Allow Wildcard ', $slug_filter, ($checked == "yes") ? "checked='checked'" : "");
				submit_button( __( 'Filter', 'hreflang-tags-pro' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
				echo '</div>';
			}
		}
	}

	/**
	 *
	 * @return array
	 */
	function get_sortable_columns() {
		return $sortable = array(
			'col_page_title' => array( 'post_title', true ),
			'col_post_type'  => array( 'post_type', false ),
			'col_post_date'  => array( 'post_date', false ),
		);
	}

	/**
	 * Sets the correct pagenumber and pageurl for the navigation
	 */
	function prepare_page_navigation() {

		$request_url = $this->request_url . $this->page_url;

		$current_page   = $this->current_page;
		$current_filter = $this->current_filter;
		$current_slug_filter = $this->current_slug_filter;
		$current_allowed_wildcard = $this->current_allowed_wildcard;
		$current_status = $this->current_status;
		$current_order  = $this->current_order;
    	//$current_lang   = $this->current_lang;

		// If current type doesn't compare with objects page_type, than we have to unset some vars in the requested url (which will be use for internal table urls).
		if (isset($_GET['type']) && $_GET['type'] != $this->page_type ) {
			$request_url = remove_query_arg( 'paged', $request_url ); // Page will be set with value 1 below.
			$request_url = remove_query_arg( 'post_type_filter', $request_url );
			$request_url = remove_query_arg( 'slug_filter', $request_url );
			$request_url = remove_query_arg( 'allowed_wildcard', $request_url );
			$request_url = remove_query_arg( 'post_status', $request_url );
			$request_url = remove_query_arg( 'orderby', $request_url );
			$request_url = remove_query_arg( 'order', $request_url );
      		//$request_url = remove_query_arg( 'lang', $request_url );
			$request_url = add_query_arg( 'pages', 1, $request_url );

			$current_page   = 1;
			$current_filter = '-1';
			$current_slug_filter = '';
			$current_allowed_wildcard = 'no';
			$current_status = '';
			$current_order  = array( 'orderby' => 'post_title', 'order' => 'asc' );
      		//$current_lang   = 'all';

		}

		$_SERVER['REQUEST_URI'] = $request_url;

		$_GET['paged']                = $current_page;
		$_REQUEST['paged']            = $current_page;
		$_REQUEST['post_type_filter'] = $current_filter;
		$_GET['post_type_filter']     = $current_filter;
		$_REQUEST['slug_filter']  	  = $current_slug_filter;
		$_GET['slug_filter']     	  = $current_slug_filter;
		$_REQUEST['allowed_wildcard'] = $current_allowed_wildcard;
		$_GET['allowed_wildcard']     = $current_allowed_wildcard;
		$_GET['post_status']          = $current_status;
		$_GET['orderby']              = $current_order['orderby'];
		$_GET['order']                = $current_order['order'];
    	//$_GET['lang']                 = $current_order['lang'];

	}

	/**
	 * Preparing the requested pagerows and setting the needed variables
	 */
	function prepare_items() {

		$post_type_clause = $this->get_post_type_clause();
		$slug_clause 	  = $this->get_slug_clause();
		$all_states       = $this->get_all_states();
		$subquery         = $this->get_base_subquery();

		// Setting the column headers.
		$this->set_column_headers();

		// Count the total number of needed items and setting pagination given $total_items.
		$total_items = $this->count_items( $subquery, $all_states, $post_type_clause, $slug_clause );
		$this->set_pagination( $total_items );

		// Getting items given $query.
		$query = $this->parse_item_query( $subquery, $all_states, $post_type_clause, $slug_clause );
		$this->get_items( $query );
		
		$this->get_meta_data();			

	}

	/**
	 * Getting the columns for first row
	 *
	 * @return array
	 */
	public function get_columns() {
		return $this->merge_columns();
	}

	/**
	 * Setting the column headers
	 */
	protected function set_column_headers() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
	}

	/**
	 * Counting total items
	 *
	 * @param string $subquery         SQL FROM part.
	 * @param string $all_states       SQL IN part.
	 * @param string $post_type_clause SQL post type part.
	 *
	 * @return mixed
	 */
	protected function count_items( $subquery, $all_states, $post_type_clause, $slug_clause ) {
		global $wpdb;
		$total_items = $wpdb->get_var(
			"
					SELECT COUNT(ID)
					FROM {$subquery}
					WHERE post_status IN ({$all_states}) $post_type_clause $slug_clause
				"
		);

		return $total_items;
	}

	/**
	 * Getting the post_type_clause filter
	 *
	 * @return string
	 */
	protected function get_post_type_clause() {
		// Filter Block.
		$post_types       = null;
		$post_type_clause = '';
		$post_type_filter = filter_input( INPUT_GET, 'post_type_filter' );

		if ( ! empty( $post_type_filter ) && get_post_type_object( sanitize_text_field( $post_type_filter ) ) ) {
			$post_types       = esc_sql( sanitize_text_field( $post_type_filter ) );
			$post_type_clause = "AND post_type IN ('{$post_types}')";
		}

		return $post_type_clause;
	}
	
	protected function get_slug_clause() {
		$slug = null;
		$slug_clause = '';
		$slug_filter = filter_input( INPUT_GET, 'slug_filter');
		$allowed_wildcard = filter_input( INPUT_GET, 'allowed_wildcard');
		
		if ( ! empty( $slug_filter ) ) {
			$slug = esc_sql( sanitize_text_field( $slug_filter ) );
			if ($allowed_wildcard == "yes") {
				$slug_clause = "AND post_name LIKE '%%".$slug."%%'";
			}
			else {
				$slug_clause = "AND post_name = '".$slug."'";
			}
		}
		
		return $slug_clause;
	}

	/**
	 * Setting the pagination.
	 *
	 * Total items is the number of all visible items.
	 *
	 * @param int $total_items Total items counts.
	 */
	protected function set_pagination( $total_items ) {

		// Calculate items per page.
		$per_page = $this->get_items_per_page( 'hreflang_tags_pro_bulk_editor_posts_per_page', 20 );
		$paged    = esc_sql( sanitize_text_field( filter_input( INPUT_GET, 'paged' ) ) );

		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'total_pages' => ceil( $total_items / $per_page ),
				'per_page'    => $per_page,
			)
		);

		$this->pagination = array(
			'per_page' => $per_page,
			'offset'   => ( $paged - 1 ) * $per_page,
		);

	}

	/**
	 * Parse the query to get items from database.
	 *
	 * Based on given parameters there will be parse a query which will get all the pages/posts and other post_types
	 * from the database.
	 *
	 * @param string $subquery         SQL FROM part.
	 * @param string $all_states       SQL IN part.
	 * @param string $post_type_clause SQL post type part.
	 *
	 * @return string
	 */
	protected function parse_item_query( $subquery, $all_states, $post_type_clause, $slug_clause ) {
		// Order By block.
		$orderby = filter_input( INPUT_GET, 'orderby' );

		$orderby = ! empty( $orderby ) ? esc_sql( sanitize_text_field( $orderby ) ) : 'post_title';
		$orderby = $this->sanitize_orderby( $orderby );

		// Order clause.
		$order = filter_input( INPUT_GET, 'order' );
		$order = ! empty( $order ) ? esc_sql( strtoupper( sanitize_text_field( $order ) ) ) : 'ASC';
		$order = $this->sanitize_order( $order );

		// Get all needed results.
		$query = "
				SELECT ID, post_title, post_name, post_type, post_status, post_modified, post_date
				FROM {$subquery}
				WHERE post_status IN ({$all_states}) $post_type_clause $slug_clause
				ORDER BY {$orderby} {$order}
				LIMIT %d,%d
			";
		return $query;
	}

	/**
	 * Heavily restricts the possible columns by which a user can order the table in the bulk editor, thereby preventing a possible CSRF vulnerability.
	 *
	 * @param string $orderby The column by which we want to order.
	 *
	 * @return string $orderby
	 */
	protected function sanitize_orderby( $orderby ) {
		$valid_column_names = array(
			'post_title',
			'post_type',
			'post_date',
		);

		if ( in_array( $orderby, $valid_column_names ) ) {
			return $orderby;
		}

		return 'post_title';
	}

	/**
	 * Makes sure the order clause is always ASC or DESC for the bulk editor table, thereby preventing a possible CSRF vulnerability.
	 *
	 * @param string $order Whether we want to sort ascending or descending.
	 *
	 * @return string $order SQL order string (ASC, DESC).
	 */
	protected function sanitize_order( $order ) {
		if ( in_array( strtoupper( $order ), array( 'ASC', 'DESC' ) ) ) {
			return $order;
		}

		return 'ASC';
	}

	/**
	 * Getting all the items.
	 *
	 * @param string $query SQL query to use.
	 */
	protected function get_items( $query ) {
		global $wpdb;

		$this->items = $wpdb->get_results(
			$wpdb->prepare(
				$query,
				$this->pagination['offset'],
				$this->pagination['per_page']
			)
		);
	}

	/**
	 * Getting all the states.
	 *
	 * @return string
	 */
	protected function get_all_states() {
		$states          = get_post_stati( array( 'show_in_admin_all_list' => true ) );
		$states['trash'] = 'trash';

		if ( ! empty( $_GET['post_status'] ) ) {
			$requested_state = sanitize_text_field( $_GET['post_status'] );
			if ( in_array( $requested_state, $states ) ) {
				$states = array( $requested_state );
			}
		}

		$states     = esc_sql( $states );
		$all_states = "'" . implode( "', '", $states ) . "'";

		return $all_states;
	}


	/**
	 * Based on $this->items and the defined columns, the table rows will be displayed.
	 */
	function display_rows() {
		global $allregions,$langcode2Name;
		$records = $this->items;

		list( $columns, $hidden ) = $this->get_column_info();

		if ( ( is_array( $records ) && $records !== array() ) && ( is_array( $columns ) && $columns !== array() ) ) {

			foreach ( $records as $rec ) {
				$post_name = 'post-'.$rec->ID;
				$html_lang = get_post_meta($rec->ID,'html_lang',true);
				$html_region = get_post_meta($rec->ID,'html_region',true);
				echo '<tr id="record_', $rec->ID, '">';
				foreach ( $columns as $column_name => $column_display_name ) {

					$attributes = $this->column_attributes( $column_name, $hidden );

					$column_value = $this->parse_column( $column_name, $rec );

					if ( method_exists( $this, 'parse_page_specific_column' ) && empty( $column_value ) ) {
						$column_value = $this->parse_page_specific_column( $column_name, $rec, $attributes );
					}

					if ( ! empty( $column_value ) ) {
						echo sprintf( '<td %2$s>%1$s</td>', $column_value, $attributes );
					}
				}

				echo '</tr>';
				echo '<tr id="record_', $rec->ID, '" class="hreflang-rows">';
				echo '<td colspan="6">';
				echo '<div class="html-wrapper hreflang-pro-html-lang" '.(isset($html_lang) && ($html_lang != '') ? 'style="display:block"' : 'style="display: none"' ).'>';
				echo '<p><strong>HTML lang attribute for '.wp_strip_all_tags( $rec->post_title ).'</strong></p>';
				echo '<label for="meta-box-dropdown">'.__('HTML Tag Language','hreflang-tags-pro').' </label>';
				echo '<select name="html-lang" id="html-lang" class="html-lang">';
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
				echo '<select name="html-region" id="html-region" class="html-region">';
				echo '<option value>'.__('No region/default','hreflang-tags-by-dcgws').'</option>';
				foreach ($allregions as $region) {
				  echo '<option value="'.$region->alpha2Code.'"';
				  if ($region->alpha2Code == $html_region) {
					 echo ' selected="selected"';
				  }
				  echo '>'.$region->name.'</option>';
				}
				echo '</select>';
				echo '<button class="bulk-delete-new-html-tag" data-id="'.$post_name.'"><span class="dashicons dashicons-no"></span></button>';
				echo '</div>';
				echo '<br/>';
				echo '<p><strong>HREFLANG Tags for '.wp_strip_all_tags( $rec->post_title ).'</strong></p>';
				echo '<div class="href-content-wrapper" id="'.$post_name.'">';
				$hreflang_pro_data = get_post_meta($rec->ID);
				$keys = array();
				foreach($hreflang_pro_data as $key=>$value ) {
					if (stristr($key,'hreflang')) {
						$keys[] = $key;
						$values[] = $value;
					}
				}
				if (count($keys) == 0) { ?>
					<div id="hreflang-bulk-<?php echo $post_name; ?>-1" class="href-lang">
					  <label for="hreflang-href"><?php _e('Alternative URL','hreflang-tags-pro'); ?></label>
						<div class="hreflang_pro_link_trigger">
						<a href="javascript:void(0);">Browse</a>
						<input name="hreflang-href[]" class="hreflang-href" id="hreflang-href-<?php echo $post_name; ?>-1" type="text" value="">
					   </div>
					  <label for="meta-box-dropdown"><?php _e('Language','hreflang-tags-pro'); ?></label>
					  <select name="hreflang-lang[]" class="hreflang-lang" id="hreflang-lang">
					  <?php echo '<option value="">'.__('Select one','hreflang-tags-by-dcgws').'</option>';
							foreach ($langcode2Name as $language) {
								echo '<option value="'.$language['code'].'">'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
							}
						echo '</select>'; ?>
					  </select>
					  <label for="meta-box-dropdown"><?php _e('Region','hreflang-tags-pro'); ?></label>
						<select name="hreflang-region[]" id="hreflang-region">
							<option value><?php _e('No region/default','hreflang-tags-by-dcgws'); ?></option>
							<?php	foreach ($allregions as $region) {
									echo '<option value="'.$region->alpha2Code.'">'.$region->name.'</option>';
							} ?>
						</select>
					  <button class="add-new-hreflang-tag" data-id="<?php echo $post_name; ?>"><span class="dashicons dashicons-plus"></span></button>
					</div>
				  </div>
        <?php }
				$n = 0;
				if (is_array($keys) && count($keys) > 0) {
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
					} ?>
                <div id="hreflang-bulk-<?php echo $post_name; ?>-<?php echo ($n+1); ?>" class="href-lang">
                  <label for="hreflang-href"><?php _e('Alternative URL','hreflang-tags-pro'); ?></label>
                  <div class="hreflang_pro_link_trigger">
                  <a href="javascript:void(0);">Browse</a>
                  <input name="hreflang-href[]" type="text" class="hreflang-href" id="hreflang-href-<?php echo $post_name; ?>-<?php echo ($n+1); ?>" value="<?php echo $values[$n][0]; ?>">
                  </div>
                  <label for="meta-box-dropdown"><?php _e('Language','hreflang-tags-pro'); ?></label>
                  <select name="hreflang-lang[]" class="hreflang-lang" id="hreflang-lang">
                  <?php echo '<option value="">'.__('Select one','hreflang-tags-by-dcgws').'</option>';
                  		foreach ($langcode2Name as $language) {
                  			echo '<option value="'.$language['code'].'"';
                  			if ($language['code'] == $href_lang) {
                  				 echo ' selected="selected"';
                  			}
                  			echo '>'.preg_replace("/\([^)]+\)/","",$language['name']).'</option>';
                  		}
                  	echo '</select>'; ?>
                  </select>
                  <label for="meta-box-dropdown"><?php _e('Region','hreflang-tags-pro'); ?></label>
                  <select name="hreflang-region[]" id="hreflang-region" class="hreflang-region">
                  <option value><?php _e('No region/default','hreflang-tags-by-dcgws'); ?></option>
                  <?php	foreach ($allregions as $region) {
                      echo '<option value="'.$region->alpha2Code.'" '; ?>
                      <?php if ($region->alpha2Code == $href_region) { echo 'selected="selected"';} ?>
                      <?php echo '>'.$region->name.'</option>'; ?>
                  <?php } ?>
                  </select>
                  <?php if ( ($n+1) == count($keys)){
                    echo '<button class="bulk-add-new-hreflang-tag" data-id="'.$post_name.'"><span class="dashicons dashicons-plus"></span></button>';
                    echo '<button class="bulk-remove-new-hreflang-tag" data-id="'.$post_name.'"><span class="dashicons dashicons-minus"></span></button>';
                  } else {
                    echo '<button class="bulk-delete-new-hreflang-tag" data-id="'.$post_name.'"><span class="dashicons dashicons-no"></span></button>';
                  } ?>
                </div>
  		  	  <?php
  				$n++;
          }
        }
				unset($keys);
				unset($values);
				?>
      </div>
				<?php
				echo '<hr />';
				echo '</td>';
				echo '</tr>';
			}
		}
	}

	/**
	 * Getting the attributes for each table cell.
	 *
	 * @param string $column_name Column name string.
	 * @param array  $hidden      Set of hidden columns.
	 *
	 * @return string
	 */
	protected function column_attributes( $column_name, $hidden ) {

		$class = sprintf( 'class="%1$s column-%1$s"', $column_name );
		$style = '';

		if ( in_array( $column_name, $hidden ) ) {
			$style = ' style="display:none;"';
		}

		$attributes = $class . $style;

		return $attributes;
	}

	/**
	 * Parsing the title.
	 *
	 * @param WP_Post $rec Post object.
	 *
	 * @return string
	 */
	protected function parse_page_title_column( $rec ) {

		$return = sprintf( '<strong>%1$s</strong>', stripslashes( wp_strip_all_tags( $rec->post_title ) ) );

		$post_type_object = get_post_type_object( $rec->post_type );
		$can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $rec->ID );

		$actions = array();

		if ( $can_edit_post && 'trash' !== $rec->post_status ) {
			$actions['edit'] = '<a href="' . esc_url( get_edit_post_link( $rec->ID, true ) ) . '" title="' . esc_attr( __( 'Edit this item', 'hreflang-tags-pro' ) ) . '">' . __( 'Edit', 'hreflang-tags-pro' ) . '</a>';
		}

		if ( $post_type_object->public ) {
			if ( in_array( $rec->post_status, array( 'pending', 'draft', 'future' ) ) ) {
				if ( $can_edit_post ) {
					$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $rec->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'hreflang-tags-pro' ), $rec->post_title ) ) . '">' . __( 'Preview', 'hreflang-tags-pro' ) . '</a>';
				}
			}
			elseif ( 'trash' !== $rec->post_status ) {
				$actions['view'] = '<a href="' . esc_url( get_permalink( $rec->ID ) ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'hreflang-tags-pro' ), $rec->post_title ) ) . '" rel="bookmark">' . __( 'View', 'hreflang-tags-pro' ) . '</a>';
			}
		}

		$return .= $this->row_actions( $actions );

		return $return;

	}

	/**
	 * Parsing the column based on the $column_name.
	 *
	 * @param string  $column_name Column name.
	 * @param WP_Post $rec         Post object.
	 *
	 * @return string
	 */
	protected function parse_column( $column_name, $rec ) {

		static $date_format;

		if ( $date_format == null ) {
			$date_format = get_option( 'date_format' );
		}

		switch ( $column_name ) {
			case 'col_page_title':
				$column_value = $this->parse_page_title_column( $rec );
				break;

			case 'col_page_slug':
				$permalink    = get_permalink( $rec->ID );
				$display_slug = str_replace( get_bloginfo( 'url' ), '', $permalink );
				$column_value = sprintf( '<a href="%2$s" target="_blank">%1$s</a>', stripslashes( $display_slug ), esc_url( $permalink ) );
				break;

			case 'col_post_type':
				$post_type    = get_post_type_object( $rec->post_type );
				$column_value = $post_type->labels->singular_name;
				break;

			case 'col_post_status':
				$post_status  = get_post_status_object( $rec->post_status );
				$column_value = $post_status->label;
				break;

			case 'col_post_date':
				$column_value = date_i18n( $date_format, strtotime( $rec->post_date ) );
				break;

			case 'col_row_action':
				$column_value = sprintf( '<a href="javascript:void(0);" class="hreflang-save" data-id="%1$s">Save</a>', $rec->ID );
				break;
		}

		if ( ! empty( $column_value ) ) {
			return $column_value;
		}
	}

	/**
	 * Parse the field where the existing meta-data value is displayed.
	 *
	 * @param integer    $record_id  Record ID.
	 * @param string     $attributes HTML attributes.
	 * @param bool|array $values     Optional values data array.
	 *
	 * @return string
	 */
	protected function parse_meta_data_field( $record_id, $attributes, $values = false ) {

		// Fill meta data if exists in $this->meta_data.
		$meta_data  = ( ! empty( $this->meta_data[ $record_id ] ) ) ? $this->meta_data[ $record_id ] : array();
		$meta_key   =  $this->target_db_field;
		$meta_value = ( ! empty( $meta_data[ $meta_key ] ) ) ? $meta_data[ $meta_key ] : '';

		if ( ! empty( $values ) ) {
			$meta_value = $values[ $meta_value ];
		}

		return sprintf( '<td %2$s id="hreflang-existing-%4$s-%3$s">%1$s</td>', $meta_value, $attributes, $record_id, $this->target_db_field );
	}

	/**
	 * Method for setting the meta data, which belongs to the records that will be shown on the current page.
	 *
	 * This method will loop through the current items ($this->items) for getting the post_id. With this data
	 * ($needed_ids) the method will query the meta-data table for getting the title.
	 */
	protected function get_meta_data() {

		$post_ids  = $this->get_post_ids();
		$meta_data = $this->get_meta_data_result( $post_ids );

		$this->parse_meta_data( $meta_data );

		// Little housekeeping.
		unset( $post_ids, $meta_data );

	}

	/**
	 * Getting all post_ids from to $this->items.
	 *
	 * @return string
	 */
	protected function get_post_ids() {
		$needed_ids = array();
		if (is_array($this->items)) {
			foreach ( $this->items as $item ) {
				$needed_ids[] = $item->ID;
			}			
		}

		$post_ids = "'" . implode( "', '", $needed_ids ) . "'";

		return $post_ids;
	}

	/**
	 * Getting the meta_data from database.
	 *
	 * @param string $post_ids Post IDs string for SQL IN part.
	 *
	 * @return mixed
	 */
	protected function get_meta_data_result( $post_ids ) {
		global $wpdb;

		$meta_data = $wpdb->get_results(
			"
				 	SELECT *
				 	FROM {$wpdb->postmeta}
				 	WHERE post_id IN({$post_ids}) && meta_key = '" . $this->target_db_field . "'
				"
		);

		return $meta_data;
	}

	/**
	 * Setting $this->meta_data.
	 *
	 * @param array $meta_data Meta data set.
	 */
	protected function parse_meta_data( $meta_data ) {

		foreach ( $meta_data as $row ) {
			$this->meta_data[ $row->post_id ][ $row->meta_key ] = $row->meta_value;
		}

	}

	/**
	 * This method will merge general array with given parameter $columns.
	 *
	 * @param array $columns Optional columns set.
	 *
	 * @return array
	 */
	protected function merge_columns( $columns = array() ) {
		$columns = array_merge(
			array(
				'col_page_title'  => __( 'WP Page Title', 'hreflang-tags-pro' ),
				'col_post_type'   => __( 'Post Type', 'hreflang-tags-pro' ),
				'col_post_status' => __( 'Post Status', 'hreflang-tags-pro' ),
				'col_post_date'   => __( 'Publication date', 'hreflang-tags-pro' ),
				'col_page_slug'   => __( 'Page URL/Slug', 'hreflang-tags-pro' ),
			),
			$columns
		);

		$columns['col_row_action'] = __( 'Action', 'hreflang-tags-pro' );

		return $columns;
	}
}
