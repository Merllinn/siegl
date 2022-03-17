<?php
//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'potesilcom10' );

/** MySQL database username */
define( 'DB_USER', 'potesilcom011' );

/** MySQL database password */
define( 'DB_PASSWORD', '6Uy90JMo' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY', 'ca278f9f2ebeb6a70c33d44116542b8296fdbced2e8dd9122f1d8b36ce38');
define( 'SECURE_AUTH_KEY', 'ed207d5794d8b45805bbaa540e3c5d81b8bb861891311f2187b45106b00e');
define( 'LOGGED_IN_KEY', 'bb38f6f9bfb919f9030d7827a632684f44359b30868934a06dd20de3a32d');
define( 'NONCE_KEY', '9c49ab86b9e9cfca0f22fb8d310335f2d0de0ae739ed695b9b1202a4841d');
define( 'AUTH_SALT', '1b70661727b2f92455156c141146c5c6300404d2d8c3b9ce20ff78f704b3');
define( 'SECURE_AUTH_SALT', '44120898bbb46a86c8354c8d9145bab62e10cebfdf14a1ca321f4e4d213f');
define( 'LOGGED_IN_SALT', '716df7330b788add82af74496309ee7661f8e9f90011870fa3e3bf7fcd96');
define( 'NONCE_SALT', 'adc1cc368ffb19be659ce32f7f14cc054636130b2eb5b2730ea1b5c0e18a');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp582_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WPCF7_AUTOP', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/** Automatic updates */
define('WP_AUTO_UPDATE_CORE', true);
add_filter('auto_update_plugin', '__return_true');
add_filter('auto_update_theme', '__return_true');

/** Disable wp-cron.php to replace it with other cron */
define('DISABLE_WP_CRON', true);
