# Mega Menu Plugin - Troubleshooting Guide

## Error: "Plugin could not be activated because it triggered a fatal error"

### ✅ Fixed Issues

The plugin had two compatibility issues that have been corrected:

1. **FOREIGN KEY Constraint Issue**
   - Removed FOREIGN KEY constraint from database creation
   - Now using manual cascade deletion in PHP
   - Compatible with all MySQL versions

2. **Database Table Creation**
   - Simplified CREATE TABLE statements
   - Removed problematic constraints
   - Added better compatibility

### 🔧 What to Do Now

#### Step 1: Deactivate the Plugin (if it's still active)
1. Go to **WordPress Dashboard** → **Plugins** → **Installed Plugins**
2. If you see "Mega Menu" listed with a red error, click **Deactivate**
3. If you don't see it, skip to Step 2

#### Step 2: Re-activate the Plugin
1. Go to **Plugins** → **Installed Plugins**
2. Find "Mega Menu"
3. Click **Activate**

#### Step 3: Verify Activation
1. Check if "Mega Menu" now appears in the left dashboard menu
2. If it appears, activation was successful!
3. If not, see troubleshooting below

---

## If You Still Get an Error

### Check WordPress Error Logs

1. **Enable WordPress Debug Mode**
   - Open `wp-config.php` (in your website root)
   - Find the line: `define( 'WP_DEBUG', false );`
   - Change it to: `define( 'WP_DEBUG', true );`
   - Add these lines above it:
     ```php
     define( 'WP_DEBUG_LOG', true );
     define( 'WP_DEBUG_DISPLAY', false );
     ```

2. **Check the Debug Log**
   - The error log is located at: `/wp-content/debug.log`
   - Download this file and look for the error message
   - Share the error with us

### Common Errors & Solutions

#### Error: "Parse error: syntax error"
- **Cause**: PHP version incompatibility
- **Solution**: Check PHP version (must be 7.2+)
- **Action**: Contact your hosting provider

#### Error: "Undefined function or class"
- **Cause**: Missing PHP extension or WordPress not fully loaded
- **Solution**: Try deactivating other plugins temporarily
- **Action**: Re-activate the plugin

#### Error: "MySQL error"
- **Cause**: Database compatibility issue
- **Solution**: We've fixed this in the latest version
- **Action**: Try activating again

---

## Manual Activation Steps

If the plugin still won't activate automatically:

### Step 1: Verify File Permissions
```
mega-menu folder: 755
mega-menu files: 644
```

To change permissions via FTP:
1. Connect via FTP
2. Right-click mega-menu folder → Properties
3. Set permissions to 755
4. Do the same for all files (644)

### Step 2: Clear WordPress Cache
If you use a caching plugin:
1. **WP Super Cache**: Clear all cache
2. **W3 Total Cache**: Purge all caches
3. **Cloudflare**: Clear cache (if used)

### Step 3: Delete Browser Cache
- Press **Ctrl + Shift + Delete** (Windows) or **Cmd + Shift + Delete** (Mac)
- Select "All time"
- Click "Clear data"

### Step 4: Try Again
- Go to Plugins page
- Activate "Mega Menu"

---

## Advanced Troubleshooting

### Check PHP Compatibility

1. Create a file called `test-php.php` in your WordPress root
2. Paste this code:
   ```php
   <?php
   echo "PHP Version: " . phpversion() . "\n";
   echo "MySQL Version: " . mysqli_get_server_info() . "\n";
   echo "WordPress Version: " . get_bloginfo('version') . "\n";
   ?>
   ```
3. Access it via: `yoursite.com/test-php.php`
4. Check the versions displayed
5. Delete the file when done

### Verify Database Connection
1. WordPress can create posts ✓ = Database is working
2. Try creating a new post to verify

### Test Each Plugin File

If you're comfortable with code:
1. Temporarily comment out the class inclusions in `mega-menu.php`
2. Activate the plugin
3. Uncomment one line at a time
4. After each activation attempt, note which file causes the error

---

## If You Need Help

### What Information to Provide

1. **PHP Version**: Your hosting control panel shows this
2. **WordPress Version**: Dashboard → Updates page
3. **MySQL Version**: Check in hosting control panel
4. **Error Message**: From WordPress error log
5. **Other Plugins**: List of active plugins

### Where to Get Help

1. Check [INSTALLATION.md](INSTALLATION.md) for setup issues
2. Review [README.md](README.md) for feature questions
3. See [DEVELOPER.md](DEVELOPER.md) for code questions

---

## Quick Fixes Checklist

- [ ] PHP version is 7.2 or higher
- [ ] MySQL version is 5.6 or higher
- [ ] File permissions are correct (755/644)
- [ ] Plugin folder is in: `/wp-content/plugins/mega-menu/`
- [ ] All plugin files are present
- [ ] WordPress cache is cleared
- [ ] Browser cache is cleared
- [ ] No conflicting plugins installed

---

## Prevention Tips

- ✅ Keep WordPress updated
- ✅ Keep PHP version current
- ✅ Test with a few plugins at a time
- ✅ Use staging server for testing
- ✅ Backup database before installing plugins

---

## Success!

If you see **"Mega Menu"** in your left dashboard menu, the plugin is activated successfully!

Next steps:
1. Read [QUICKSTART.md](QUICKSTART.md)
2. Create your first mega menu
3. Add menu items
4. Use the shortcode on your pages

---

**Still having issues?** 
Make sure you're using the latest version of the plugin files and that all have been uploaded correctly.
