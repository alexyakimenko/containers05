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
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

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
define( 'AUTH_KEY',         'SQJ?3RoLCHX(d[#Np,1n5KN]|8L6$#sq9t ^-n?#rs`,q<!p9pWG0((ckFEJ4$F~' );
define( 'SECURE_AUTH_KEY',  ',3uEM5W{z_>Ngnu6(L,Vc8L$g:wwH9}Q>`3)k8C+C#s=SZ0/3o9d$~IA n,U,><a' );
define( 'LOGGED_IN_KEY',    'D;bvS&8>sBPEp6f50yx=$Z6E~ue9z.7T-;Fb$M$YB~-B/CydKNu`bhlp*#OE{c^D' );
define( 'NONCE_KEY',        '4WldGA$9_qZCrP}d75@(x$pX$nAxzKodd,9ok$I1~YS)iz9N$ &1@ }^JTO)>djq' );
define( 'AUTH_SALT',        'g;GOJ<UV/tB~&evPXE]b.9?M,bpL#-//$X6@6kC$S,%A<R7xh.5cz=bT.lU/^;vg' );
define( 'SECURE_AUTH_SALT', 'eP$pl;qTs]pOJ]6:G<A>j?c[>=j>D#*:~%y ,4Gx.kAKvxu}5;.tX_5Z:$).XJ?[' );
define( 'LOGGED_IN_SALT',   '+6p.mS2Yrl&(s>Zy<WBZ}lkq|t1Em3ANrr%*/1@~5m@36:WF[,P<GwgOl&Ey}WWF' );
define( 'NONCE_SALT',       '`*Je]O/#lW(sNo8X=rAE[Iw OLZ)aXNfo8{UH_0=mqe34ar,f(_:Z[N-l&i0H[b{' );

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
