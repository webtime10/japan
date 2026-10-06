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
define('FS_METHOD', 'direct');
//define('WP_TEMP_DIR', dirname(__FILE__) . '/wp-content/upgrade');
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', "japan2" );

/** Database username */
define( 'DB_USER', "root" );

/** Database password */
define( 'DB_PASSWORD', "123" );

/** Database hostname */
define( 'DB_HOST', "127.0.1.29" );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('WP_MEMORY_LIMIT', '3512M');
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
define( 'AUTH_KEY',         '@ %4=^iOa9dC0:eT|o(tGk#<b{FaFTC,X8P^3K!aS^%P;U&yTN!1xGh+g!*]#<C[' );
define( 'SECURE_AUTH_KEY',  'dHa_(j0i,Ha#|7d[7J=]lwaRi{_q?<V`XVG6SNZQd>(IXP#TU75F}:1-<Ja@>Exj' );
define( 'LOGGED_IN_KEY',    '=7)kV2^0tr5tZ9d&ZZ^< )K,Ig<+`f29=9R0$7hVhoGb98y%CtOlBqbE9L|kw@FT' );
define( 'NONCE_KEY',        'vw;)d9@ZbeGjPq)azE!s%,m:4fbp<0Lb>PgqU`D7qq98TQ2$,_z}Jz0FX%/!+Rps' );
define( 'AUTH_SALT',        '$oMm i{9_P(}M o?-@C~a%#76.HraKtZz`?uvebIZ75|tXl3Vq$^blGx)]~g!{4G' );
define( 'SECURE_AUTH_SALT', '$y<^BB[CckUj`wr7lp9LOtzI=B[*`,-:BQasid+gg%r_=FmOm8jjC_PoBI(bG#q2' );
define( 'LOGGED_IN_SALT',   '8N0FE!:MnXNgGJ?Ey:U*OQ,/H8UUDwb.z_k# O7BS|AyuL24zc$.a{>cu/e@j X;' );
define( 'NONCE_SALT',       '5,GQZ/Ymv,zRAN5~GD~.P*~*?b?=kc::H;kKY}-;Y}#,!9}_`s`J+KYsp.Fhyk$+' );

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
define( 'WP_DEBUG_LOG', true ); // Вот это включит запись в файл
/* Add any custom values between this line and the "stop editing" line. */
// Не выводить Deprecated/Notice в HTML — иначе «headers already sent» и ломается админка при PHP 8.2+.
if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
	define( 'WP_DEBUG_DISPLAY', false );
}
@ini_set( 'display_errors', '0' );

/* Add any custom values between this line and the "stop editing" line. */

define('LARAVEL_API_KEY', '123456sei091_');
define('AI_CALCULATOR_LARA_API_KEY', '123456sei091_2');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}


/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
