<?php
/**
 * Admin class for Mega Menu
 */

class Mega_Menu_Admin {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function __construct() {
		add_action( 'admin_init', array( $this, 'ensure_tables_exist' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_ajax_create_mega_menu', array( $this, 'ajax_create_mega_menu' ) );
		add_action( 'wp_ajax_add_menu_item', array( $this, 'ajax_add_menu_item' ) );
		add_action( 'wp_ajax_update_menu_item', array( $this, 'ajax_update_menu_item' ) );
		add_action( 'wp_ajax_delete_menu_item', array( $this, 'ajax_delete_menu_item' ) );
		add_action( 'wp_ajax_delete_mega_menu', array( $this, 'ajax_delete_mega_menu' ) );
		add_action( 'wp_ajax_get_menu_items', array( $this, 'ajax_get_menu_items' ) );
		add_action( 'wp_ajax_get_all_menus', array( $this, 'ajax_get_all_menus' ) );
		add_action( 'wp_ajax_get_attachment_image', array( $this, 'ajax_get_attachment_image' ) );
	}
	
	/**
	 * Ensure database tables exist
	 */
	public function ensure_tables_exist() {
		global $wpdb;
		
		// Check menus table
		$menus_table = $wpdb->prefix . 'mega_menus';
		$items_table = $wpdb->prefix . 'mega_menu_items';
		
		$charset_collate = $wpdb->get_charset_collate();
		
		// Create menus table
		$sql = "CREATE TABLE IF NOT EXISTS $menus_table (
		  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  title varchar(255) NOT NULL,
		  page_id bigint(20) unsigned NOT NULL,
		  shortcode varchar(100),
		  created_at datetime,
		  updated_at datetime,
		  PRIMARY KEY  (id)
		) $charset_collate;";
		
		$wpdb->query( $sql );
		
		// Create items table
		$sql2 = "CREATE TABLE IF NOT EXISTS $items_table (
		  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  menu_id bigint(20) unsigned NOT NULL,
		  heading varchar(255) NOT NULL,
		  link varchar(500),
		  image_ids longtext,
		  row_order int(11),
		  created_at datetime,
		  updated_at datetime,
		  PRIMARY KEY  (id)
		) $charset_collate;";
		
		$wpdb->query( $sql2 );
		
		// Verify tables were created
		$check = $wpdb->get_var( "SHOW TABLES LIKE '$menus_table'" );
		
		if ( ! $check ) {
			error_log( 'Mega Menu: Failed to create wp_mega_menus table' );
			error_log( 'Last error: ' . $wpdb->last_error );
		}
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Mega Menu', 'mega-menu' ),
			__( 'Mega Menu', 'mega-menu' ),
			'manage_options',
			'mega-menu',
			array( $this, 'render_admin_page' ),
			'dashicons-menu',
			25
		);
		
