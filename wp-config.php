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
define( 'DB_NAME', 'website_ban_mo_hinh' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
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
define( 'AUTH_KEY',         'ah~6Q^nPF~MFKDd-@66.oO#,+oLcxK+5NOQuoW*!:.4z9J>4]Qn7&Z?^jhuaZH;!' );
define( 'SECURE_AUTH_KEY',  'bVQ:iK![zn.6A-[>!Nk$*pG6DN:^tJe_,!rAsoW#H<k?u7S|_dv=W-+&EWs<A6zS' );
define( 'LOGGED_IN_KEY',    'w2U^]HO!oMvklcbI&G&~m%Fne~}v<6g~^/zwz.Ev^[Ea:dAfZ).<g8Ds=Z0g]3xm' );
define( 'NONCE_KEY',        'zI*5x!#amF #izb}}y<y#EvI9x@oZl=/sZ^/G%$;H_e*n{}Q53_8qGP4!l[>_f66' );
define( 'AUTH_SALT',        'LY6@9;) y7eq2R2q.s@=fG,a/Z[Gb~$xGGjN8=Gm,?1l4Bd&WkrHE>a,EB5O<rp]' );
define( 'SECURE_AUTH_SALT', '$&zelA!L%3+g}o&n;kMzOR[esW}#4hRMAQ_SKz66iYqN0=NPWpQ`?%0B> 5@5FJ6' );
define( 'LOGGED_IN_SALT',   'rKG}b/^d0[uKv:i;)4F+@bPDGZd?7Nw4A&cz|CBNv1|VIcUY}6I0$C;fpD#2~<gM' );
define( 'NONCE_SALT',       'egau?I]s{_RmePKC+%a>^pud7V_:.8).@7<+{3M9~,%ccm_DB` rUF0nzpi2>zy;' );

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
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
