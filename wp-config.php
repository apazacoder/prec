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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dbpreciodelaceite' );

/** MySQL database username */
define( 'DB_USER', 'uswpsandbox' );

/** MySQL database password */
define( 'DB_PASSWORD', 'KaeHysUG' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'jEk&hMd@v<5OpN[N-WN`;b*9b{XM:y|k5IE-@aHEy)i:PDxB3z-9[@BkY{p8n~o*');
define('SECURE_AUTH_KEY',  '6[?W,|I5a]/YpMW|+y5}a[Ee8|hRAXZl-:E*X+q=8yG I?19ufERnOkQ?Gc*owP.');
define('LOGGED_IN_KEY',    ')L-sDy[%){>S^50s01HFBCi XUO_Vu]/`TUI?9ZVUw$Tw*1e!S--^lDEa}+7$|Kr');
define('NONCE_KEY',        'KAy@A0w{U/g)+bdl|A<Ya.zD%c-IL##*Q>4OFTEXUEwnw)M>esf*O[nd>h;[hM_W');
define('AUTH_SALT',        'C[r1>_Wg9s~#QvVQg(66llP^@[;_(=3cNg`^]&R*Mciyzpmsme[Zm^qzU$dE[lxa');
define('SECURE_AUTH_SALT', 'h;|SzQC/?v0a6*Sn,0XqK9t,)L(%`>H_8|CU+/+_4z+<f_rGx+QEPhErPH+X4AEe');
define('LOGGED_IN_SALT',   '3ZGod)86G+F.LB*?t+-_ 4Ky;#F$lZ[JZ).K#v- );]|au2UteqxwE?cd~_Bbd7e');
define('NONCE_SALT',       '+ZuJ]F{!Wc`YM[jMt~LN>!oEpt*nPJn5Wh<&2w}KgGS/o=&LP<g|JS2ZV);[a5uD');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
