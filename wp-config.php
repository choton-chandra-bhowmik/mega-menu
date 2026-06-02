<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mega-menu-plugin' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'pBLW$_@`iKvA:P|o,fH6wPkIqV`tH2>]#ou6c$1wQw6wZK~$;b_E^m/]}Ruz8aF_' );
define( 'SECURE_AUTH_KEY',  'Bn:m[:5LW} N(00MwQ9*iuh7>,;XoFj7ac|i<k =<{m^`qV+FD6$mi/AFOF5kEEr' );
define( 'LOGGED_IN_KEY',    '$XA@jU#INX$ HXIUy:r;:)Yd(T?#nuqflq8Wyk(#_f_E5XJFHrI>$I=@;VfH?uP)' );
define( 'NONCE_KEY',        'w8!kf LElN?$jq&#mTu3E8Ehf[]8T#p:Ro2WLmR5>e3NCyHU=Loq.o[ sT&vDKa&' );
define( 'AUTH_SALT',        's+Rx>n_~GI4=Y&}ND93Y ?I8y?0f`S3tl=S|m5G<4S_&F/7)j(RiA-SDs|d@2Z:O' );
define( 'SECURE_AUTH_SALT', 'LT%E,,IQyRm9#?kMriB$/VR,h_sFJ>r^>kK)?7^F32;i4@8mkj]U,Gtb=YC-y7R$' );
define( 'LOGGED_IN_SALT',   ']Tgd/zz2a$z(oG8ZMAk5*=P>GAz*~FC{HU?;/[RDJS|T|G-iAPA|,xt+m5zoI=G7' );
define( 'NONCE_SALT',       'DI&Xs9yv1N4dL3[L#i($uj:bV%}#A7FO8d)Vjm?FXqk2%YGvz(?WUSDMqTF&lg<X' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
