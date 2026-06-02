# Mega Menu Plugin - Developer Documentation

## Plugin Structure

```
mega-menu/
├── mega-menu.php              # Main plugin file (entry point)
├── README.md                  # User documentation
├── QUICKSTART.md             # Quick start guide
├── DEVELOPER.md              # This file
├── admin/
│   ├── class-mega-menu-admin.php    # Admin functionality
│   ├── css/
│   │   └── mega-menu-admin.css      # Admin interface styles
│   └── js/
│       └── mega-menu-admin.js       # Admin interface scripts
├── includes/
│   ├── class-mega-menu-db.php       # Database operations
│   └── class-mega-menu.php          # Frontend shortcode & public functions
└── public/
    ├── css/
    │   └── mega-menu.css            # Frontend styles
    └── js/
        └── mega-menu.js             # Frontend scripts
```

## Core Classes

### 1. Mega_Menu_Plugin (mega-menu.php)
Main plugin class that initializes everything.

**Key Methods:**
- `__construct()` - Hooks into WordPress
- `init()` - Initialize all plugin components
- `activate()` - Run on plugin activation
- `deactivate()` - Run on plugin deactivation

### 2. Mega_Menu_DB (includes/class-mega-menu-db.php)
Handles all database operations.

**Key Methods:**
```php
// Menu Operations
Mega_Menu_DB::create_menu($title, $page_id)
Mega_Menu_DB::get_menu($menu_id)
Mega_Menu_DB::get_all_menus()
Mega_Menu_DB::get_menu_by_shortcode($shortcode)
Mega_Menu_DB::update_menu($menu_id, $title, $page_id)
Mega_Menu_DB::delete_menu($menu_id)

// Menu Item Operations
Mega_Menu_DB::add_menu_item($menu_id, $heading, $link, $image_ids, $row_order)
Mega_Menu_DB::update_menu_item($item_id, $heading, $link, $image_ids, $row_order)
Mega_Menu_DB::delete_menu_item($item_id)
Mega_Menu_DB::get_menu_items($menu_id)
Mega_Menu_DB::get_menu_items_count($menu_id)

// Create Tables
Mega_Menu_DB::create_tables()
```

### 3. Mega_Menu (includes/class-mega-menu.php)
Handles frontend functionality and shortcode rendering.

**Key Methods:**
```php
// Enqueue Scripts & Styles
$mega_menu->enqueue_public_assets()

// Shortcode Handler
$mega_menu->render_shortcode($atts)
```

### 4. Mega_Menu_Admin (admin/class-mega-menu-admin.php)
Handles WordPress admin interface and AJAX operations.

**Key Methods:**
```php
// Admin Menu
$admin->add_admin_menu()
$admin->enqueue_admin_assets($hook)
$admin->render_admin_page()

// AJAX Handlers
$admin->ajax_create_mega_menu()
$admin->ajax_add_menu_item()
$admin->ajax_update_menu_item()
$admin->ajax_delete_menu_item()
$admin->ajax_delete_mega_menu()
$admin->ajax_get_menu_items()
$admin->ajax_get_all_menus()
$admin->ajax_get_attachment_image()
```

## Database Schema

### wp_mega_menus Table
```sql
CREATE TABLE wp_mega_menus (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    page_id bigint(20) NOT NULL,
    shortcode varchar(100) UNIQUE,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

### wp_mega_menu_items Table
```sql
CREATE TABLE wp_mega_menu_items (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    menu_id bigint(20) NOT NULL,
    heading varchar(255) NOT NULL,
    link varchar(500),
    image_ids longtext,
    row_order int(11) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY menu_id (menu_id),
    FOREIGN KEY (menu_id) REFERENCES wp_mega_menus(id) ON DELETE CASCADE
);
```

## Plugin Constants

```php
MEGA_MENU_VERSION          // Current plugin version
MEGA_MENU_PLUGIN_DIR       // Plugin directory path
MEGA_MENU_PLUGIN_URL       // Plugin URL
MEGA_MENU_TABLE_NAME       // Mega menus table name
MEGA_MENU_ITEMS_TABLE_NAME // Menu items table name
```

## Hooks

### Actions

```php
// Plugin initialization
add_action('plugins_loaded', array($this, 'init'));

// Admin hooks
add_action('admin_menu', array($this, 'add_admin_menu'));
add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

// AJAX hooks
add_action('wp_ajax_create_mega_menu', array($this, 'ajax_create_mega_menu'));
add_action('wp_ajax_add_menu_item', array($this, 'ajax_add_menu_item'));
// ... more AJAX hooks

