<?php
/**
 * Database class for Mega Menu
 */

class Mega_Menu_DB {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Create database tables
	 */
	public static function create_tables() {
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$menus_table = $wpdb->prefix . 'mega_menus';
		$items_table = $wpdb->prefix . 'mega_menu_items';
		$image_data_table = $wpdb->prefix . 'mega_menu_image_data';
		
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
		
		// Create image metadata table
		$sql3 = "CREATE TABLE IF NOT EXISTS $image_data_table (
		  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  menu_item_id bigint(20) unsigned NOT NULL,
		  image_id bigint(20) unsigned NOT NULL,
		  image_text varchar(500),
		  image_url varchar(1000),
		  image_order int(11) DEFAULT 0,
		  created_at datetime,
		  updated_at datetime,
		  PRIMARY KEY  (id),
		  UNIQUE KEY unique_image (menu_item_id, image_id)
		) $charset_collate;";
		
		$wpdb->query( $sql3 );
		
		if ( ! empty( $wpdb->last_error ) ) {
			error_log( 'Mega Menu DB Error: ' . $wpdb->last_error );
		}
	}
	
	/**
	 * Create a new mega menu
	 */
	public static function create_menu( $title, $page_id ) {
		global $wpdb;
		
		$shortcode = sanitize_title( $title ) . '_' . uniqid();
		
		$result = $wpdb->insert(
			$wpdb->prefix . 'mega_menus',
			array(
				'title'     => sanitize_text_field( $title ),
				'page_id'   => intval( $page_id ),
				'shortcode' => $shortcode,
			),
			array( '%s', '%d', '%s' )
		);
		
		if ( $result ) {
			return $wpdb->insert_id;
		}
		
		// Log error for debugging
		error_log( 'Mega Menu DB Error: ' . $wpdb->last_error );
		
		return false;
	}
	
	/**
	 * Get menu by ID
	 */
	public static function get_menu( $menu_id ) {
		global $wpdb;
		
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM " . $wpdb->prefix . "mega_menus WHERE id = %d",
				$menu_id
			)
		);
	}
	
	/**
	 * Get all menus
	 */
	public static function get_all_menus() {
		global $wpdb;
		
		return $wpdb->get_results(
			"SELECT * FROM " . $wpdb->prefix . "mega_menus ORDER BY created_at DESC"
		);
	}
	
	/**
	 * Get menu by shortcode
	 */
	public static function get_menu_by_shortcode( $shortcode ) {
		global $wpdb;
		
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM " . $wpdb->prefix . "mega_menus WHERE shortcode = %s",
				$shortcode
			)
		);
	}
	
	/**
	 * Update menu
	 */
	public static function update_menu( $menu_id, $title, $page_id ) {
		global $wpdb;
		
		return $wpdb->update(
			$wpdb->prefix . 'mega_menus',
			array(
				'title'   => sanitize_text_field( $title ),
				'page_id' => intval( $page_id ),
			),
			array( 'id' => $menu_id ),
			array( '%s', '%d' ),
			array( '%d' )
		);
	}
	
	/**
	 * Delete menu and its items
	 */
	public static function delete_menu( $menu_id ) {
		global $wpdb;
		
		// Delete all menu items first
		$wpdb->delete(
			$wpdb->prefix . 'mega_menu_items',
			array( 'menu_id' => $menu_id ),
			array( '%d' )
		);
		
		// Delete the menu
		return $wpdb->delete(
			$wpdb->prefix . 'mega_menus',
			array( 'id' => $menu_id ),
			array( '%d' )
		);
	}
	
	/**
	 * Add menu item
	 */
	public static function add_menu_item( $menu_id, $heading, $link, $image_ids, $row_order ) {
		global $wpdb;
		
		$result = $wpdb->insert(
			$wpdb->prefix . 'mega_menu_items',
			array(
				'menu_id'    => intval( $menu_id ),
				'heading'    => sanitize_text_field( $heading ),
				'link'       => esc_url( $link ),
				'image_ids'  => $image_ids, // JSON array as string
				'row_order'  => intval( $row_order ),
			),
			array( '%d', '%s', '%s', '%s', '%d' )
		);
		
		if ( $result ) {
			return $wpdb->insert_id;
		}
		
		return false;
	}
	
	/**
	 * Update menu item
	 */
	public static function update_menu_item( $item_id, $heading, $link, $image_ids, $row_order ) {
		global $wpdb;
		
		return $wpdb->update(
			$wpdb->prefix . 'mega_menu_items',
			array(
				'heading'    => sanitize_text_field( $heading ),
				'link'       => esc_url( $link ),
				'image_ids'  => $image_ids,
				'row_order'  => intval( $row_order ),
			),
			array( 'id' => $item_id ),
			array( '%s', '%s', '%s', '%d' ),
			array( '%d' )
		);
	}
	
	/**
	 * Delete menu item
	 */
	public static function delete_menu_item( $item_id ) {
		global $wpdb;
		
		return $wpdb->delete(
			$wpdb->prefix . 'mega_menu_items',
			array( 'id' => $item_id ),
			array( '%d' )
		);
	}
	
	/**
	 * Get menu items
	 */
	public static function get_menu_items( $menu_id ) {
		global $wpdb;
		
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM " . $wpdb->prefix . "mega_menu_items WHERE menu_id = %d ORDER BY row_order ASC",
				$menu_id
			)
		);
	}
	
	/**
	 * Get single menu item by ID
	 */
	public static function get_menu_item( $item_id ) {
		global $wpdb;
		
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM " . $wpdb->prefix . "mega_menu_items WHERE id = %d",
				$item_id
			)
		);
	}
	
	/**
	 * Save image metadata for a menu item
	 */
	public static function save_image_metadata( $menu_item_id, $image_id, $image_text = '', $image_url = '' ) {
		global $wpdb;
		
		$table = $wpdb->prefix . 'mega_menu_image_data';
		
		// Check if record exists
		$existing = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id FROM $table WHERE menu_item_id = %d AND image_id = %d",
				$menu_item_id,
				$image_id
			)
		);
		
		if ( $existing ) {
			// Update existing record
			return $wpdb->update(
				$table,
				array(
					'image_text' => $image_text,
					'image_url'  => $image_url,
					'updated_at' => current_time( 'mysql' ),
				),
				array(
					'menu_item_id' => $menu_item_id,
					'image_id'     => $image_id,
				),
				array( '%s', '%s', '%s' ),
				array( '%d', '%d' )
			);
		} else {
			// Insert new record
			return $wpdb->insert(
				$table,
				array(
					'menu_item_id' => $menu_item_id,
					'image_id'     => $image_id,
					'image_text'   => $image_text,
					'image_url'    => $image_url,
					'created_at'   => current_time( 'mysql' ),
					'updated_at'   => current_time( 'mysql' ),
				),
				array( '%d', '%d', '%s', '%s', '%s', '%s' )
			);
		}
	}
	
	/**
	 * Get image metadata for a menu item
	 */
	public static function get_image_metadata( $menu_item_id ) {
		global $wpdb;
		
		$table = $wpdb->prefix . 'mega_menu_image_data';
		
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table WHERE menu_item_id = %d ORDER BY image_order ASC",
				$menu_item_id
			)
		);
	}
	
	/**
	 * Delete image metadata for a specific image
	 */
	public static function delete_image_metadata( $menu_item_id, $image_id ) {
		global $wpdb;
		
		$table = $wpdb->prefix . 'mega_menu_image_data';
		
		return $wpdb->delete(
			$table,
			array(
				'menu_item_id' => $menu_item_id,
				'image_id'     => $image_id,
			),
			array( '%d', '%d' )
		);
	}
	
	/**
	 * Delete all image metadata for a menu item
	 */
	public static function delete_all_image_metadata( $menu_item_id ) {
		global $wpdb;
		
		$table = $wpdb->prefix . 'mega_menu_image_data';
		
		return $wpdb->delete(
			$table,
			array( 'menu_item_id' => $menu_item_id ),
			array( '%d' )
		);
	}
	
	/**
	 * Get menu items count
	 */
	public static function get_menu_items_count( $menu_id ) {
		global $wpdb;
		
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM " . $wpdb->prefix . "mega_menu_items WHERE menu_id = %d",
				$menu_id
			)
		);
	}
}
?>
