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
define( 'DB_NAME', 'vineet57_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost:3308' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'lQ3OZ17&U@CVp{;6C/LqQ<Z`yL]}^aZsW</X;6VVCjpR`;SG?Ycp# ^z8[+jaeuV' );
define( 'SECURE_AUTH_KEY',  '*l_/;+W#;aGc*.!.;8&TF%8mzsZ8<J|(S&X/^%~B4mm{2wUxHIKp*P@SA+]Hc0_K' );
define( 'LOGGED_IN_KEY',    '3$^xU}9],V[H}8!|98B!lQG+.%]e<^^*Z8O>iwi@b5xN_VPU1av3z)beC]1s_W1W' );
define( 'NONCE_KEY',        'x&3:av$7 `qeC,fXF-xc.V22wi4O/FQ-x5S6X.gT326|$:.J1OU]y?FJom4k;sX{' );
define( 'AUTH_SALT',        '^{Mr+]d0RS=n9:uE1ooH;cJT*6igfhgLPA8VJ,QRlcxa4GC`1m4vk^l>be!&+YZ?' );
define( 'SECURE_AUTH_SALT', '!B5Bt8%De4[/0z(S_UnVF.L(DZX`>^<(LD5I6m1Q.nl.nC]#)b]p>d!*Vb9?+{K]' );
define( 'LOGGED_IN_SALT',   'a(Md1+bnxy7S#RQsRuphXwOz/BV{CCB{>s_8in%dx_Ob(:eGZoFEjg7<#_CES<iu' );
define( 'NONCE_SALT',       'omV4*,~:o-[n>+WLmTH6Av-LR0;xnJ{kGuZw}_|nZYdcU-> 2`0KW]P8e-X4Z+v4' );

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