		// Add submenu for viewing all menus
		add_submenu_page(
			'mega-menu',
			__( 'View All Menus', 'mega-menu' ),
			__( 'View All Menus', 'mega-menu' ),
			'manage_options',
			'mega-menu-view-all',
			array( $this, 'render_view_all_menus' )
		);
	}
	
	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( $hook !== 'toplevel_page_mega-menu' ) {
			return;
		}
		
		wp_enqueue_style(
			'mega-menu-admin',
			MEGA_MENU_PLUGIN_URL . 'admin/css/mega-menu-admin.css',
			array(),
			MEGA_MENU_VERSION
		);
		
		wp_enqueue_script(
			'mega-menu-admin',
			MEGA_MENU_PLUGIN_URL . 'admin/js/mega-menu-admin.js',
			array( 'jquery', 'wp-util' ),
			MEGA_MENU_VERSION,
			true
		);
		
		// Pass data to JavaScript
		wp_localize_script(
			'mega-menu-admin',
			'megaMenuData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'mega_menu_nonce' ),
			)
		);
		
		// WordPress media uploader
		wp_enqueue_media();
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		?>
		<div class="wrap mega-menu-admin-wrap">
			<h1><?php esc_html_e( 'Mega Menu', 'mega-menu' ); ?></h1>
			
			<div class="mega-menu-admin-container">
				<!-- Left: Create Menu Section -->
				<div class="mega-menu-create-section">
					<h2><?php esc_html_e( 'Create New Mega Menu', 'mega-menu' ); ?></h2>
					
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="menu-title"><?php esc_html_e( 'Menu Title', 'mega-menu' ); ?></label>
							</th>
							<td>
								<input type="text" id="menu-title" placeholder="<?php esc_attr_e( 'e.g., Sports Menu', 'mega-menu' ); ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="menu-page"><?php esc_html_e( 'Select Page', 'mega-menu' ); ?></label>
							</th>
							<td>
								<select id="menu-page">
									<option value=""><?php esc_html_e( '-- Select a Page --', 'mega-menu' ); ?></option>
									<?php $this->render_pages_dropdown(); ?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"></th>
							<td>
								<button type="button" class="button button-primary" id="create-menu-btn">
									<?php esc_html_e( 'Create Menu', 'mega-menu' ); ?>
								</button>
							</td>
						</tr>
					</table>
				</div>
				
				<!-- Right: Menu Items Editor -->
				<div class="mega-menu-editor-section">
					<h2><?php esc_html_e( 'Edit Menu Items', 'mega-menu' ); ?></h2>
					
					<div id="menus-list" class="menus-list">
						<p><?php esc_html_e( 'No menus created yet. Create a menu to get started.', 'mega-menu' ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render pages dropdown
	 */
	private function render_pages_dropdown() {
		$pages = get_pages( array(
			'number' => -1,
		) );
		
		foreach ( $pages as $page ) {
			echo '<option value="' . esc_attr( $page->ID ) . '">' . esc_html( $page->post_title ) . '</option>';
		}
	}
	
	/**
	 * AJAX: Create mega menu
	 */
	public function ajax_create_mega_menu() {
		// Verify nonce
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		
		if ( ! wp_verify_nonce( $nonce, 'mega_menu_nonce' ) ) {
			wp_send_json_error( array(
				'message' => 'Security check failed',
				'code'    => 'nonce_failed'
			) );
		}
		
		// Check permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array(
				'message' => 'Permission denied',
				'code'    => 'permission_denied'
			) );
		}
		
		// Ensure tables exist
		$this->ensure_tables_exist();
		
		$title   = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$page_id = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
		
		if ( empty( $title ) || empty( $page_id ) ) {
			wp_send_json_error( array(
				'message' => 'Title and page are required',
				'code'    => 'missing_required_fields',
				'title'   => $title,
				'page_id' => $page_id
			) );
		}
		
		$menu_id = Mega_Menu_DB::create_menu( $title, $page_id );
		
		if ( ! $menu_id ) {
			global $wpdb;
			wp_send_json_error( array(
				'message'       => 'Failed to create menu',
				'code'          => 'db_error',
				'db_error'      => $wpdb->last_error,
				'title'         => $title,
				'page_id'       => $page_id
			) );
		}
		
		$menu = Mega_Menu_DB::get_menu( $menu_id );
		
		if ( ! $menu ) {
			wp_send_json_error( array(
				'message' => 'Menu created but could not retrieve it',
				'code'    => 'menu_not_found',
				'menu_id' => $menu_id
			) );
		}
		
		wp_send_json_success( array(
			'menu' => $menu,
			'html' => $this->render_menu_editor( $menu ),
		) );
	}
	
	/**
	 * AJAX: Add menu item
	 */
	public function ajax_add_menu_item() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		
		if ( ! wp_verify_nonce( $nonce, 'mega_menu_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Security check failed', 'code' => 'nonce_failed' ) );
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied', 'code' => 'permission_denied' ) );
		}
		
		// Ensure tables exist
		$this->ensure_tables_exist();
		
		$menu_id   = isset( $_POST['menu_id'] ) ? intval( $_POST['menu_id'] ) : 0;
		$heading   = isset( $_POST['heading'] ) ? sanitize_text_field( $_POST['heading'] ) : '';
		$link      = isset( $_POST['link'] ) ? esc_url( $_POST['link'] ) : '';
		$image_ids = isset( $_POST['image_ids'] ) ? sanitize_text_field( $_POST['image_ids'] ) : '[]';
		
		if ( empty( $menu_id ) ) {
			wp_send_json_error( array( 'message' => 'Menu ID is required', 'code' => 'missing_menu_id' ) );
		}
		
		// Get the next row order
		$row_order = Mega_Menu_DB::get_menu_items_count( $menu_id );
		
		$item_id = Mega_Menu_DB::add_menu_item( $menu_id, $heading, $link, $image_ids, $row_order );
		
		if ( ! $item_id ) {
			global $wpdb;
			wp_send_json_error( array(
				'message'  => 'Failed to add menu item',
				'code'     => 'db_error',
				'db_error' => $wpdb->last_error,
				'menu_id'  => $menu_id,
				'heading'  => $heading
			) );
		}
		
		wp_send_json_success( array(
			'item_id' => $item_id,
			'html'    => $this->render_menu_item_row( $item_id, $heading, $link, $image_ids ),
		) );
	}
	
	/**
	 * AJAX: Update menu item
	 */
	public function ajax_update_menu_item() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		
		if ( ! wp_verify_nonce( $nonce, 'mega_menu_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Security check failed', 'code' => 'nonce_failed' ) );
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Permission denied', 'code' => 'permission_denied' ) );
		}
		
		// Ensure tables exist
		$this->ensure_tables_exist();
		
		$item_id   = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : 0;
		$heading   = isset( $_POST['heading'] ) ? sanitize_text_field( $_POST['heading'] ) : '';
		$link      = isset( $_POST['link'] ) ? esc_url( $_POST['link'] ) : '';
		$image_ids = isset( $_POST['image_ids'] ) ? sanitize_text_field( $_POST['image_ids'] ) : '[]';
		
		if ( empty( $item_id ) ) {
			wp_send_json_error( array( 'message' => 'Item ID is required', 'code' => 'missing_item_id' ) );
		}
		
		$result = Mega_Menu_DB::update_menu_item( $item_id, $heading, $link, $image_ids, 0 );
		
		if ( $result === false ) {
			global $wpdb;
			wp_send_json_error( array(
				'message'  => 'Failed to update menu item',
				'code'     => 'db_error',
				'db_error' => $wpdb->last_error,
				'item_id'  => $item_id,
				'heading'  => $heading
			) );
		}
		
		wp_send_json_success( array( 'success' => true, 'rows_affected' => $result ) );
	}
	
	/**
	 * AJAX: Delete menu item
	 */
	public function ajax_delete_menu_item() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		
		if ( ! wp_verify_nonce( $nonce, 'mega_menu_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed', 'mega-menu' ) );
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'mega-menu' ) );
		}
		
		$item_id = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : 0;
		
		if ( empty( $item_id ) ) {
			wp_send_json_error( __( 'Item ID is required', 'mega-menu' ) );
		}
		
		$result = Mega_Menu_DB::delete_menu_item( $item_id );
		
		if ( ! $result ) {
			wp_send_json_error( __( 'Failed to delete menu item', 'mega-menu' ) );
		}
		
		wp_send_json_success( array( 'success' => true ) );
	}
	
	/**
	 * AJAX: Delete mega menu
	 */
	public function ajax_delete_mega_menu() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		
		if ( ! wp_verify_nonce( $nonce, 'mega_menu_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed', 'mega-menu' ) );
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'mega-menu' ) );
		}
		
		$menu_id = isset( $_POST['menu_id'] ) ? intval( $_POST['menu_id'] ) : 0;
		
		if ( empty( $menu_id ) ) {
			wp_send_json_error( __( 'Menu ID is required', 'mega-menu' ) );
		}
		
		$result = Mega_Menu_DB::delete_menu( $menu_id );
		
		if ( ! $result ) {
			wp_send_json_error( __( 'Failed to delete menu', 'mega-menu' ) );
		}
		
		wp_send_json_success( array( 'success' => true ) );
	}
	
	/**
	 * AJAX: Get menu items
	 */
	public function ajax_get_menu_items() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		
		if ( ! wp_verify_nonce( $nonce, 'mega_menu_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed', 'mega-menu' ) );
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'mega-menu' ) );
		}
		
		$menu_id = isset( $_POST['menu_id'] ) ? intval( $_POST['menu_id'] ) : 0;
		
		if ( empty( $menu_id ) ) {
			wp_send_json_error( __( 'Menu ID is required', 'mega-menu' ) );
		}
		
		$menu_items = Mega_Menu_DB::get_menu_items( $menu_id );
		
		$html = '';
		foreach ( $menu_items as $item ) {
			$html .= $this->render_menu_item_row( $item->id, $item->heading, $item->link, $item->image_ids );
		}
		
		wp_send_json_success( array(
			'items' => $menu_items,
			'html'  => $html,
		) );
	}
	
	/**
	 * Render menu editor HTML
	 */
	private function render_menu_editor( $menu ) {
		ob_start();
		?>
		<div class="mega-menu-item" data-menu-id="<?php echo esc_attr( $menu->id ); ?>">
			<div class="menu-header">
				<h3><?php echo esc_html( $menu->title ); ?></h3>
				<div class="menu-actions">
					<span class="shortcode-display">
						<strong><?php esc_html_e( 'Shortcode:', 'mega-menu' ); ?></strong>
						<code>[mega_menu shortcode="<?php echo esc_attr( $menu->shortcode ); ?>"]</code>
						<button type="button" class="button button-small copy-shortcode" title="<?php esc_attr_e( 'Copy to clipboard', 'mega-menu' ); ?>">
							<?php esc_html_e( 'Copy', 'mega-menu' ); ?>
						</button>
					</span>
					<button type="button" class="button button-small delete-menu" data-menu-id="<?php echo esc_attr( $menu->id ); ?>">
						<?php esc_html_e( 'Delete Menu', 'mega-menu' ); ?>
					</button>
				</div>
			</div>
			
			<div class="menu-items-container">
				<div class="menu-items-list"></div>
				<button type="button" class="button button-secondary add-row-btn" data-menu-id="<?php echo esc_attr( $menu->id ); ?>">
					<?php esc_html_e( '+ Add Row', 'mega-menu' ); ?>
				</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Render menu item row HTML
	 */
	private function render_menu_item_row( $item_id, $heading, $link, $image_ids ) {
		ob_start();
		?>
		<div class="menu-item-row" data-item-id="<?php echo esc_attr( $item_id ); ?>">
			<div class="menu-item-content">
				<div class="form-group inline-group">
					<label><?php esc_html_e( 'Heading', 'mega-menu' ); ?></label>
					<input type="text" class="item-heading" value="<?php echo esc_attr( $heading ); ?>" placeholder="<?php esc_attr_e( 'Enter heading', 'mega-menu' ); ?>" />
				</div>
				
				<div class="form-group inline-group">
					<label><?php esc_html_e( 'Link', 'mega-menu' ); ?></label>
					<input type="url" class="item-link" value="<?php echo esc_attr( $link ); ?>" placeholder="<?php esc_attr_e( 'https://example.com', 'mega-menu' ); ?>" />
				</div>
				
				<div class="form-group inline-group">
					<label><?php esc_html_e( 'Images', 'mega-menu' ); ?></label>
					<button type="button" class="button upload-images-btn" data-item-id="<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Add Images', 'mega-menu' ); ?>
					</button>
				</div>
			</div>
			
			<div class="images-preview-row">
				<?php 
				$image_ids_array = json_decode( $image_ids, true );
				if ( is_array( $image_ids_array ) && count( $image_ids_array ) > 0 ) {
					foreach ( $image_ids_array as $img_id ) {
						$img_src = wp_get_attachment_image_src( $img_id, 'thumbnail' );
						if ( $img_src ) {
							?>
							<div class="image-thumbnail" data-image-id="<?php echo esc_attr( $img_id ); ?>">
								<img src="<?php echo esc_url( $img_src[0] ); ?>" alt="Thumbnail" />
								<button type="button" class="remove-image" data-image-id="<?php echo esc_attr( $img_id ); ?>">×</button>
							</div>
							<?php
						}
					}
				}
				?>
			</div>
			<input type="hidden" class="item-image-ids" value="<?php echo esc_attr( $image_ids ); ?>" />
			
			<div class="menu-item-actions">
				<button type="button" class="button button-small save-item" data-item-id="<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Save', 'mega-menu' ); ?>
				</button>
				<button type="button" class="button button-small button-link-delete delete-item" data-item-id="<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Delete', 'mega-menu' ); ?>
				</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * AJAX: Get all menus
	 */
	public function ajax_get_all_menus() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		
		if ( ! wp_verify_nonce( $nonce, 'mega_menu_nonce' ) ) {
			wp_send_json_error( __( 'Security check failed', 'mega-menu' ) );
		}
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied', 'mega-menu' ) );
		}
		
		$menus = Mega_Menu_DB::get_all_menus();
		
		wp_send_json_success( array(
			'menus' => $menus,
		) );
	}
	
	/**
	 * AJAX: Get attachment image
	 */
	public function ajax_get_attachment_image() {
		$image_id = isset( $_GET['image_id'] ) ? intval( $_GET['image_id'] ) : 0;
		
		if ( empty( $image_id ) ) {
			wp_send_json_error( __( 'Image ID is required', 'mega-menu' ) );
		}
		
		$image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		
		if ( ! $image_src ) {
			wp_send_json_error( __( 'Image not found', 'mega-menu' ) );
		}
		
		wp_send_json_success( array(
			'src' => $image_src[0],
		) );
	}
}
?>
