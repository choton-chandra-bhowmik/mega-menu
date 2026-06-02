# Mega Menu Plugin for WordPress

A powerful and easy-to-use WordPress plugin to create customizable mega menus with images, links, and multiple rows. Perfect for creating featured content sections on your website.

## Features

- ✨ **Easy Menu Creation**: Create multiple mega menus directly from the WordPress dashboard
- 📄 **Page Assignment**: Assign each mega menu to any WordPress page
- 🖼️ **Image Support**: Upload multiple images per menu row
- 🔗 **Custom Links**: Add links to each menu row
- 📋 **Multiple Rows**: Add unlimited rows to each mega menu
- 🎨 **Responsive Design**: Beautiful, mobile-friendly frontend
- 📋 **Shortcode Support**: Generate unique shortcodes for each menu to use anywhere
- 🛡️ **Security**: Built with WordPress security best practices

## Installation

1. Download the plugin files to your WordPress plugins directory:
   ```
   wp-content/plugins/mega-menu/
   ```

2. Activate the plugin from the WordPress Plugins page (go to **Plugins** → **Installed Plugins** → Find "Mega Menu" and click **Activate**)

3. The plugin will automatically create the required database tables on activation

## Getting Started

### Step 1: Create a Mega Menu

1. Go to **WordPress Dashboard** → **Mega Menu**
2. Fill in the menu details:
   - **Menu Title**: Enter a descriptive name (e.g., "Sports Menu")
   - **Select Page**: Choose which page this menu will be associated with
3. Click **Create Menu**

### Step 2: Add Menu Rows

After creating a menu, you'll see it in the **Edit Menu Items** section:

1. Click **+ Add Row** to add a new menu item
2. For each row, enter:
   - **Heading**: The title of this menu item
   - **Link**: The URL to link to (optional)
   - **Upload Images**: Click to upload one or more images

### Step 3: Manage Images

- Click **Upload Images** to add images via the WordPress media library
- Upload multiple images at once
- Click the **×** button on image thumbnails to remove them
- Click **Save** to save your changes

### Step 4: Use the Shortcode

Once you've created and customized your mega menu:

1. Copy the shortcode shown in the menu header (e.g., `[mega_menu shortcode="sports_123abc"]`)
2. Paste it into any WordPress page or post where you want the menu to appear
3. The menu will display with all your configured rows and images

## Complete Workflow Example

### Creating a Sports Menu

1. **Create Menu**:
   - Title: "Sports"
   - Page: Sports (select from dropdown)
   - Click "Create Menu"

2. **Add Rows** (click "+ Add Row" for each):
   
   Row 1:
   - Heading: "Football"
   - Link: "https://example.com/football"
   - Upload 2-3 football images
   - Click "Save"
   
   Row 2:
   - Heading: "Basketball"
   - Link: "https://example.com/basketball"
   - Upload 2-3 basketball images
   - Click "Save"
   
   Row 3:
   - Heading: "Tennis"
   - Link: "https://example.com/tennis"
   - Upload 2-3 tennis images
   - Click "Save"

3. **Use Shortcode**:
   - Copy: `[mega_menu shortcode="sports_123abc"]`
   - Paste on any page where you want the Sports Menu to appear

## Dashboard Layout

The Mega Menu dashboard has two main sections:

### Left Panel - Create New Mega Menu
- Menu Title input field
- Page selector dropdown
- "Create Menu" button

### Right Panel - Edit Menu Items
- Lists all created menus
- Shows shortcode for each menu with a copy button
- Delete button to remove entire menu
- "+ Add Row" button to add new items
- Edit panel for each row with save and delete options

## Shortcode Usage

### Basic Usage
```
[mega_menu shortcode="your_shortcode_here"]
```

### Example Shortcodes
```
[mega_menu shortcode="sports_123abc"]
[mega_menu shortcode="partner_456def"]
[mega_menu shortcode="franchise_789ghi"]
```

## Styling

The plugin includes built-in responsive CSS. You can customize the appearance by:

1. Using CSS overrides in your theme's custom CSS
2. Editing the CSS files:
   - Frontend: `public/css/mega-menu.css`
   - Admin: `admin/css/mega-menu-admin.css`

### Frontend CSS Classes

- `.mega-menu-container` - Main wrapper
- `.mega-menu-content` - Content grid
- `.mega-menu-row` - Individual menu item
- `.mega-menu-row-heading` - Row heading
- `.mega-menu-item-image` - Menu images

## Database Structure

The plugin creates two database tables:

### wp_mega_menus
Stores mega menu information:
- `id` - Menu ID
- `title` - Menu name
- `page_id` - Associated WordPress page
- `shortcode` - Unique shortcode identifier
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

### wp_mega_menu_items
Stores menu row data:
- `id` - Item ID
- `menu_id` - Associated menu (foreign key)
- `heading` - Row heading
- `link` - Row link URL
- `image_ids` - JSON array of attachment IDs
- `row_order` - Display order
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## Security

The plugin includes:
- WordPress nonce verification for all AJAX requests
- Proper capability checks (`manage_options`)
- Input sanitization and output escaping
- SQL injection prevention using prepared statements
- XSS protection through proper escaping

## Troubleshooting

### Menu not appearing on page
- Verify you've pasted the correct shortcode
- Check that the menu has at least one row added
- Ensure the page has the plugin activated

### Images not uploading
- Check your WordPress upload directory permissions
- Ensure file size limits allow your image uploads
- Try a different image format

### Database tables not created
- Deactivate and reactivate the plugin
- Check WordPress database permissions
- Review your server error logs

## Version

Current Version: 1.0.0

## Support

For issues or feature requests, please contact your WordPress administrator or the plugin developer.

## License

GNU General Public License v2 or later

## Credits

Created with WordPress development best practices and security in mind.

---

**Happy Building! 🚀**
