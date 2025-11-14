<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

/**
 * Database connection information is automatically provided.
 * There is no need to set or change the following database configuration
 * values:
 *   DB_HOST
 *   DB_NAME
 *   DB_USER
 *   DB_PASSWORD
 *   DB_CHARSET
 *   DB_COLLATE
 */

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         '-L@vf^m|mLZj>7+y3F8-En<2^XpG*}:03y_hL)n3<u52ygD^fcQ%TBc{,Q!FLBdX');
define('SECURE_AUTH_KEY',  'RwJ1w@W#QJ*|^5(p2~|bzjp@gsQgSZgl#0Mb5#McA$7yPM-OBP3!sa|X;NnJvVQ.');
define('LOGGED_IN_KEY',    'Z0[yQY)[1!I[P[1=}rqEL9o[p8y+Ka|V1>Dp}-*^v.z;EPX@^(ix^$pkuJqY[Sa(');
define('NONCE_KEY',        'uXRot+,n:TImWn:>ljsapNF_Xn=2:G#w[r2$JbJLBc[!Yj*W.sWcU8_]jj+urT$Y');
define('AUTH_SALT',        'W}DB4YZE$.4}t^J>kK$_9>PT|vKS7igsOsO04qS{u{MsbfeTI*fMH06)A(iYk$|H');
define('SECURE_AUTH_SALT', 'FNI_WQB]}IHeO_^GeuRxFps~GxBnY,T;7vxL4q5?!F,W}^nw1_UR+z43AE)Sp)zs');
define('LOGGED_IN_SALT',   ';d0g2GjX{<wX>+sXK0j(a:*dK5S6EBWuf5mcC#t9cDxbamTmrT]w[QO8Jv<R_ih<');
define('NONCE_SALT',       '3PPlNbq6Pp{,DF.L6=Wps9F;1.V]sI5a[yGj$p3B%cA8XPm.I?X)p6jQraIT$[WS');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
if ( ! defined( 'WP_DEBUG') ) {
	define('WP_DEBUG', false);
}

define( 'WP_ENVIRONMENT_TYPE', 'staging' );
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
