# Mega Menu Plugin - Installation Guide

## Prerequisites

Before installing the Mega Menu plugin, ensure you have:

- ✅ WordPress 5.0 or higher
- ✅ PHP 7.2 or higher
- ✅ MySQL 5.6 or higher (or compatible database)
- ✅ Administrator access to WordPress dashboard
- ✅ FTP/File Manager access (if manually installing) or Plugin Upload capability

## Installation Methods

### Method 1: Upload via WordPress Admin (Recommended)

1. **Prepare the plugin folder**
   - Ensure you have the complete `mega-menu` folder with all files

2. **Create a ZIP file**
   - Zip the entire `mega-menu` folder
   - The ZIP should contain:
     ```
     mega-menu/
     ├── mega-menu.php
     ├── README.md
     ├── admin/
     ├── includes/
     └── public/
     ```

3. **Upload via WordPress Dashboard**
   - Log in to your WordPress Admin Dashboard
   - Go to: **Plugins** → **Add New**
   - Click the **Upload Plugin** button
   - Click **Choose File** and select your `mega-menu.zip`
   - Click **Install Now**
   - Click **Activate Plugin**

4. **Verify Installation**
   - You should see "Mega Menu" in the left admin menu
   - Check: **Plugins** → **Installed Plugins** → You should see "Mega Menu" listed

### Method 2: Manual Installation via FTP

1. **Extract the plugin folder**
   - Download and extract the `mega-menu` folder to your computer

2. **Connect via FTP**
   - Use an FTP client (FileZilla, Cyberduck, etc.)
   - Connect to your web hosting server with FTP credentials

3. **Upload files**
   - Navigate to: `/wp-content/plugins/`
   - Upload the entire `mega-menu` folder here

4. **Activate in WordPress**
   - Go to WordPress Dashboard
   - Go to: **Plugins** → **Installed Plugins**
   - Find "Mega Menu" and click **Activate**

### Method 3: Direct File Transfer

If using file manager in your hosting control panel (cPanel, Plesk, etc.):

1. **Access File Manager**
   - Log in to your hosting control panel
   - Go to File Manager
   - Navigate to: `public_html/wp-content/plugins/`

2. **Upload plugin folder**
   - Upload the `mega-menu` folder here
   - Make sure folder permissions are `755` and file permissions are `644`

3. **Activate plugin**
   - Go to WordPress Dashboard
   - Go to: **Plugins** → **Installed Plugins**
   - Find "Mega Menu" and click **Activate**

## Post-Installation Steps

### 1. Verify Database Tables
The plugin automatically creates database tables when activated. To verify:

1. Go to **WordPress Dashboard**
2. Check the left menu for **Mega Menu** option
3. Click **Mega Menu** to access the plugin interface

### 2. Check Permissions
Ensure your WordPress directory has correct permissions:

```
/wp-content/plugins/mega-menu/     - 755
/wp-content/plugins/mega-menu/admin/  - 755
/wp-content/plugins/mega-menu/includes/ - 755
/wp-content/plugins/mega-menu/public/ - 755
/wp-content/uploads/               - 755
```

### 3. Verify Media Library Access
The plugin uses WordPress media library for image uploads:

1. Go to **Media** from the dashboard
2. Try uploading a test image
3. If successful, media library is working correctly

## Troubleshooting Installation Issues

### Issue: Plugin doesn't appear in Plugins list

**Solution:**
- Verify the `mega-menu` folder is in `/wp-content/plugins/`
- Check that `mega-menu.php` file exists in the folder root
- Ensure file and folder permissions are correct (755/644)
- Try deactivating and reactivating the plugin

### Issue: "Fatal error: Cannot redeclare class"

**Solution:**
- Check if there's an older version of the plugin installed
- Delete the old plugin folder completely
- Upload the new plugin folder
- Activate the plugin

### Issue: Database tables not created

**Solution:**
- Check your database connection works (try creating a post)
- Ensure your database user has CREATE TABLE permission
- Deactivate and reactivate the plugin
- Check your hosting provider's error logs

### Issue: Admin page shows blank or errors

**Solution:**
- Check browser console (F12) for JavaScript errors
- Verify jQuery is loaded (should be by default in WordPress)
- Clear browser cache (Ctrl+Shift+Delete)
- Try a different browser
- Check PHP error logs on your server

### Issue: Images won't upload

**Solution:**
- Check WordPress upload directory permissions (should be 755)
- Verify upload directory exists: `/wp-content/uploads/`
- Check file upload size limits in PHP (php.ini)
- Try uploading from Media Library directly first
- Check your hosting provider's file upload limits

### Issue: Shortcode not displaying

**Solution:**
- Verify you copied the complete shortcode correctly
- Check that the menu has at least one item added
- Ensure the page/post where you pasted it is published
- Try adding shortcode to a post first to test
- Check browser console for JavaScript errors

## System Requirements Check

To verify your server meets requirements, add this to a test file:

```php
<?php
echo "PHP Version: " . phpversion() . "<br>";
echo "WordPress Version: " . get_bloginfo('version') . "<br>";
echo "MySQL Version: " . mysql_get_server_info() . "<br>";
echo "Memory Limit: " . WP_MEMORY_LIMIT . "<br>";
echo "Max Upload Size: " . ini_get('upload_max_filesize') . "<br>";
?>
```

## Recommended Configuration

For optimal performance:

- **PHP Version**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **WordPress**: 5.8 or higher
- **PHP Memory Limit**: 64MB or higher
- **Max Upload Size**: 10MB or higher
- **Upload Directory Permissions**: 755

## Uninstallation

### To deactivate the plugin:
1. Go to **Plugins** → **Installed Plugins**
2. Find "Mega Menu" and click **Deactivate**

### To completely remove the plugin:
1. Deactivate the plugin (see above)
2. Go to **Plugins** → **Installed Plugins**
3. Find "Mega Menu" and click **Delete**
4. Or delete the `/wp-content/plugins/mega-menu/` folder via FTP

### Data Preservation Note:
- Deactivating keeps your data in the database
- Deleting the plugin folder also keeps the database tables
- To completely remove all data, you need to manually delete database tables:
  - `wp_mega_menus`
  - `wp_mega_menu_items`

## Getting Help

If you encounter issues:

1. **Check the [README.md](README.md)** for general documentation
2. **Check the [QUICKSTART.md](QUICKSTART.md)** for quick reference
3. **Check the [DEVELOPER.md](DEVELOPER.md)** for technical details
4. **Review your server error logs** for specific error messages
5. **Contact your hosting provider** if you have server/permission issues

## Next Steps

After successful installation:

1. Read the [QUICKSTART.md](QUICKSTART.md) to get started
2. Create your first menu from the Mega Menu dashboard
3. Add menu items with images
4. Copy and use the shortcode on your pages

---

**Installation Complete! 🎉**

You're ready to create awesome mega menus!
