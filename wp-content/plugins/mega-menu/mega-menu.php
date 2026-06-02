<?php
/**
 * Plugin Name: Mega Menu
 * Plugin URI: https://example.com/mega-menu
 * Description: Create and manage mega menus with images and links. Assign to pages and use shortcodes.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mega-menu
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'MEGA_MENU_VERSION', '1.0.0' );
define( 'MEGA_MENU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MEGA_MENU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MEGA_MENU_TABLE_NAME', 'wp_mega_menus' );
define( 'MEGA_MENU_ITEMS_TABLE_NAME', 'wp_mega_menu_items' );

// Include required files
require_once MEGA_MENU_PLUGIN_DIR . 'includes/class-mega-menu-db.php';
require_once MEGA_MENU_PLUGIN_DIR . 'includes/class-mega-menu.php';
require_once MEGA_MENU_PLUGIN_DIR . 'admin/class-mega-menu-admin.php';

/**
 * Main Plugin Class
 */
class Mega_Menu_Plugin {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}
	
	public function init() {
		// Initialize database class
		Mega_Menu_DB::get_instance();
		
		// Initialize admin class
		if ( is_admin() ) {
			Mega_Menu_Admin::get_instance();
		}
		
		// Initialize public class (shortcodes, etc.)
		Mega_Menu::get_instance();
		
		// Load text domain
		load_plugin_textdomain( 'mega-menu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	public function activate() {
		// Create database tables
		try {
			Mega_Menu_DB::create_tables();
			
			// Verify tables were created
			global $wpdb;
			$table_exists = $wpdb->get_var( 
				"SELECT COUNT(*) FROM information_schema.tables 
				WHERE table_schema = DATABASE() 
				AND table_name = '" . $wpdb->prefix . "mega_menus'"
			);
			
			if ( ! $table_exists ) {
				wp_die( 'Mega Menu: Failed to create database tables. Please ensure your database user has CREATE TABLE permissions.' );
			}
		} catch ( Exception $e ) {
			wp_die( 'Mega Menu activation error: ' . $e->getMessage() );
		}
	}
	
	public function deactivate() {
		// Could delete tables here if needed, but usually we keep them
		// to preserve user data when plugin is temporarily deactivated
	}
}

// Initialize the plugin
Mega_Menu_Plugin::get_instance();
?>
