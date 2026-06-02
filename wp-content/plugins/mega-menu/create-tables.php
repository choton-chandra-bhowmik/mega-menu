<?php
/**
 * Direct Table Creation Script
 * Visit: http://yoursite.com/wp-content/plugins/mega-menu/create-tables.php
 */

// Load WordPress
require_once( '../../../../wp-load.php' );

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Access denied.' );
}

global $wpdb;

echo '<h2>Mega Menu - Direct Table Creation</h2>';
echo '<p>Creating tables...</p>';

$charset_collate = $wpdb->get_charset_collate();

$menus_table = $wpdb->prefix . 'mega_menus';
$items_table = $wpdb->prefix . 'mega_menu_items';

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

$result1 = $wpdb->query( $sql );
echo '<p>Menus table result: ' . ( $result1 !== false ? 'SUCCESS' : 'FAILED' ) . '</p>';
if ( ! empty( $wpdb->last_error ) ) {
	echo '<p style="color: red;">Error: ' . $wpdb->last_error . '</p>';
}

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

$result2 = $wpdb->query( $sql2 );
echo '<p>Items table result: ' . ( $result2 !== false ? 'SUCCESS' : 'FAILED' ) . '</p>';
if ( ! empty( $wpdb->last_error ) ) {
	echo '<p style="color: red;">Error: ' . $wpdb->last_error . '</p>';
}

// Verify
echo '<br><h3>Verification:</h3>';
$check1 = $wpdb->get_var( "SHOW TABLES LIKE '$menus_table'" );
$check2 = $wpdb->get_var( "SHOW TABLES LIKE '$items_table'" );

echo '<p>' . $menus_table . ': ' . ( $check1 ? '<span style="color: green;">✓ EXISTS</span>' : '<span style="color: red;">✗ MISSING</span>' ) . '</p>';
echo '<p>' . $items_table . ': ' . ( $check2 ? '<span style="color: green;">✓ EXISTS</span>' : '<span style="color: red;">✗ MISSING</span>' ) . '</p>';

if ( $check1 && $check2 ) {
	echo '<p style="color: green; font-weight: bold;">✓ All tables created successfully! You can now use the plugin.</p>';
} else {
	echo '<p style="color: red; font-weight: bold;">✗ Some tables are still missing.</p>';
	echo '<p>Database Name: ' . DB_NAME . '</p>';
	echo '<p>Table Prefix: ' . $wpdb->prefix . '</p>';
}

echo '<p><a href="' . admin_url( 'admin.php?page=mega-menu' ) . '">← Back to Mega Menu</a></p>';
?>