// Frontend hooks
add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
```

### Filters

```php
// None currently, but can be added:
apply_filters('mega_menu_shortcode_output', $output, $menu, $items)
apply_filters('mega_menu_item_html', $item_html, $item)
```

## Shortcode

### Basic Usage
```php
[mega_menu shortcode="your_shortcode_here"]
```

### Attributes
- `shortcode` (required) - The unique shortcode identifier for the menu

## AJAX Endpoints

All AJAX calls require:
- Action parameter: `action=create_mega_menu` (etc.)
- Nonce: `nonce=` (megaMenuData.nonce from JS)

### Create Menu
```
POST /wp-admin/admin-ajax.php?action=create_mega_menu
Parameters:
- title: Menu title
- page_id: WordPress page ID
```

### Add Menu Item
```
POST /wp-admin/admin-ajax.php?action=add_menu_item
Parameters:
- menu_id: Menu ID
- heading: Item heading
- link: Item link
- image_ids: JSON array of attachment IDs
```

### Update Menu Item
```
POST /wp-admin/admin-ajax.php?action=update_menu_item
Parameters:
- item_id: Item ID
- heading: Item heading
- link: Item link
- image_ids: JSON array of attachment IDs
```

### Delete Menu Item
```
POST /wp-admin/admin-ajax.php?action=delete_menu_item
Parameters:
- item_id: Item ID
```

### Delete Menu
```
POST /wp-admin/admin-ajax.php?action=delete_mega_menu
Parameters:
- menu_id: Menu ID
```

### Get Menu Items
```
POST /wp-admin/admin-ajax.php?action=get_menu_items
Parameters:
- menu_id: Menu ID
```

### Get All Menus
```
GET /wp-admin/admin-ajax.php?action=get_all_menus
```

### Get Attachment Image
```
GET /wp-admin/admin-ajax.php?action=get_attachment_image
Parameters:
- image_id: Attachment ID
```

## CSS Classes for Customization

### Frontend
```css
.mega-menu-container       /* Main wrapper */
.mega-menu-content         /* Content grid */
.mega-menu-row             /* Individual item */
.mega-menu-row-content     /* Item content */
.mega-menu-row-info        /* Item info section */
.mega-menu-row-heading     /* Item heading */
.mega-menu-row-heading a   /* Heading link */
.mega-menu-row-images      /* Images container */
.mega-menu-item-image      /* Individual image */
```

### Admin
```css
.mega-menu-admin-wrap      /* Admin page wrapper */
.mega-menu-create-section  /* Create menu section */
.mega-menu-editor-section  /* Edit menu section */
.mega-menu-item            /* Menu item in list */
.menu-item-row             /* Menu item row editor */
.form-group                /* Form input group */
.menu-item-actions         /* Action buttons */
```

## Extending the Plugin

### Example: Add a New AJAX Action

```php
// In your functions.php or custom plugin
add_action('wp_ajax_my_custom_action', 'my_custom_action_handler');

function my_custom_action_handler() {
    check_ajax_referer('mega_menu_nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied');
    }
    
    // Your code here
    
    wp_send_json_success(array('data' => 'value'));
}
```

### Example: Filter Menu Output

```php
// In your theme's functions.php
add_filter('mega_menu_shortcode_output', 'my_custom_menu_output', 10, 3);

function my_custom_menu_output($output, $menu, $items) {
    // Modify $output and return it
    return $output;
}
```

### Example: Add Custom CSS

```php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('my-mega-menu-custom', get_stylesheet_directory_uri() . '/mega-menu-custom.css');
});
```

## Security Considerations

1. **Nonce Verification**: All AJAX requests use nonce verification
2. **Capability Checks**: Only users with `manage_options` can access admin features
3. **Sanitization**: All inputs are sanitized using WordPress functions
4. **Escaping**: All outputs are properly escaped
5. **SQL Injection Prevention**: Uses prepared statements with `$wpdb->prepare()`

## Performance Notes

1. Database queries are optimized with proper WHERE clauses
2. Images are served from WordPress media library (no duplicate storage)
3. CSS and JavaScript are minified and optimized
4. Uses WordPress data caching where appropriate

## Future Enhancement Ideas

1. Add support for menu ordering via drag-and-drop
2. Add template variations for different menu styles
3. Add custom post type for menus instead of custom table
4. Add menu preview in admin
5. Add translation/localization support
6. Add color customization options
7. Add export/import functionality
8. Add menu cloning feature

## Testing

### Manual Testing Checklist
- [ ] Create new menu
- [ ] Add multiple items to menu
- [ ] Upload multiple images
- [ ] Edit item details
- [ ] Delete individual items
- [ ] Delete entire menu
- [ ] View menu on frontend via shortcode
- [ ] Test responsive design on mobile
- [ ] Test image lightbox functionality

### Code Quality
- Uses WordPress coding standards
- Follows object-oriented programming principles
- Properly documented with PHPDoc comments
- All user inputs are sanitized
- All database outputs are escaped

## Support & Contributing

For bug reports, feature requests, or contributions, please contact the plugin developer.

---

**Version**: 1.0.0  
**Last Updated**: 2026-06-02
